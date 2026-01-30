## [v0.6.1] – 2026-01-30

- release: patch -- refactor navigation: model, api and admin (#36)
* hotfix: navigation structrue, update docs,. update api controller

* update docs

* Hotfi endpoint api media
- chore: update changelog for v0.6.0

## [v0.6.0] – 2026-01-30

- release: minor -- Finished refactor modules structure (#35)
* Hotfix: issue nedpoint

* upadate ModuleManager, BlockRegistry, Commerce modul, Funnel modul

* change Event bus, Funnel event listeners

* Migrating controllers to module classes, Cleanup AppServiceProvider, CommerceModuleServiceProvider

* Composer.json modulu  add CI action

* Add ci moudle release

* Migrate old name space faze 1

* Complete backward-compatible migration chunk 1

* Complete backward-compatible migration chunk 2

* Hotfix error test

* Add seeder for creating page. Update docs API
- chore: update changelog for v0.5.9

## [v0.5.9] – 2026-01-29

- release: patch --  Fix all bock issues, progress modules refactoring (#34)
* Fix composer, add docPHP for media

* add media fisrt to product flow

* fix select from library

* Update docs for build block in Pages

* Hotfix: label for blocks

* add localizace


- chore: update changelog for v0.5.8

## [v0.5.8] – 2026-01-29

- release: patch -- sync Modules process (#33)

- chore: update changelog for v0.5.7

## [v0.5.7] – 2026-01-29

- release: patch -- Migrating business logic to modules Admin  (#32)
* Migrating business logic to modules Admin

* Ecommerce module

* Funnel modules
- chore: update changelog for v0.5.6

## [v0.5.6] – 2026-01-29

- release: patch -- Add all thema blocks to builder (#31)
* add schema for new blocks from all thema

* chunk 01/03

* chunk 02/03

* chunk 03/03
- chore: update changelog for v0.5.5

## [v0.5.5] – 2026-01-28

- release: patch -- add service block (#30)
* Add Services block

* update composer
- chore: update changelog for v0.5.4

## [v0.5.4] – 2026-01-28

- release: patch -- Add SEO Image Renaming (#28)
* add SEO Image Renaming
- chore: update changelog for v0.5.3

## [v0.5.3] – 2026-01-28

- release: patch -- Update hero block (#27)
* Upadate hero block
- chore: update changelog for v0.5.2

## [v0.5.2] – 2026-01-28

- release: patch --  Add new block to build, hotfix sync media (#26)
* add premium cta block

* Hotfix sync media
- chore: update changelog for v0.5.1

## [v0.5.1] – 2026-01-27

- release: patch -- add Rebuildmap servis class for build public part (#25)
* add RebuildMap for resources

* update readme for flow field type and rebuildmap. Add support doc
- chore: update changelog for v0.5.0

## [v0.5.0] – 2026-01-27

- release: minor -- New builder page Shema + New version Form shema (#24)
* add Testimaniol block

* udpate testimonial rating

* add thema contact form block

* Update contact form optimization flow

* synchronization of icons for admin/public forms
ltimpalization of tables for forms

* add section field

* fix send form api

* Add sidebar options to setting form

* Refactor form model->move label, massage title to data_options

* change themaSetting to patter Resource/page

* fix 2 collumns layout

* reafactor load fields in from from json patter

* WIP: form field types registry
- chore: update changelog for v0.4.1

## [v0.4.1] – 2026-01-26

- release: patch -- Update docs, block and phpStan media docs (#23)
* Fix composer, add docPHP for media

* add media fisrt to product flow

* fix select from library

* Update docs for build block in Pages
- chore: update changelog for v0.4.0

## [v0.4.0] – 2026-01-26

- release: minor -- private-first media workflow (#22)
* Implement a private-first media workflow

* Add MediaPicker component, block media integration, and migration commands for centralized media management

* Fix images preview.
- chore: update changelog for v0.3.7

## [v0.3.7] – 2026-01-23

- release: patch -- command build block (#21)
* Create an Artisan command to build block
- chore: update changelog for v0.3.6

## [v0.3.6] – 2026-01-23

- release: patch -- Refactor Rest API (#20)
* Add REST API hardening with token auth, rate limiting, idempotency, audit logging, FormRequest validation, and centralized exception handling
- chore: update changelog for v0.3.5

## [v0.3.5] – 2026-01-23

- release: patch -- update docs (#19)
* Update docs
- chore: update changelog for v0.3.4

## [v0.3.4] – 2026-01-23

- PR title: release: patch --Refactor product architecture (#17)
* fix save navigation

* refactor product architecture

* Update doc, update Rest Api produkt. Add product descriptions
- chore: update changelog for v0.3.3

## [v0.3.3] – 2026-01-23

- release: patch -- Filament Builder Block for page (#16)
* Migrate page builder from Repeater to Filament Builder with Hero block, reusable Blade components, preview action, validation rules, and block contract documentation

* add builder localizace

* Filament Builder Block Autoloading
- chore: update changelog for v0.3.2

## [v0.3.2] – 2026-01-22

- release: patch -- Refactor page builder (#15)
* add doc for menu, navigation scrope

* Add phpDoc

* add doc for endpoints

* Migrate page builder from Repeater to Filament Builder with Hero block, reusable Blade components, preview action, validation rules, and block contract documentation
- chore: update changelog for v0.3.1

## [v0.3.1] – 2026-01-20

- release: patch -- Refactor navigation (#14)
* Refactor menu, set up menu group for links
- chore: update changelog for v0.3.0

## [v0.3.0] – 2026-01-20

- release: minor -- Add i18n support (#13)
* Add i18n support with Czech/English translations, locale middleware, and translatable page titles
- chore: update changelog for v0.2.1

## [v0.2.1] – 2026-01-20

- release: patch -- Fix Astro build  (#12)
* Fix Astro build errors: add currency fallback, map form schema to fields, handle nullable config
- chore: update changelog for v0.2.0

## [v0.2.0] – 2026-01-20

- release: minor -- Astro Headless Setup (#11)
* Astro Headless Setup
- chore: update changelog for v0.1.4

## [v0.1.4] – 2026-01-19

- release: patch -- production hardening with idempotent handlers
* Add production hardening with idempotent handlers, DB transactions, webhook signature verification, and IP whitelist middleware
- chore: update changelog for v0.1.3

## [v0.1.3] – 2026-01-19

- release: patch -- fix form (#9)
* Fix misssing form
- chore: update changelog for v0.1.2

## [v0.1.2] – 2026-01-19

- release: patch -- application Orchestration (#8)
* Application Orchestration layer

* orchestrace
- chore: update changelog for v0.1.1

## [v0.1.1] – 2026-01-19

- release: patch -- Frontend MVP & Public API Integration
* Frontend MVP & Public API Integration
- chore: update changelog for v0.1.0

## [v0.1.0] – 2026-01-19

- release: minor -- lightweight Commerce & Checkout Core (#6)
* Lightweight Commerce & Checkout Core
- chore: update changelog for v0.0.5

## [v0.0.5] – 2026-01-19

- release: patch -- Funnel Engine & Marketing Automation Core (#5)
* Funnel Engine & Marketing Automation Core
- chore: update changelog for v0.0.4

## [v0.0.4] – 2026-01-19

- release: patch -- add Forms & Contracts
* Add Forms & Contracts module with dynamic form builder, public submission API, and lead capture system
- chore: update changelog for v0.0.3

## [v0.0.3] – 2026-01-19

- release: patch -- Add CMS module and public REST API (#3)
* Add CMS module with block-based page editor, navigation management, and public REST API
- chore: update changelog for v0.0.2

## [v0.0.2] – 2026-01-19

- release: patch -- Add Filament admin panel (#2)
* Initialize Laravel 12 platform with SQLite, Redis, Mailpit and CI pipeline

* Add Filament admin panel with role-based access control and core domain entities (Subscriber, Page, Product)
- Initialize Laravel
* Initialize Laravel 12 platform with SQLite, Redis, Mailpit and CI pipeline
- Init first setup

