# Documentation Standards

## File naming

- Use `kebab-case` for filenames.
- Prefer concise, descriptive names.
- Use singular nouns unless a section is clearly plural (e.g., `endpoints/`).

## Structure

- Each doc starts with a single `# Title` matching the file purpose.
- Use `##` for major sections, `###` for subsections.
- Keep a short introduction under the title (1–3 sentences).

## Doc types

- **Guide**: goal-oriented, step-by-step, includes prerequisites.
- **Reference**: facts and contracts, no long prose.
- **API**: request/response shapes, errors, and examples.
- **Overview**: high-level context and pointers to details.

## Linking

- Use repo-relative paths (e.g., `docs/guides/setup/local-backend-setup.md`).
- Prefer canonical docs; avoid duplicating content across files.

## Deprecation

- Move outdated docs to `docs/archive/`.
- Add a short note at the top with a deprecation date and replacement link.

## Changelog process

- For release-relevant behavior changes, update `CHANGELOG.md` in the same pull request.
- Changelog entries should include scope, user impact, and mitigation notes for breaking/risky changes.
- If docs are deferred by policy exception, changelog update is still required in Gate 6 evidence.
