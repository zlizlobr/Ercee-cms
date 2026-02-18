# Novy Modul: Git Setup (Junior Postup)

## Cíl

Po vytvoření nového modulu mít okamžitě funkční:

1. samostatný Git repozitář,
2. CI (`pint`, `phpstan`, testy),
3. PR workflow (`task/*`, `release/*`),
4. bezpečný merge proces.

## Kde modul žije

- Lokální složka modulů: `/usr/local/var/www/ercee-modules`
- Každý modul má vlastní repozitář (není jeden společný git pro všechny moduly).

## 1) Založení repa pro nový modul

1. Vytvoř modul ve workspace (např. `ercee-module-xyz`).
2. V rootu modulu spusť:
   - `git init`
   - `git checkout -b main`
   - `git add -A`
   - `git commit -m "chore: bootstrap module"`
3. Na GitHubu vytvoř private repo `ercee-module-xyz`.
4. Připoj remote:
   - `git remote add origin <GITHUB_REPO_URL>`
   - `git push -u origin main`

## 2) Instalace standardního CI flow

Z CMS repo (`/usr/local/var/www/Ercee-cms-llm-codex`) spusť:

- `scripts/workflow/install-module-ci-template.sh --module-path /usr/local/var/www/ercee-modules/ercee-module-xyz`

To vloží do modulu standardní `.github/workflows/ci.yml`.

## 3) Co CI kontroluje

`Module CI` má 3 režimy:

1. `Module Only / Code Quality (required)`
2. `Module Only / Tests PHP 8.3 (required)`
3. `Module Only / Tests PHP 8.4 (optional)`

Volitelně:

1. `CMS Integration / <module> (optional)`

Poznámka:

- Default je `diff-only` (rychlé PR kontroly).
- Full quality lze pustit přes release flag/workflow volbu.

## 4) Composer minimum (aby CI nepadalo na tooling)

V modulu musí být v `require-dev`:

- `laravel/pint`
- `phpstan/phpstan`
- `phpunit/phpunit`

A po změně:

- `composer update --dev`
- commit `composer.json` + `composer.lock`

## 5) GitHub Settings (browser) pro modul

V modulu otevři:

- `Settings -> Rules -> Rulesets -> New ruleset`

Doporučené minimum:

1. Target branch: `main`
2. `Require a pull request before merging` = ON
3. `Require status checks to pass` = ON
4. Required checks:
   - `Module CI / Module Only / Code Quality (required)`
   - `Module CI / Module Only / Tests PHP 8.3 (required)`
5. `Require conversation resolution before merging` = ON
6. `Block force pushes` = ON

Pozor:

- Nejdřív musí check aspoň jednou proběhnout, jinak GitHub nenabídne jeho výběr.

## 6) Branch/PR konvence

1. Feature branch: `task/<kratky-popis>`
2. Release branch: `release/<verze-nebo-popis>`
3. PR title:
   - `task: <co se řeší>`
   - `release: <co vydáváš>`

## 7) Rychlý smoke test po setupu

1. Vytvoř branch `task/ci-smoke`.
2. Udělej malou změnu v PHP souboru.
3. Otevři PR do `main`.
4. Ověř, že běží:
   - `Module Only / Code Quality`
   - `Module Only / Tests PHP 8.3`
5. Pokud check neběží:
   - zkontroluj `on: pull_request` ve workflow,
   - zkontroluj že workflow je v default branch,
   - zkontroluj že repo má zapnuté Actions.

## 8) Nejčastější problémy

1. `No such file or directory: ./vendor/bin/pint`
   - chybí `laravel/pint` v `require-dev`.
2. `No merge-base ... Falling back`
   - běžné u nových branchí; workflow musí fallbacknout na full check.
3. Check sice existuje, ale merge neblokuje
   - check není přidaný v rulesetu jako required.
4. Na private free plánu nelze vynutit vše stejně jako v Team/Enterprise
   - i tak drž procesně pravidlo: bez zelené CI nemergovat.

## 9) Hotovo checklist

- [ ] Modul má vlastní GitHub repo (private)
- [ ] `main` branch existuje a je pushnutá
- [ ] `.github/workflows/ci.yml` je z našeho template
- [ ] `laravel/pint`, `phpstan/phpstan`, `phpunit/phpunit` jsou v `require-dev`
- [ ] Ruleset má required checks pro `Code Quality` + `Tests PHP 8.3`
- [ ] PR smoke test proběhl a checky jsou vidět
