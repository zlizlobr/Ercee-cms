# Module Builder Agent Skill Reference

Canonical skill source:

- <https://github.com/zlizlobr/ercee-agents/blob/main/module-builder-agent/SKILL.md>

Repository-local context:

- `docs/workflow/runbooks/module-builder-agent.md`
- `docs/guides/module-development-guide.md`
- `docs/workflow/new-module-git-setup-junior.md`

Required behavior for `new-module` scope:

- bootstrap module CI via `scripts/workflow/install-module-ci-template.sh`
- verify module dev tooling (`laravel/pint`, `phpstan/phpstan`, `phpunit/phpunit`)
- return GitHub ruleset/setup recommendations for the new module repository

Note:

- This file is a CI-safe reference document.
- Runtime agent loading is expected to use the centralized agents repository.
