# PHPDoc Sweep Agent Runbook

Use this runbook when you need to complete or standardize PHPDoc in a target PHP directory.

## Steps

1. Load rules from `docs/guides/application-phpdoc-notes-guide.md`.
2. Scan only the requested directory recursively for `*.php` files.
3. Add missing class/method docblocks and required `@param`, `@return`, `@var` annotations.
4. Re-scan the same directory and confirm no remaining PHPDoc gaps against the guide.
5. Return a concise changed-files summary.

## Failure Recovery

- If the canonical guide is missing or ambiguous for a specific case, stop and request clarification before editing.
