# Project Status

_Last updated: 2026-11-28_

## Overview
Bilingual (en/ar) learning platform where learners build web projects (portfolio, landing page) from templates, stage by stage. Laravel 12 / PHP 8.5 (Herd php85), Breeze (Blade + Tailwind), Pest, served by Herd.

## Architecture (quick map)
- **Roles:** `admin` / `learner` — middleware aliases `admin`, `learner`, `active` (suspended check) in `bootstrap/app.php`. `/dashboard` redirects by role.
- **Auth:** Breeze + Google OAuth (`Auth/GoogleAuthController`, Socialite).
- **Domain models:** `Template` → `Stage` (global intro stage has `template_id = null`, `is_global = true`) ; `Project` (user, template, `current_stage_id`, status) → `StageProgress` (`in_progress` / `locked` …).
- **Services:** `ProjectGeneratorService` (creates project + progress rows in a transaction; first stage unlocked), `ActivityLogService` (enum `App\ActivityAction`).
- **Authorization:** `ProjectPolicy`.
- **i18n:** `resources/lang/{en,ar}/*.php`, `SetLocale` middleware, `/language/{locale}` route. All UI strings must exist in both locales.
- **Seeders:** `AdminSeeder`, `TemplateSeeder`, `StageSeeder`.

## Current stage
Phase 3 (Projects) wrapping up + profile/avatar work (uncommitted).

## Completed
- Phase 1: Auth (email + Google), roles, account suspension, activity log, legal pages (terms/privacy), language switcher.
- Phase 2: Templates & stages schema + seeders.
- Phase 3: Learner project CRUD (index/create/store/show/destroy) via `ProjectController`, `StoreProjectRequest`, API resources.

## In progress (uncommitted as of 2026-11-28)
- User avatar upload (`ProfileUpdateRequest`, `User::avatarUrl`/`initials`, `components/avatar.blade.php`).
- Learner dashboard + `layouts/project.blade.php`, new lang files `nav`, `profile_settings`, `project`.
- Tests: `tests/Feature/Learner/AvatarAndDashboardTest.php`, `ProfileTest.php`.

## Remaining / next
- Commit the avatar/profile/dashboard work (still uncommitted).
- Stage workspace / progression (unlocking next stage, completing stages).
- Admin dashboard features (currently a placeholder view).

## Last completed task
Phase 1–3 verification pass (2026-11-28), Laravel is 13.21.1 (not 11). Fixes: `validation.min/max` restored as per-type arrays (en/ar); `auth.password` is the "incorrect password" message again, label moved to `auth.password_label`; added `User::dashboardUrl()` and the layout uses it (admins no longer get learner links); avatar `mimes` now `jpg,jpeg,png,gif,webp` (SVG removed — stored XSS); `StoreProjectRequest` uses `requests.store_project` messages; `catalog.stages.*` + `catalog.stage_descriptions.*` added. Tests: `tests/Feature/VerificationFixesTest.php`.

Profile page translations (2026-11-28): page subtitle now `profile.settings_subtitle` (`profile.subtitle` stays the dashboard's); removed duplicate ar `title`/`subtitle`, added ar `password_hint`; `profile.flash.*` keys renamed to match controller statuses (`profile-updated`, `password-updated`, `verification-link-sent`); fixed swapped `info_hint`/`hint` in `profile/edit.blade.php`; removed dead reset-link banner. Tests in `ProfileTest.php`.

Learner dashboard translations (2026-11-28): added en/ar `learner_dashboard.php` and `catalog.php` (template name/description/level per slug — also used by projects create/index/show); fixed `projects.*` → `project.*` keys in `learner/dashboard.blade.php` and `layouts/project.blade.php`. Test: "renders the dashboard fully translated" in `AvatarAndDashboardTest.php`.

Profile flash messages auto-dismiss after 3 s with a fade (Alpine, `profile/edit.blade.php`, 2026-11-28). Test in `ProfileTest.php`.

Avatar upload size errors (2026-11-28): `ProfileUpdateRequest::avatarUploadFailureMessage()` returns `profile.errors.max` for `UPLOAD_ERR_INI_SIZE`/`UPLOAD_ERR_FORM_SIZE`, `profile.errors.uploaded` otherwise; PHP's reason is only logged (no longer appended — it was English-only). `AvatarAndDashboardTest` passes.

Profile "Forgot password" now links to `password.request` (2026-11-28). Password reset routes moved out of the `guest` group in `routes/auth.php` so signed-in users aren't redirected; forgot-password email prefills from the signed-in user. Tests in `Auth/PasswordResetTest.php`.

## Decisions
- Business logic in `app/Services`, validation in Form Requests, API Resources for project/template/stage data.
- Bilingual en/ar is mandatory for all user-facing text.
- Password reset routes (`forgot-password`, `reset-password`) are open to guests and signed-in users (not in `guest` middleware).
- No success flash messages for creating/deleting projects (removed 2026-11-28 by request); profile page flashes still exist.
- Avatar strings live only in `profile.php`: `avatar_label`, `avatar_remove`, `hint`, `errors.*` (duplicates in `project`/`requests` removed 2026-11-28).
- Avatar upload limit is 5 MB (`max:5120` KB) everywhere — rule, lang strings, tests.
- PHP must allow it: Herd php85 `php.ini` raised to `upload_max_filesize = 10M`, `post_max_size = 12M` (was 2M/8M → caused "The avatar failed to upload."). `avatar.uploaded` errors now show the 5 MB message. Any other machine/server needs the same php.ini change.

## Known issues
- Google sign-in: `.env` `GOOGLE_REDIRECT_URI` is `http://localhost:8000/auth/google/callback` but `APP_URL` is `http://minne.test` (and Herd serves `final_year_project_git.test`; no `minne.test` link). Session state cookie is set on one host and the callback lands on another → InvalidStateException. Set the URI to the real Herd host and register the same URI in Google Cloud Console.
- No `.env.testing`: `migrate:fresh --env=testing` falls back to `.env` and wipes the dev DB `minne`. Tests are fine (`phpunit.xml` sets `DB_DATABASE=testing`).
- Profile routes use `auth` only (not `active`), so suspended users can still edit/delete their profile.
- ar `validation.php` only covers ~8 rules and has no `attributes` map: other rules fall back to English, and `:attribute` stays English in Arabic messages.
- `learning-platform/` is a tracked duplicate copy of the app (58 files) — likely leftover; confirm before deleting.
- Stray `*.backup` files in root (`package.json.backup`, `tailwind.config.js.backup`, etc.).
- No `.ai/rules` directory yet.
