#!/usr/bin/env python3
"""Generate initiative + gate subtasks skeleton for .linear/data/tasks.json."""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import uuid
from datetime import date
from pathlib import Path

GATES = [
    "Spec/Plan",
    "Implementace",
    "Test Gate",
    "Ralph Review Gate (agent-only)",
    "Docs Gate",
    "Release Readiness",
]


def slugify(value: str) -> str:
    value = value.lower().strip()
    value = re.sub(r"[^a-z0-9]+", "-", value)
    return value.strip("-") or "initiative"


def new_task(title: str, description: str, parent_id: str | None = None) -> dict:
    task = {
        "id": f"local-{uuid.uuid4().hex[:8]}",
        "title": title,
        "description": description,
        "priority": "high",
        "state": "draft",
        "linearId": None,
        "createdAt": str(date.today()),
        "labels": ["llm-codex"],
        "branchName": f"feature/{slugify(title)}",
    }
    if parent_id:
        task["parentId"] = parent_id
    return task


def main() -> int:
    parser = argparse.ArgumentParser(description="Append initiative skeleton to tasks.json")
    parser.add_argument("--title", required=True)
    parser.add_argument("--description", required=True)
    parser.add_argument("--file", default=".linear/data/tasks.json")
    parser.add_argument("--sync", action="store_true", help="Run .linear/scripts/sync.sh after write")
    parser.add_argument("--pull", action="store_true", help="Run .linear/scripts/pull.sh after write")
    args = parser.parse_args()

    path = Path(args.file)
    payload = {"tasks": []}

    if path.exists():
        payload = json.loads(path.read_text(encoding="utf-8"))

    parent = new_task(args.title, args.description)
    payload["tasks"].append(parent)

    for gate in GATES:
        payload["tasks"].append(
            new_task(
                title=f"{args.title} - {gate}",
                description=f"Workflow gate subtask: {gate}",
                parent_id=parent["id"],
            )
        )

    path.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"Updated {path}")

    root = Path.cwd()
    if args.sync:
        sync_script = root / ".linear" / "scripts" / "sync.sh"
        if not sync_script.exists():
            raise SystemExit(f"Missing script: {sync_script}")
        subprocess.run([str(sync_script)], check=True)
    if args.pull:
        pull_script = root / ".linear" / "scripts" / "pull.sh"
        if not pull_script.exists():
            raise SystemExit(f"Missing script: {pull_script}")
        subprocess.run([str(pull_script)], check=True)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
