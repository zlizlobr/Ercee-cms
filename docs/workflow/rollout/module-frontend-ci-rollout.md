# Module + Frontend CI Rollout

## Scope

- Module repositories in `/usr/local/var/www/ercee-modules/*`
- Frontend repository in `/usr/local/var/www/ercee-frontend`

## 1) Install CI into each module repository

Run from CMS repo root:

```bash
scripts/workflow/install-module-ci-template.sh \
  --repo-path /usr/local/var/www/ercee-modules/ercee-module-forms \
  --module-name forms \
  --module-namespace 'Modules\\Forms' \
  --cms-repo zlizlobr/Ercee-cms

scripts/workflow/install-module-ci-template.sh \
  --repo-path /usr/local/var/www/ercee-modules/ercee-module-commerce \
  --module-name commerce \
  --module-namespace 'Modules\\Commerce' \
  --cms-repo zlizlobr/Ercee-cms

scripts/workflow/install-module-ci-template.sh \
  --repo-path /usr/local/var/www/ercee-modules/ercee-module-funnel \
  --module-name funnel \
  --module-namespace 'Modules\\Funnel' \
  --cms-repo zlizlobr/Ercee-cms

scripts/workflow/install-module-ci-template.sh \
  --repo-path /usr/local/var/www/ercee-modules/ercee-module-llm \
  --module-name llm \
  --module-namespace 'Modules\\Llm' \
  --cms-repo zlizlobr/Ercee-cms

scripts/workflow/install-module-ci-template.sh \
  --repo-path /usr/local/var/www/ercee-modules/ercee-module-theme-builds \
  --module-name theme-builds \
  --module-namespace 'Modules\\ThemeBuilds' \
  --cms-repo zlizlobr/Ercee-cms
```

## 2) Install CI into frontend repository

```bash
scripts/workflow/install-frontend-ci-template.sh \
  --repo-path /usr/local/var/www/ercee-frontend
```

## 3) Required checks setup in GitHub UI

Use `Settings -> Rules -> Rulesets` for each repo.

### Module repos (`ercee-module-*`)

Set these checks as **required**:

- `Module Only / Code Quality (required)`
- `Module Only / Tests PHP 8.3 (required)`

Do **not** require:

- `Module Only / Tests PHP 8.4 (required)` (runs only on push)
- `CMS Integration / <module> (optional)`
- `CMS Integration / skipped notice`

### Frontend repo (`ercee-frontend`)

Set these checks as **required**:

- `Frontend / Quality + Tests (required)`
- `Frontend / Data Contract Check (required)`

## 3.1 Optional automation (API instead of browser clicks)

You can apply branch protection via script:

```bash
export GITHUB_TOKEN=YOUR_ADMIN_TOKEN

scripts/workflow/apply-module-frontend-branch-protection.sh \
  --repo zlizlobr/ercee-module-forms \
  --type module

scripts/workflow/apply-module-frontend-branch-protection.sh \
  --repo zlizlobr/ercee-module-commerce \
  --type module

scripts/workflow/apply-module-frontend-branch-protection.sh \
  --repo zlizlobr/ercee-module-funnel \
  --type module

scripts/workflow/apply-module-frontend-branch-protection.sh \
  --repo zlizlobr/ercee-module-llm \
  --type module

scripts/workflow/apply-module-frontend-branch-protection.sh \
  --repo zlizlobr/ercee-module-theme-builds \
  --type module

scripts/workflow/apply-module-frontend-branch-protection.sh \
  --repo zlizlobr/ercee-frontend \
  --type frontend
```

## 4) Secrets required

### Module repos

Optional (only for integration job):

- `CMS_REPO_TOKEN` (read access to CMS repo)

If not set, CI remains green on required checks; only integration job is skipped.

### Frontend repo

No mandatory cross-repo secret for the provided template.

## 5) Validation after rollout

For each module repo:

1. Open PR with a small PHP change.
2. Confirm required checks are green without CMS integration.
3. Confirm merge is not blocked by skipped optional integration job.

For frontend repo:

1. Open PR with TS/UI change.
2. Confirm `Quality + Tests` and `Data Contract Check` run and pass.

## 6) Troubleshooting

- If PR still asks for review unexpectedly, check conflicting legacy branch protection rules in addition to rulesets.
- If required check is shown as missing, verify check name in ruleset matches workflow job name exactly.
- If module integration fails migrations, keep integration non-required and tune migration allowlist.
