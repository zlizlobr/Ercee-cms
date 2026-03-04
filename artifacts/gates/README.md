# Gate Artifacts

Store gate evidence in:

`artifacts/gates/<initiative-id>/<gate-name>/`

Expected gate names:

- `spec-plan`
- `implementace`
- `test-gate`
- `ralph-review`
- `docs-gate`
- `release-readiness`

Recommended files:

- `summary.md`
- `agent-output.json`
- `review-findings.json` (for Ralph gate)
- `test-results.txt` (for test gate)
- `exception-record.md` (when policy exception is used)
