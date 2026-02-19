# Test Writing Guide (Senior Baseline)

This is the canonical guide for writing tests in the Ercee ecosystem.
Use it for implementation rules. Keep flow docs focused on execution order and commands.

## Scope
- CMS: `/usr/local/var/www/Ercee-cms`
- Frontend: `/usr/local/var/www/ercee-frontend`
- Modules: `/usr/local/var/www/ercee-modules/ercee-module-*`

## 1. Test Design Principles

1. Test behavior, not implementation details.
2. Prefer business invariants over shape-only assertions.
3. Every changed bug path must have a regression test.
4. Keep tests deterministic and isolated.
5. Minimize flaky dependencies (time, network, shared state).

## 2. Required Assertion Pattern

For each changed endpoint or public contract, include:
- 1 happy-path test with business rule assertion.
- 1 negative-path test (`404/422/401/403/429`, based on contract).
- 1 side-effect or invariant assertion (`assertDatabaseHas`, event dispatch, ordering, fallback precedence).
- idempotence/retry test for write operations (create/update/process).

`assertJsonStructure` is supplemental only.

## 3. Layer Responsibilities

- Unit:
  - Pure domain/application logic.
  - No real I/O.
  - Fast and deterministic.
- Feature/Integration:
  - HTTP contract, auth boundary, validation, side effects.
  - DB assertions and event/job assertions are expected.
- E2E:
  - Critical user journeys and runtime integration only.
  - Do not use E2E as substitute for unit/feature coverage.

## 4. Test Naming and Structure

- Use explicit test method names that encode behavior and condition:
  - `test_pages_show_returns_404_for_draft_or_missing_slug`
  - `test_rebuild_endpoint_dispatches_frontend_build_with_valid_token`
- Arrange-Act-Assert structure:
  - Arrange data and dependencies.
  - Act with one explicit request/action.
  - Assert status + contract + behavior.

## 5. Data and Fixture Rules

- Use factories with explicit overrides for scenario clarity.
- Avoid hidden coupling to seeded or pre-existing local data.
- Keep fixtures minimal and local to the test.
- If time impacts behavior, set explicit timestamps in factory data.

## 6. Contract-Focused API Testing

For API responses:
- Assert key typed fields (not only existence).
- Assert semantic constraints:
  - filtering (`active` only),
  - ordering determinism,
  - fallback precedence,
  - canonical identity (`payload.slug == request slug`).
- Assert error payload contract for negative branches.

## 7. Side-Effect and Safety Assertions

For write endpoints:
- Verify persisted state (`assertDatabaseHas`, count deltas).
- Verify emitted events/jobs where part of behavior.
- Verify duplicates are not created when idempotency/retry applies.

For security-sensitive endpoints:
- Always test missing and invalid auth/token/signature paths.

## 8. Flaky Prevention Rules

- No `test.skip` for critical smoke in default branch.
- Avoid assertions on unstable text/order unless contract defines it.
- Mock external providers and network boundaries.
- Keep one behavior focus per test.

## 9. Anti-Patterns (Do Not Merge)

- Shape-only tests with no behavior assertion.
- Assertions that depend on unrelated global state.
- Tests that pass only in a specific local environment.
- Intentional fail tests inside regular suites.

## 10. Review Checklist (PR Gate)

- Does each changed endpoint have happy + negative + invariant coverage?
- Are write paths protected by idempotence/retry checks (when relevant)?
- Are auth/permission boundaries tested?
- Is the test deterministic and isolated?
- Is there at least one regression test for each fixed bug?

## Related Docs
- `docs/guides/testing-flow-junior.md`
- `docs/guides/testing-flow-llm.md`
- `docs/guides/test-strategy-recommendations-ecosystem.md`
- `docs/guides/endpoint-test-backlog-ecosystem.md`
