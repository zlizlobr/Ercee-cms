# Ercee Public Dev Layer Guide

This guide defines safe handling of public debug artifacts in CMS public output.

## Canonical policy

- `/usr/local/var/www/Ercee-cms/docs/guides/dev/ercee-dev-layer-policy.md`
- `/usr/local/var/www/Ercee-cms/docs/guides/dev/ercee-dev-layer-policy.contract.json`

## Rules

- Never write ad-hoc debug files directly into `public/`.
- Use `App\Support\DevLayer\PublicDebugWriter` for any public debug artifact.
- Public debug output must be explicitly enabled and only in dev profile.
- In staging/prod, public debug must remain OFF.

## Validation

- `php /usr/local/var/www/Ercee-cms/scripts/workflow/validate-dev-layer-policy.php`
