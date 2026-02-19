# Testing Flow For Junior Developer (CMS + Frontend + Modules)

Tento guide je prakticky checklist pro kazdou zmenu.

## 1. Vyber typ zmeny
- Menim CMS bloky -> pouzij `verify:blocks`
- Menim public formulare/field type -> pouzij `verify:forms-field`
- Menim backend logiku modulu -> spust modulove PHPUnit testy

## 2. Spust A-level kontrolu (vzdy)

### Pro bloky
```bash
cd /usr/local/var/www/Ercee-cms
npm run preflight:blocks
npm run verify:blocks
```

### Pro field/public cast
```bash
cd /usr/local/var/www/ercee-frontend
npm run preflight:forms-field
npm run verify:forms-field
```

## 3. Spust B-level testy

Frontend unit testy:
```bash
cd /usr/local/var/www/ercee-frontend
npm run test
```

CMS nebo modul testy:
```bash
cd /usr/local/var/www/Ercee-cms
./scripts/test-safe.sh
```

nebo pro modul:
```bash
cd /usr/local/var/www/ercee-modules/<module>
./vendor/bin/phpunit
```

## 4. Spust C-level smoke (kdyz menis public runtime)

```bash
cd /usr/local/var/www/Ercee-cms
npm run verify:blocks:e2e
```

nebo:
```bash
cd /usr/local/var/www/ercee-frontend
npm run verify:forms-field:e2e
```

## 5. Co delat kdyz test failne
- `missing eslint/vitest` -> v frontendu spust `npm ci`
- `typecheck fail` -> oprav TS typy a parser mapovani
- `lint fail` -> oprav format/styl
- `unit test fail` -> oprav chovani nebo test fixture
- `e2e fail` -> over data seed, endpointy a runtime render
- `DB-SAFETY Blocked` -> pokud opravdu potrebujes mutacni prikaz (`migrate`, `db:seed`), spust ho explicitne s `ERCEE_ALLOW_DB_MUTATION=1`.

## 6. Done criteria
- Bez merge, pokud neprojde A + B.
- U public zmen preferuj i C.
- Bugfix bez regresniho testu se nepovazuje za hotovy.

## 7. Povinne endpoint assertions (anti-shape-only pravidlo)
- Samotne `assertJsonStructure` nestaci.
- Pro kazdy meneny endpoint pridej minimalne:
  - 1x happy path s business pravidlem (ne jen shape),
  - 1x negativni branch (404/422/401/403 podle typu endpointu),
  - 1x side-effect/invariant assertion (DB count, event dispatch, ordering, fallback precedence),
  - idempotence/retry test vsude, kde endpoint vytvari nebo meni data.

Prakticky checklist pred PR:
- "Overil jsem business pravidlo?"
- "Overil jsem negativni branch?"
- "Overil jsem side effect nebo invariant?"
- "Pokud endpoint zapisuje data: overil jsem idempotenci?"

Referencni matrix pro vsechny endpointy:
- `docs/guides/test-strategy-recommendations-ecosystem.md` (sekce 7)
- `docs/guides/endpoint-test-backlog-ecosystem.md` (konkretni TODO po endpointu)

## Related docs
- `docs/guides/test-writing-guide.md`
- `docs/cms-block-integration-guide.md`
- `dev/todo/testing-unification-ercee-ecosystem.md`
- `docs/guides/setup/local-frontend-setup.md`
