# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Official turboSMTP Email Validator WordPress plugin (slug: `turbosmtp-email-validator`). It validates email addresses submitted through WordPress forms in real time against the turboSMTP email validation API and blocks submissions whose address does not pass. Structured on the WordPress Plugin Boilerplate (WPPB): a `Turbosmtp_Email_Validator_Loader` collects hooks, `Turbosmtp_Email_Validator` (includes/class-turbosmtp-email-validator.php) wires admin and public classes together in `define_admin_hooks()` / `define_public_hooks()`.

## Commands

There is no build step: PHP is loaded directly by WordPress and the CSS/JS under `admin/` and `public/` are plain files enqueued as-is. No tests and no linting are configured.

Toolchain: Node 20 (`.nvmrc`) + pnpm 9 (pinned via `packageManager`, use through corepack: `corepack enable` once, then plain `pnpm` works). It exists only for dev tooling — nothing in the shipped plugin is generated from it.

## Local dev environment (wp-env)

`@wordpress/env` (needs Docker) spins up WordPress with the plugin mounted and activated; config in `.wp-env.json` (PHP 8.2, `WP_DEBUG` on). Ports are 8890/8891 so it can run alongside the turbosmtp-for-wordpress plugin's wp-env (8888/8889).

```bash
pnpm install
pnpm run env:start    # http://localhost:8890 — admin: admin/password
pnpm run env:stop
pnpm run env:destroy  # remove containers + volumes
pnpm run env:cli ...  # WP-CLI inside the container, e.g. pnpm run env:cli option get turbosmtp_email_validator_enabled
```

## Architecture

### Validation flow

All form integrations are registered in `define_public_hooks()` (includes/class-turbosmtp-email-validator.php), each gated on two options: `turbosmtp_email_validator_enabled` must be `yes`, and the form's key must be in the `turbosmtp_email_validator_validation_forms` array. Supported forms (keys from `turbosmtp_email_validator_validation_forms()` in common-api.php): Contact Form 7, WPForms, WooCommerce (registration + checkout), WordPress comments, WordPress registration (single + multisite), MC4WP Mailchimp, Gravity Forms, Elementor Forms.

Each handler in `public/class-turbosmtp-email-validator-public.php` builds a `Turbosmtp_Email_Validator_Form_Public` (public/class-turbosmtp-email-validator-form-public.php) with a `source` + `form_id`, then:

1. `prep_validation_info( $email )` — skips validation if the email/domain is whitelisted (`turbosmtp_email_validator_email_in_whitelist()`), otherwise reuses a recent result for the same email+source within the threshold window (default 3 minutes, `turbosmtp_email_validator_threshold` filter), otherwise calls the API. Every fresh validation is inserted into the `{$wpdb->prefix}validated_emails` table and fires the `turbosmtp_email_validator_validated_email` action.
2. `setup_form_validation( $validationInfo, $callback, $args )` — invokes the form-specific error callback when the status does not pass.

Status logic lives in common-api.php: `turbosmtp_email_validator_status_ok()` passes any status listed in the `turbosmtp_email_validator_validation_pass` option (defaults: `valid`, `catch-all`, `unknown`). API failures **fail open**: `Turbosmtp_Email_Validator_API::validateEmail()` wraps errors in `WP_Error` whose data carries `status: valid` (with `sub_status` `api_bad_request`/`api_request_wrong` and the real status in `original_status`), so form submissions are never blocked by API downtime.

WordPress comments use a different mechanism: `pre_comment_on_post` temporarily adds a filter on `is_email` (`apply_is_email_validator` / `remove_is_email_validator`).

### API client (includes/class-turbosmtp-email-validator-api.php)

`Turbosmtp_Email_Validator_API` wraps `wp_remote_get`/`wp_remote_post` against `https://pro.api.serversmtp.com/api/v2` (overridable via the `turbosmtp_api_url` filter). Auth is a consumer key/secret pair sent as request headers. Endpoints used: `emailvalidation/validateEmail`, `emailvalidation/subscription`, `user/config` (the latter doubles as the key-validation check in `isValid()`). An optional long-lived cache (90 days, in the same `validated_emails` table) can be enabled with the `turbosmtp_email_validator_has_cache` filter.

### Admin (admin/class-turbosmtp-email-validator-admin.php)

Single class holding the settings pages (WordPress Settings API; partials in `admin/partials/`: settings, history, account, whitelist, validator, validation-details, logged-out), the `admin_post` login handler, and AJAX endpoints (`turbosmtp-email-validator-disconnect`, `turbosmtp-email-validator_get_email_details`). The validation history table is `Turbosmtp_Validated_Emails_Table` (includes/class-turbosmtp-validated-emails-table.php, extends `WP_List_Table`).

### Options

All prefixed `turbosmtp_email_validator_`: `enabled`, `validation_forms`, `validation_pass`, `error_message`, `whitelist` (one email or `@domain` per line), `consumer_key`, `consumer_secret`, `api_timeout` (seconds, default 5).

### Activation (includes/class-turbosmtp-email-validator-activator.php)

Creates the `{$wpdb->prefix}validated_emails` table with `dbDelta()` and seeds the `validation_forms`, `validation_pass`, and `error_message` options.

### Extensibility

Filters: `turbosmtp_email_validator_validation_forms`, `_validation_statuses`, `_status_ok`, `_threshold`, `_has_threshold`, `_has_cache`, `_expire_interval`, `_skip_validation`, `turbosmtp_api_url`. Actions: `turbosmtp_email_validator_before_email_validation`, `_validated_email`. The custom error message integrates with Polylang (`pll__`) and WPML (`wpml_translate_single_string`).

## Translations

User-facing strings go through `__()`/`_e()` with the `turbosmtp-email-validator` text domain. Unlike most plugins on translate.wordpress.org, the catalog and the Italian translation are committed in `languages/` (`turbosmtp-email-validator.pot`, `turbosmtp-email-validator-it_IT.po`/`.mo`).

## Release / versioning

Releases are driven by **conventional commits** (`feat:`, `fix:`, …) via `commit-and-tag-version`:

```bash
pnpm run release:dry   # preview: next version + changelog
pnpm run release       # bump versions, update CHANGELOG.md, commit, create tag
git push --follow-tags origin master   # push manually — this triggers the WP.org deploy
```

`pnpm run release` computes the next semver from commits since the last tag and updates every version location in one commit: `turbosmtp-email-validator.php` (header `Version:` + `TURBOSMTP_EMAIL_VALIDATOR_VERSION` constant), `Stable tag:` in `.wordpress-org/readme/README.txt`, `package.json`, and `CHANGELOG.md`. Config is in `.versionrc.js`; the custom updaters live in `scripts/version-updaters/`. Tags are plain versions with no `v` prefix (e.g. `1.9.1`). Never bump versions by hand.

The user-facing changelog inside `.wordpress-org/readme/README.txt` (`== Changelog ==` section) is NOT generated — curate it manually when the release is worth announcing to WP.org users.

Deployment to the WordPress.org SVN repo happens automatically via GitHub Actions (`.github/workflows/deploy.yml`) when a git tag is pushed. `.distignore` controls what is excluded from the deployed zip (dev files like `CLAUDE.md`, `CHANGELOG.md`, `.versionrc.js`, `scripts/` are excluded).

## Conventions

- Code style is WordPress-flavored PHP (tabs, snake_case); match the surrounding file.
- Everything written to the repo or GitHub is in **English**: PR titles and bodies, commit messages, code comments, and docs — regardless of the language used in conversation.
