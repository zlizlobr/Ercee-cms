#!/usr/bin/env python3
"""Gate 6 checks: release readiness evidence completeness."""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]

REQUIRED_SECTIONS = [
    "## Change Impact",
    "## Risks",
    "## Rollback or Mitigation",
]


def main() -> int:
    parser = argparse.ArgumentParser(description="Enforce Gate 6 release readiness evidence")
    parser.add_argument("--file", required=True, help="Path to release-readiness summary markdown file")
    args = parser.parse_args()

    path = ROOT / args.file
    if not path.exists():
        print(f"ERROR: release-readiness evidence file missing: {args.file}")
        return 1

    text = path.read_text(encoding="utf-8", errors="ignore")
    missing = [section for section in REQUIRED_SECTIONS if section not in text]

    if missing:
        print("ERROR: missing required release-readiness sections:")
        for section in missing:
            print(f"- {section}")
        return 1

    print("OK: Gate 6 release-readiness evidence passed")
    return 0


if __name__ == "__main__":
    sys.exit(main())
