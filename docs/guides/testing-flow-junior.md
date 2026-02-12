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
php artisan test
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

## 6. Done criteria
- Bez merge, pokud neprojde A + B.
- U public zmen preferuj i C.
- Bugfix bez regresniho testu se nepovazuje za hotovy.

## Related docs
- `docs/cms-block-integration-guide.md`
- `dev/todo/testing-unification-ercee-ecosystem.md`
- `docs/guides/setup/local-frontend-setup.md`
