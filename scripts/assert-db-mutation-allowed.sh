#!/usr/bin/env bash
set -euo pipefail

CONTEXT="${1:-db-mutation}"
APP_ENV_VALUE="${APP_ENV:-unknown}"
CI_VALUE="${CI:-false}"
ALLOW_VALUE="${ERCEE_ALLOW_DB_MUTATION:-0}"

if [[ "$APP_ENV_VALUE" == "testing" || "$APP_ENV_VALUE" == "production" || "$CI_VALUE" == "true" || "$ALLOW_VALUE" == "1" ]]; then
  exit 0
fi

cat >&2 <<EOF
[DB-SAFETY] Blocked: $CONTEXT
[DB-SAFETY] Refusing DB mutation in current environment.
[DB-SAFETY] APP_ENV=$APP_ENV_VALUE CI=$CI_VALUE ERCEE_ALLOW_DB_MUTATION=$ALLOW_VALUE
[DB-SAFETY] To continue intentionally, rerun with ERCEE_ALLOW_DB_MUTATION=1.
EOF

exit 1
