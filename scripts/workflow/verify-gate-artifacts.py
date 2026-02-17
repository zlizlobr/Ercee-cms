#!/usr/bin/env python3
"""Verify required gate artifact structure for an initiative."""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
BASE = ROOT / "artifacts" / "gates"

REQUIRED_GATES = [
    "spec-plan",
    "implementace",
    "test-gate",
    "ralph-review",
    "docs-gate",
    "release-readiness",
]


def main() -> int:
    parser = argparse.ArgumentParser(description="Verify gate evidence directories.")
    parser.add_argument("--initiative", required=True, help="Initiative/task identifier")
    args = parser.parse_args()

    initiative_dir = BASE / args.initiative
    if not initiative_dir.exists():
        print(f"ERROR: missing initiative directory: {initiative_dir.relative_to(ROOT)}")
        return 1

    missing: list[str] = []
    for gate in REQUIRED_GATES:
        gate_dir = initiative_dir / gate
        if not gate_dir.exists():
            missing.append(gate)

    if missing:
        print("ERROR: missing gate artifact directories:")
        for gate in missing:
            print(f"- {gate}")
        return 1

    print(f"OK: all required gate artifact directories exist for '{args.initiative}'")
    return 0


if __name__ == "__main__":
    sys.exit(main())
