# CLAUDE.md — Project Root

## What this is

A WordPress development environment running in Docker. The theme and plugin source live in `wp-content/`. WordPress core is mounted read-only from `./wordpress/` (not committed — pulled via Composer or downloaded separately).

## Docker stack (`compose.yml`)

| Service | Image | Purpose | Ports |
|---|---|---|---|
| `nginx` | `nginx:1.27-alpine` | Reverse proxy + static file serving | 80 |
| `wordpress` | Custom (built from `docker/wordpress/Dockerfile`) | PHP-FPM 8.3 + XDebug (Alpine) | — |
| `db` | `mariadb:11.8` | Database | 3306 (localhost only) |
| `phpmyadmin` | `phpmyadmin` | DB GUI | 8000 (localhost only) |
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

## Developer Notes

- When making commits, always check the README.md and CHANGELOG.md files at root of project for any needed updates.
- Always check for accessibility when developing new features.
- Always check for security when developing new features.
- When creating new blocks check for the pattern used on previous blocks and follow those patterns.
- When making CSS/SCSS follow the BEM naming convention.
- Always be sure code passes linting tools. WPCS for PHP, stylelint and prettier for CSS/SCSS, ESLint & prettier for JavaScript.
- The theme location in the docker cli_tools container is here: /home/webdev/www/public_html/wp-content/themes/core-wp
