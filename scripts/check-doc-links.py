#!/usr/bin/env python3
"""Check markdown links in docs/ for missing local files."""

from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DOCS_DIR = ROOT / "docs"

LINK_RE = re.compile(r"\[([^\]]+)\]\(([^)]+)\)")

# Ignore external links and anchors-only links
EXTERNAL_PREFIXES = ("http://", "https://", "mailto:")


def normalize_target(md_path: Path, target: str) -> Path | None:
    target = target.strip()

    if not target or target.startswith("#"):
        return None
    if target.startswith(EXTERNAL_PREFIXES):
        return None

    # Strip optional title after space
    if " " in target:
        target = target.split(" ", 1)[0]

    # Strip anchor for local files
    if "#" in target:
        target = target.split("#", 1)[0]

    if not target:
        return None

    # Treat root-relative (repo-relative) paths as relative to repo root.
    if target.startswith("/"):
        return (ROOT / target.lstrip("/")).resolve()

    # Relative to the markdown file location.
    return (md_path.parent / target).resolve()


def main() -> int:
    missing: list[str] = []
    skipped: list[str] = []

    for md_path in DOCS_DIR.rglob("*.md"):
        try:
            text = md_path.read_text(encoding="utf-8")
        except FileNotFoundError:
            # Some docs entries are symlinks to external skill repos and may not
            # exist in CI runners. Skip these entries instead of crashing.
            skipped.append(str(md_path.relative_to(ROOT)))
            continue

        for _, target in LINK_RE.findall(text):
            target_path = normalize_target(md_path, target)
            if target_path is None:
                continue
            if not target_path.exists():
                rel_md = md_path.relative_to(ROOT)
                missing.append(f"{rel_md}: {target}")

    if missing:
        print("Missing local doc links:\n")
        for item in missing:
            print(f"- {item}")
        return 1

    if skipped:
        print("Skipped unresolved markdown symlinks:")
        for item in skipped:
            print(f"- {item}")
        print()

    print("All local doc links in docs/ are valid.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
