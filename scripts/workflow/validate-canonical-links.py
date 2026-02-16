#!/usr/bin/env python3
"""Validate canonical internal documentation links."""

from __future__ import annotations

import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
DOCS = ROOT / "docs"

LINK_RE = re.compile(r"\[[^\]]+\]\(([^)]+)\)")
CANONICAL_BLOCKLIST = {
    "docs/reference/api/authentication.md": "docs/api/authentication.md",
    "docs/reference/api/conventions.md": "docs/api/conventions.md",
    "docs/reference/api/errors.md": "docs/api/errors.md",
    "docs/local-backend-setup.md": "docs/guides/setup/local-backend-setup.md"
}


def main() -> int:
    violations: list[str] = []

    for md in DOCS.rglob("*.md"):
        text = md.read_text(encoding="utf-8")
        for target in LINK_RE.findall(text):
            clean = target.split("#", 1)[0].strip()
            if clean in CANONICAL_BLOCKLIST:
                rel = md.relative_to(ROOT)
                replacement = CANONICAL_BLOCKLIST[clean]
                violations.append(f"{rel}: use '{replacement}' instead of '{clean}'")

    if violations:
        print("Canonical link violations:\n")
        for violation in violations:
            print(f"- {violation}")
        return 1

    print("Canonical link validation passed.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
