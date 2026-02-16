#!/usr/bin/env python3
"""Lightweight documentation standards checks for markdown files in docs/."""

from __future__ import annotations

import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
DOCS = ROOT / "docs"


def main() -> int:
    errors: list[str] = []
    skipped: list[str] = []

    for path in DOCS.rglob("*.md"):
        try:
            text = path.read_text(encoding="utf-8").splitlines()
        except FileNotFoundError:
            # Some markdown files are symlinks to centralized external skills
            # and may be unresolved on CI runners. Skip these files.
            skipped.append(str(path.relative_to(ROOT)))
            continue

        if not text:
            errors.append(f"{path.relative_to(ROOT)}: empty file")
            continue

        if not text[0].startswith("# "):
            errors.append(f"{path.relative_to(ROOT)}: first line must be '# Title'")

        intro_lines = [line for line in text[1:6] if line.strip()]
        if len(intro_lines) == 0:
            errors.append(f"{path.relative_to(ROOT)}: missing short intro under title")

    if errors:
        print("Documentation standards violations:\n")
        for error in errors:
            print(f"- {error}")
        return 1

    if skipped:
        print("Skipped unresolved markdown symlinks:")
        for item in skipped:
            print(f"- {item}")
        print()

    print("All docs files passed lightweight standards checks.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
