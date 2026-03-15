# CLAUDE.md — Project Root

## What this is

A WordPress development environment running in Docker. The theme and plugin source live in `wp-content/`. WordPress core is mounted read-only from `./wordpress/` (not committed — pulled via Composer or downloaded separately).

## Docker stack (`compose.yml`)

| Service | Image | Purpose | Ports |
|---|---|---|---|
| `wordpress` | Custom (built from `docker/wordpress/Dockerfile`) | Apache + PHP 8.3 + XDebug | 80 |
| `db` | `mariadb:11.8` | Database | 3306 |
| `phpmyadmin` | `phpmyadmin` | DB GUI | 8000 |
| `cli_tools` | `digitalblake/light-cli:5.0.0` | Node/pnpm/PHP CLI, browser-sync | 3000, 3001 |

**Start everything:** `docker compose up -d`

**Enter the CLI container for build commands:**
```sh
docker compose exec cli_tools zsh
```

## Pre-commit hooks

Lint checks run automatically before each commit. Activate once per clone:

```sh
git config core.hooksPath .githooks
```

The hook (`.githooks/pre-commit`) runs phpcs on staged PHP files, eslint on staged JS files, and stylelint on staged SCSS files. It requires `composer install` and `pnpm install` to have been run first.

## Environment variables

Credentials live in `.env` (gitignored). Copy `.env-example` and fill in values. Never commit `.env`.

## Vendor / Composer

PHP dependencies (PHPCS, WordPress Coding Standards, PHPCompatibility) are managed by Composer. The vendor directory lives at `wp-content/vendor/`, so from the theme directory the path is `../../vendor/bin/phpcs`.

PHPCS `installed_paths` for WordPress and PHPCompatibility standards is configured inside the `cli_tools` container. Running PHPCS locally requires those standards to be configured globally on your machine.

## XDebug

The `wordpress` service has XDebug 3 installed and configured (`docker/wordpress/xdebug.ini`). It connects back to the host on port 9003. The VSCode "Listen for XDebug" launch configuration in `.vscode/launch.json` is pre-configured. XDebug is dev-only — do not use this Dockerfile in production.

XDebug defaults to `off` in `xdebug.ini`. It is enabled in `compose.yml` via `XDEBUG_MODE=debug`. Comment that line out when you don't need it to avoid the PHP performance overhead.

## Path mappings (for debugging / reference)

| Host | Container (`wordpress`) |
|---|---|
| `./wp-content` | `/var/www/html/wp-content` |
| `./wordpress` | `/var/www/html` |
