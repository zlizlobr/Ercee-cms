#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
ENV_FILE="$ROOT_DIR/.linear/.env"

if [[ -f "$ENV_FILE" ]]; then
  set -a
  # shellcheck disable=SC1090
  source "$ENV_FILE"
  set +a
fi

: "${LINEAR_API_KEY:?LINEAR_API_KEY is required in .linear/.env}"

LINEAR_VERBOSE=0
if [[ "${1:-}" == "--verbose" ]]; then
  LINEAR_VERBOSE=1
fi
export LINEAR_VERBOSE
export LINEAR_ROOT_DIR="$ROOT_DIR"

python3 - <<'PY'
import json
import os
import sys
import urllib.request
import urllib.error

root_dir = os.environ.get("LINEAR_ROOT_DIR")
if not root_dir:
    print("Missing LINEAR_ROOT_DIR", file=sys.stderr)
    sys.exit(1)
tasks_path = os.path.join(root_dir, ".linear", "tasks.json")

api_key = os.environ.get("LINEAR_API_KEY")
if not api_key:
    print("Missing LINEAR_API_KEY in .linear/.env", file=sys.stderr)
    sys.exit(1)

with open(tasks_path, "r", encoding="utf-8") as f:
    data = json.load(f)

tasks = data.get("tasks", [])

def log(msg):
    if os.environ.get("LINEAR_VERBOSE") == "1":
        print(msg, file=sys.stderr)

def fetch_issue(issue_id):
    query = (
        "query IssueArchived($id: String!) {"
        "  issue(id: $id) {"
        "    id"
        "    identifier"
        "    archivedAt"
        "  }"
        "}"
    )
    payload = json.dumps({"query": query, "variables": {"id": issue_id}}).encode("utf-8")
    req = urllib.request.Request(
        "https://api.linear.app/graphql",
        data=payload,
        headers={
            "Content-Type": "application/json",
            "Authorization": api_key,
        },
        method="POST",
    )

    try:
        with urllib.request.urlopen(req) as resp:
            body = resp.read().decode("utf-8")
    except urllib.error.HTTPError as exc:
        body = exc.read().decode("utf-8") if exc.fp else ""
        raise RuntimeError(f"Issue fetch failed: HTTP {exc.code} {exc.reason} {body}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"Issue fetch failed: {exc}") from exc

    response = json.loads(body)
    if "errors" in response:
        raise RuntimeError(response["errors"])
    return response.get("data", {}).get("issue", {})

changed = False
updated = 0
skipped = 0
for task in tasks:
    issue_id = task.get("linearId")
    if not issue_id:
        skipped += 1
        continue
    log(f"[linear-pull] Fetching issue for task {task.get('id')}: {task.get('title')}")
    issue = fetch_issue(issue_id)
    if issue.get("archivedAt"):
        if task.get("state") != "archived":
            task["state"] = "archived"
            changed = True
            updated += 1
    else:
        if task.get("state") == "draft":
            task["state"] = "synced"
            changed = True
            updated += 1
    if task.get("state") != "archived" and task.get("state") != "draft":
        skipped += 1

if changed:
    with open(tasks_path, "w", encoding="utf-8") as f:
        json.dump(data, f, indent=2)
        f.write("\n")
log(f"[linear-pull] Done. Updated: {updated}, Skipped: {skipped}")
PY
