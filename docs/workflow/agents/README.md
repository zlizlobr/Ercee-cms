# Workflow Agents (Centralized)

Agent runtime skills are managed centrally in:

- `/usr/local/var/www/agents/module-builder-agent/SKILL.md`
- `/usr/local/var/www/agents/block-builder-agent/SKILL.md`
- `/usr/local/var/www/agents/field-type-agent/SKILL.md`
- `/usr/local/var/www/agents/test-runner-agent/SKILL.md`
- `/usr/local/var/www/agents/review-agent/SKILL.md`
- `/usr/local/var/www/agents/docs-editor-agent/SKILL.md`

The files in `docs/workflow/agents/*/SKILL.md` are symlink references for this repository.

## Why this model

- One shared agent definition works across multiple repositories.
- Project repositories keep process docs, schemas, CI rules, and artifact evidence.
- You can attach or remove symlink references per repository without duplicating skill logic.
