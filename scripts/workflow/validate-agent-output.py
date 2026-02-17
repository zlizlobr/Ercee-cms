#!/usr/bin/env python3
"""Validate workflow JSON payloads against required v1 fields."""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

ALLOWED_SEVERITIES = {"blocker", "major", "minor"}


def fail(message: str) -> int:
    print(f"ERROR: {message}")
    return 1


def validate_agent_output(payload: dict) -> int:
    required = ["scope", "files_changed", "tests_run", "risks", "next_handoff"]
    missing = [key for key in required if key not in payload]
    if missing:
        return fail(f"Missing keys: {', '.join(missing)}")

    if not isinstance(payload["scope"], str) or not payload["scope"].strip():
        return fail("'scope' must be a non-empty string")

    for key in ["files_changed", "tests_run", "risks"]:
        if not isinstance(payload[key], list):
            return fail(f"'{key}' must be an array")
        if key == "files_changed" and len(payload[key]) == 0:
            return fail("'files_changed' must not be empty")
        for index, item in enumerate(payload[key]):
            if not isinstance(item, str) or not item.strip():
                return fail(f"'{key}[{index}]' must be a non-empty string")

    if not isinstance(payload["next_handoff"], str) or not payload["next_handoff"].strip():
        return fail("'next_handoff' must be a non-empty string")

    return 0


def validate_review_findings(payload: dict) -> int:
    if "findings" not in payload:
        return fail("Missing key: findings")
    if not isinstance(payload["findings"], list):
        return fail("'findings' must be an array")

    for index, finding in enumerate(payload["findings"]):
        if not isinstance(finding, dict):
            return fail(f"'findings[{index}]' must be an object")
        for key in ["severity", "rule_id", "required_fix", "auto_fixable"]:
            if key not in finding:
                return fail(f"'findings[{index}]' missing key '{key}'")

        severity = finding["severity"]
        if severity not in ALLOWED_SEVERITIES:
            return fail(f"'findings[{index}].severity' must be one of {sorted(ALLOWED_SEVERITIES)}")

        rule_id = finding["rule_id"]
        if not isinstance(rule_id, str) or "-" not in rule_id:
            return fail(f"'findings[{index}].rule_id' must be a string like ARCH-001")

        if not isinstance(finding["required_fix"], str) or not finding["required_fix"].strip():
            return fail(f"'findings[{index}].required_fix' must be non-empty")

        if not isinstance(finding["auto_fixable"], bool):
            return fail(f"'findings[{index}].auto_fixable' must be boolean")

    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description="Validate workflow payload JSON files.")
    parser.add_argument("--type", choices=["agent-output", "review-findings"], required=True)
    parser.add_argument("--file", required=True)
    args = parser.parse_args()

    path = Path(args.file)
    if not path.exists():
        return fail(f"File not found: {path}")

    try:
        payload = json.loads(path.read_text(encoding="utf-8"))
    except json.JSONDecodeError as exc:
        return fail(f"Invalid JSON: {exc}")

    if not isinstance(payload, dict):
        return fail("Payload root must be an object")

    if args.type == "agent-output":
        result = validate_agent_output(payload)
    else:
        result = validate_review_findings(payload)

    if result == 0:
        print(f"OK: {args.type} payload is valid")

    return result


if __name__ == "__main__":
    sys.exit(main())
