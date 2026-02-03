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
        "    title"
        "    completedAt"
        "    archivedAt"
        "    parent { id }"
        "    team { id }"
        "    state { id type }"
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

def fetch_done_state_id(team_id):
    query = (
        "query TeamStates($id: String!) {"
        "  team(id: $id) {"
        "    states { nodes { id type name } }"
        "  }"
        "}"
    )
    payload = json.dumps({"query": query, "variables": {"id": team_id}}).encode("utf-8")
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
        raise RuntimeError(f"Team states fetch failed: HTTP {exc.code} {exc.reason} {body}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"Team states fetch failed: {exc}") from exc

    response = json.loads(body)
    if "errors" in response:
        raise RuntimeError(response["errors"])

    team = response.get("data", {}).get("team", {})
    for node in team.get("states", {}).get("nodes", []):
        if node.get("type") == "completed":
            return node.get("id")
    return None

def update_issue_state(issue_id, state_id):
    query = (
        "mutation IssueUpdate($id: String!, $input: IssueUpdateInput!) {"
        "  issueUpdate(id: $id, input: $input) {"
        "    success"
        "  }"
        "}"
    )
    payload = json.dumps(
        {"query": query, "variables": {"id": issue_id, "input": {"stateId": state_id}}}
    ).encode("utf-8")
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
        raise RuntimeError(f"Issue update failed: HTTP {exc.code} {exc.reason} {body}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"Issue update failed: {exc}") from exc

    response = json.loads(body)
    if "errors" in response:
        raise RuntimeError(response["errors"])
    payload_data = response.get("data", {}).get("issueUpdate")
    if not payload_data or not payload_data.get("success"):
        raise RuntimeError("issueUpdate failed")
    return True

changed = False
updated = 0
skipped = 0
done_state_id = None
for task in tasks:
    issue_id = task.get("linearId")
    if not issue_id:
        skipped += 1
        continue
    log(f"[linear-pull] Fetching issue for task {task.get('id')}: {task.get('title')}")
    issue = fetch_issue(issue_id)
    identifier = issue.get("identifier")
    title = issue.get("title") or ""
    parent = issue.get("parent") or {}
    parent_id = parent.get("id")
    if parent_id and task.get("parentLinearId") != parent_id:
        task["parentLinearId"] = parent_id
        changed = True
        updated += 1
    team_id = (issue.get("team") or {}).get("id")
    state_id = (issue.get("state") or {}).get("id")
    if state_id and task.get("workflowStateId") != state_id:
        task["workflowStateId"] = state_id
        changed = True
        updated += 1
    if done_state_id is None and team_id:
        done_state_id = fetch_done_state_id(team_id)
    if identifier and not task.get("branchName"):
        slug = "".join(ch.lower() if ch.isalnum() else "-" for ch in title)
        slug = "-".join(filter(None, slug.split("-")))
        if slug:
            task["branchName"] = f"feature/{identifier}-{slug}"
        else:
            task["branchName"] = f"feature/{identifier}"
        changed = True
        updated += 1
    if issue.get("archivedAt") or issue.get("completedAt"):
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

# Cascade archive to children when parent is archived + mark child done in Linear
archived_ids = {t.get("id") for t in tasks if t.get("state") == "archived" and t.get("id")}
archived_linear_ids = {
    t.get("linearId") for t in tasks if t.get("state") == "archived" and t.get("linearId")
}
for task in tasks:
    if task.get("state") == "archived":
        continue
    if task.get("parentId") in archived_ids or task.get("parentLinearId") in archived_linear_ids:
        log(f"[linear-pull] Archiving child due to parent archived: {task.get('id')}")
        if done_state_id and task.get("linearId"):
            log(f"[linear-pull] Marking child done in Linear: {task.get('id')}")
            update_issue_state(task["linearId"], done_state_id)
            task["workflowStateId"] = done_state_id
        task["state"] = "archived"
        changed = True
        updated += 1

if changed:
    with open(tasks_path, "w", encoding="utf-8") as f:
        json.dump(data, f, indent=2)
        f.write("\n")
log(f"[linear-pull] Done. Updated: {updated}, Skipped: {skipped}")
PY
