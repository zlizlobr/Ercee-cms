#!/usr/bin/env python3
"""Collect basic workflow telemetry from artifacts/gates."""

from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
BASE = ROOT / "artifacts" / "gates"
OUT = ROOT / "artifacts" / "workflow-metrics.json"


def main() -> int:
    initiatives = [p for p in BASE.iterdir() if p.is_dir()]
    completed_release = 0

    for initiative in initiatives:
        if (initiative / "release-readiness").exists():
            completed_release += 1

    metrics = {
        "initiatives_total": len(initiatives),
        "release_readiness_artifacts_present": completed_release,
        "release_readiness_ratio": (completed_release / len(initiatives)) if initiatives else 0.0,
    }

    OUT.write_text(json.dumps(metrics, indent=2) + "\n", encoding="utf-8")
    print(f"Wrote metrics to {OUT.relative_to(ROOT)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
