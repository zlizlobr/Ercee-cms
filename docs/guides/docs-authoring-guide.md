# Docs Authoring Guide

## Purpose

Keep documentation consistent, searchable, and easy to maintain.

## Scope

- How to write docs
- How to name docs files
- When to create a new guide vs a task plan

## File Naming Rules

Use kebab-case and a consistent suffix.

- Guides: `*-guide.md`
- Integration guides: `*-integration-guide.md`
- Architecture guides: `*-architecture-guide.md`
- Ops/build guides: `*-ops-guide.md` or `*-build-guide.md`
- Task plans: `*-refactor-tasks.md`, `*-implementation-tasks.md`
- Reference docs: `*-reference.md`

Examples:
- `frontend-architecture-guide.md`
- `theme-api-integration-guide.md`
- `astro-ssg-incremental-build-guide.md`
- `frontend-architecture-refactor-tasks.md`

## Canonical vs Task Docs

- Canonical guide = stable rules and current process.
- Task doc = time-bound plan, checklist, or migration steps.
- Do not duplicate rules in task docs. Link to the canonical guide instead.

## Writing Rules

- Start with a 1-2 sentence overview.
- Prefer short sections with clear headings.
- Use bullet lists for steps or requirements.
- Avoid long prose. Keep instructions actionable.
- Avoid copying outdated paths. Verify locations first.

## Cross-Repo References

- Use absolute paths only when the target is in another repo.
- Prefer relative paths inside the same repo.
- If a guide becomes canonical, update all references to it.

## When to Add a New Doc

- New domain or integration (create a guide).
- One-off project or migration (create a task plan).
- API contract or schema (create a reference doc).

## Placement

- Frontend architecture and ops docs: `docs/guides/frontend/`
- Content guides: `docs/guides/content/`
- Cross-cutting or general authoring: `docs/guides/`
