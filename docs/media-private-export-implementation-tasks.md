# Media: Private-First + Export Pipeline (LLM Task List)

Goal: Implement a private-first media workflow in Laravel with admin media management (via plugin) and a build-time export pipeline that materializes public assets and a manifest.

## Scope
- Admin uploads are stored on a private disk (not directly web-accessible).
- Build action exports media variants to the public directory.
- Frontend resolves media via a generated manifest (JSON).
- Use a media plugin (Spatie Media Library + Filament integration).

## Assumptions
- Laravel app uses `storage:link` for the public disk.
- Build pipeline can run `php artisan` commands.
- Target public output directory: `public/media`.

## Tasks (Implementation)
1. Install and configure media library
   - Add Spatie Media Library and Filament media manager plugin.
   - Publish vendor config(s) if needed.
   - Create/confirm Media models and migrations.
   - Configure conversions (e.g., `thumb`, `medium`, `large`, `webp`).

2. Add private media disk
   - Add a `media` disk in `config/filesystems.php` (driver `local`).
   - Root: `storage/app/media` (private, no public URL).
   - Ensure disk is used by the media library as default for new uploads.

3. Admin: Media section
   - Create a Filament Resource for media management (or enable plugin resource).
   - Fields: file upload, title, alt text, focal point, tags (optional).
   - Restrict uploads to safe MIME types and max size.
   - Store metadata in the media table (width, height, checksum).

4. Export command
   - Create `php artisan media:export` command.
   - Inputs: `--only-changed`, `--disk=media`, `--public-path=public/media`.
   - For each media item:
     - Generate conversions (if missing).
     - Copy original and conversions to `public/media/{uuid}/{filename}`.
     - Build manifest entry with URLs, sizes, mime, alt, focal point.
   - Write `public/media-manifest.json` atomically.

5. Manifest format
   - Stable JSON keyed by media ID or UUID.
   - Include:
     - `original`: url, width, height, size, mime
     - `variants`: map of conversion name to url/size/dimensions
     - `alt`, `focal_point`, `checksum`

6. API exposure
   - Update API serializers to return manifest URLs instead of raw storage paths.
   - If content stores media IDs, resolve them to manifest entries.

7. Build integration
   - Add CI/CD step to run `php artisan media:export --only-changed`.
   - Ensure `public/media` and `public/media-manifest.json` are included in build artifacts.

8. Tests
   - Unit: media export command (manifest structure, copy locations).
   - Integration: upload media in admin -> export -> public files exist.
   - Regression: unchanged media does not get re-copied when `--only-changed`.

9. Documentation
   - Document the workflow, export command, and manifest format.
   - Add runbook for re-export / invalidation scenarios.

## Non-goals
- Real-time public serving from private disk.
- CDN integration (can be added later).

