#!/usr/bin/env python3
"""Enforce A -> B -> C ordering in test gate sequence evidence."""

from __future__ import annotations

import argparse
import sys
from pathlib import Path


STAGES = ["A", "B", "C"]
ALLOWED_RESULTS = {"pass", "fail", "skipped"}


def main() -> int:
    parser = argparse.ArgumentParser(description="Validate test gate sequence evidence")
    parser.add_argument("--file", required=True)
    parser.add_argument("--critical", action="store_true", help="Require C stage for critical changes")
    args = parser.parse_args()

    path = Path(args.file)
    if not path.exists():
        print(f"ERROR: file not found: {path}")
        return 1

    seen: list[str] = []
    results: dict[str, str] = {}

    for raw in path.read_text(encoding="utf-8").splitlines():
        line = raw.strip()
        if not line or ":" not in line:
            continue
        stage, result = [part.strip() for part in line.split(":", 1)]
        if stage in STAGES:
            if result not in ALLOWED_RESULTS:
                print(f"ERROR: invalid result for {stage}: {result}")
                return 1
            seen.append(stage)
            results[stage] = result

    if seen[:2] != ["A", "B"]:
        print("ERROR: stages A and B must run first in order")
        return 1

    if args.critical and "C" not in results:
        print("ERROR: stage C is required for critical changes")
        return 1

    if "C" in results and seen != ["A", "B", "C"]:
        print("ERROR: stage C must run after A and B")
        return 1

    print("OK: test gate sequence is valid")
    return 0


if __name__ == "__main__":
    sys.exit(main())
