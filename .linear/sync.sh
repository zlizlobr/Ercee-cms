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
: "${LINEAR_TEAM_ID:?LINEAR_TEAM_ID is required in .linear/.env}"

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
team_id = os.environ.get("LINEAR_TEAM_ID")

if not api_key or not team_id:
    print("Missing LINEAR_API_KEY or LINEAR_TEAM_ID in .linear/.env", file=sys.stderr)
    sys.exit(1)

with open(tasks_path, "r", encoding="utf-8") as f:
    data = json.load(f)

tasks = data.get("tasks", [])

def log(msg):
    if os.environ.get("LINEAR_VERBOSE") == "1":
        print(msg, file=sys.stderr)

def fetch_all_labels():
    labels = {}
    cursor = None
    page = 0
    while True:
        page += 1
        log(f"[linear-sync] Fetching labels page {page}...")
        query = (
            "query IssueLabels($after: String) {"
            "  issueLabels(first: 250, after: $after) {"
            "    nodes { id name }"
            "    pageInfo { hasNextPage endCursor }"
            "  }"
            "}"
        )
        payload = json.dumps({"query": query, "variables": {"after": cursor}}).encode("utf-8")
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
            raise RuntimeError(f"Label fetch failed: HTTP {exc.code} {exc.reason} {body}") from exc
        except urllib.error.URLError as exc:
            raise RuntimeError(f"Label fetch failed: {exc}") from exc

        response = json.loads(body)
        if "errors" in response:
            raise RuntimeError(response["errors"])

        label_data = response.get("data", {}).get("issueLabels", {})
        for node in label_data.get("nodes", []):
            name = node.get("name")
            label_id = node.get("id")
            if name and label_id:
                labels[name] = label_id

        page_info = label_data.get("pageInfo", {})
        if not page_info.get("hasNextPage"):
            break
        cursor = page_info.get("endCursor")
        if not cursor:
            break
    log(f"[linear-sync] Labels fetched: {len(labels)}")
    return labels

def create_issue(task, label_map):
    input_payload = {
        "title": task["title"],
        "description": task["description"],
        "teamId": team_id,
    }

    labels = task.get("labels")
    if labels:
        missing = [name for name in labels if name not in label_map]
        if missing:
            log(f"[linear-sync] Unknown labels ignored: {', '.join(missing)}")
        resolved = [label_map[name] for name in labels if name in label_map]
        if resolved:
            input_payload["labelIds"] = resolved

    query = (
        "mutation IssueCreate($input: IssueCreateInput!) {"
        "  issueCreate(input: $input) {"
        "    success"
        "    issue { id identifier }"
        "  }"
        "}"
    )

    payload = json.dumps({"query": query, "variables": {"input": input_payload}}).encode("utf-8")
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
        raise RuntimeError(f"Issue create failed: HTTP {exc.code} {exc.reason} {body}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"Issue create failed: {exc}") from exc

    response = json.loads(body)
    if "errors" in response:
        raise RuntimeError(response["errors"])

    payload_data = response.get("data", {}).get("issueCreate")
    if not payload_data or not payload_data.get("success"):
        raise RuntimeError("issueCreate failed")

    issue = payload_data.get("issue") or {}
    return issue.get("id")

label_map = fetch_all_labels()
changed = False
created = 0
skipped = 0
for task in tasks:
    if task.get("state") != "draft":
        skipped += 1
        continue
    if task.get("linearId"):
        skipped += 1
        continue
    log(f"[linear-sync] Creating issue for task {task.get('id')}: {task.get('title')}")
    issue_id = create_issue(task, label_map)
    if issue_id:
        task["linearId"] = issue_id
        task["state"] = "synced"
        changed = True
        created += 1

if changed:
    with open(tasks_path, "w", encoding="utf-8") as f:
        json.dump(data, f, indent=2)
        f.write("\n")
log(f"[linear-sync] Done. Created: {created}, Skipped: {skipped}")
PY
