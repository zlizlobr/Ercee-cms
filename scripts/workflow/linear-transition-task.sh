#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(pwd -P)"
ENV_FILE="$ROOT_DIR/.linear/config/.env"

if [[ -f "$ENV_FILE" ]]; then
  set -a
  # shellcheck disable=SC1090
  source "$ENV_FILE"
  set +a
fi

: "${LINEAR_API_KEY:?LINEAR_API_KEY is required in .linear/config/.env}"
: "${LINEAR_TEAM_ID:?LINEAR_TEAM_ID is required in .linear/config/.env}"

LINEAR_ID=""
TASK_ID=""
STATE_NAME="Done"
DO_PULL=0
VERBOSE=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --linear-id)
      LINEAR_ID="${2:-}"
      shift 2
      ;;
    --task-id)
      TASK_ID="${2:-}"
      shift 2
      ;;
    --state-name)
      STATE_NAME="${2:-}"
      shift 2
      ;;
    --pull)
      DO_PULL=1
      shift
      ;;
    --verbose)
      VERBOSE=1
      shift
      ;;
    *)
      echo "Unknown argument: $1" >&2
      exit 1
      ;;
  esac
done

if [[ -z "$LINEAR_ID" && -z "$TASK_ID" ]]; then
  echo "Use --linear-id <id> or --task-id <local-id>" >&2
  exit 1
fi

export LINEAR_ROOT_DIR="$ROOT_DIR"
export LINEAR_TEAM_ID
export LINEAR_API_KEY
export LINEAR_ID
export TASK_ID
export STATE_NAME
export LINEAR_VERBOSE="$VERBOSE"

python3 - <<'PY'
import json
import os
import sys
import urllib.request
import urllib.error

root_dir = os.environ["LINEAR_ROOT_DIR"]
api_key = os.environ["LINEAR_API_KEY"]
team_id = os.environ["LINEAR_TEAM_ID"]
linear_id = os.environ.get("LINEAR_ID", "").strip()
task_id = os.environ.get("TASK_ID", "").strip()
state_name = os.environ.get("STATE_NAME", "Done").strip()
verbose = os.environ.get("LINEAR_VERBOSE") == "1"

tasks_path = os.path.join(root_dir, ".linear", "data", "tasks.json")


def log(msg: str) -> None:
    if verbose:
        print(msg, file=sys.stderr)


def gql(query: str, variables: dict) -> dict:
    payload = json.dumps({"query": query, "variables": variables}).encode("utf-8")
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
        raise RuntimeError(f"Linear API HTTP {exc.code} {exc.reason}: {body}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"Linear API error: {exc}") from exc

    response = json.loads(body)
    if "errors" in response:
        raise RuntimeError(str(response["errors"]))
    return response.get("data", {})


def resolve_linear_id_from_task(local_task_id: str) -> str:
    with open(tasks_path, "r", encoding="utf-8") as f:
        payload = json.load(f)
    tasks = payload if isinstance(payload, list) else payload.get("tasks", [])
    for task in tasks:
        if task.get("id") == local_task_id:
            lid = task.get("linearId")
            if not lid:
                raise RuntimeError(f"Task '{local_task_id}' has no linearId yet")
            return lid
    raise RuntimeError(f"Task '{local_task_id}' not found in .linear/data/tasks.json")


def resolve_target_state_id(name: str) -> str:
    data = gql(
        "query TeamStates($id: String!) { team(id: $id) { states { nodes { id name type } } } }",
        {"id": team_id},
    )
    nodes = ((data.get("team") or {}).get("states") or {}).get("nodes", [])
    if not nodes:
        raise RuntimeError("No workflow states found for team")

    target = name.lower().strip()
    for node in nodes:
        if (node.get("name") or "").lower().strip() == target:
            return node["id"]

    # fallback for common done aliases
    if target in {"done", "complete", "completed"}:
        for node in nodes:
            if node.get("type") == "completed":
                return node["id"]

    available = ", ".join(sorted((n.get("name") or "") for n in nodes if n.get("name")))
    raise RuntimeError(f"State '{name}' not found. Available: {available}")


def transition_issue(issue_id: str, state_id: str) -> None:
    gql(
        "mutation IssueUpdate($id: String!, $input: IssueUpdateInput!) { issueUpdate(id: $id, input: $input) { success } }",
        {"id": issue_id, "input": {"stateId": state_id}},
    )


if not linear_id:
    linear_id = resolve_linear_id_from_task(task_id)

state_id = resolve_target_state_id(state_name)
log(f"Transitioning issue {linear_id} to state '{state_name}' ({state_id})")
transition_issue(linear_id, state_id)
print(f"OK: transitioned {linear_id} -> {state_name}")
PY

if [[ "$DO_PULL" -eq 1 ]]; then
  "$ROOT_DIR/.linear/scripts/pull.sh"
fi
