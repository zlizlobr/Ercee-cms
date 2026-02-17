# Workflow Agents (Centralized)

Canonical agent skills are managed centrally in:

- <https://github.com/zlizlobr/ercee-agents>

Repository-local files in `docs/workflow/agents/*/SKILL.md` are CI-safe reference pages
that point to canonical skills and local runbooks.

## Why this model

- One shared agent definition works across multiple repositories.
- Project repositories keep process docs, schemas, CI rules, and artifact evidence.
- CI is stable because docs checks do not depend on external absolute-path symlinks.
