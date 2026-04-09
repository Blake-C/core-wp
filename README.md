# Core WP Developer Framework

Core WP is a WordPress starter framework for building custom block themes using modern tooling. It uses Full Site Editing (FSE), current build tooling, and a Docker-first local development workflow.

---

## TL;DR

These steps are for initial project setup only. After setup, `docker compose up -d` is all you need.

### Setup

```bash
git clone https://github.com/Blake-C/core-wp.git your-project-name
cd your-project-name
rm -rf .git
git init && git add -A
git commit -m "Initial Commit"
git config core.hooksPath .githooks
docker compose up -d
```

Then enter the CLI container:

```bash
docker compose exec cli_tools zsh
```

### Install Plugins

WordPress core is provided by the Docker image. Plugins are managed with Composer from `wp-content/`:

```bash
cd wp-content
composer install
```

Add plugins from [wpackagist.org](https://wpackagist.org) by adding them to `wp-content/composer.json` and running `composer update`.

### Initialize WordPress

Run the interactive setup script from inside the CLI container:

```bash
wp-init
```

This will:

- Run `pnpm install` and `pnpm run build` in the theme
- Configure WordPress via WP-CLI with your supplied credentials (site title, admin user, timezone)
- Set a homepage and pretty URLs
- Assign the `core-wp` theme and create a main navigation menu
- Remove default themes (`twentytwentyfive`, `twentytwentyfour`, `twentytwentythree`)
- Remove default plugins (`hello`, `akismet`)

### Load Theme Unit Test Data

From inside the CLI container:

```bash
wp-theme-unit-data
```

This imports the [WordPress theme unit test data](https://github.com/WPTT/theme-unit-test) for testing the theme against standard content types.

---

## Access

| Resource | URL |
| --- | --- |
| Site | http://localhost |
| phpMyAdmin | http://localhost:8000 |
| XDebug port | `9003` |

**phpMyAdmin credentials:** server: `db`, user and password from your `.env` file.

---

## Theme Architecture

Core WP uses the WordPress block theme structure (Full Site Editing):

```
wp-content/themes/core-wp/
├── assets/               # Compiled output (git-ignored)
├── parts/                # Block template parts
│   ├── header.html
│   └── footer.html
├── patterns/             # Registered block patterns
│   ├── content-post.php
│   ├── content-page.php
│   ├── content-search.php
│   └── content-none.php
├── templates/            # Block templates (replaces PHP template hierarchy)
│   ├── index.html
│   ├── single.html
│   ├── page.html
│   ├── archive.html
│   ├── search.html
│   ├── 404.html
│   ├── front-page.html
│   └── blank.html
├── theme_components/     # Source files
│   ├── sass/
│   ├── js/
│   ├── images/
│   ├── fonts/
│   └── icons/
├── theme.json            # Global styles: color palette, layout, typography
├── functions.php
└── package.json
```

Global styles (color palette, layout sizes, font scale) are defined in `theme.json` rather than `functions.php`.

---

## Build Commands

All `pnpm` commands run inside the `cli_tools` Docker container:

```bash
docker compose exec cli_tools zsh
cd wp-content/themes/core-wp
```

| Command | Description |
| --- | --- |
| `pnpm run build` | Full build — styles, scripts, images, static assets |
| `pnpm run styles` | Compile SCSS → CSS (lint → Sass → PostCSS → rename) |
| `pnpm run scripts` | Bundle JS (Prettier → Webpack) |
| `pnpm run images` | Copy and optimize images via ImageMagick |
| `pnpm run static:assets` | Copy fonts and icons |
| `pnpm run watch` | Watch mode for styles, scripts, images, and static assets |
| `pnpm run serve` | Watch + Browser-Sync livereload (CSS injected without reload) |
| `pnpm run serve:all` | Same as `serve` plus PHP (PHPCS/PHPCBF) and HTML file watching |

### Browser-Sync Ports

Browser-Sync is environment-aware via `bs-config.cjs`:

| Environment | URL | Notes |
| --- | --- | --- |
| Docker (inside container) | `http://localhost:3000` | Proxies the `nginx` service |
| Local (outside container) | `http://localhost:3010` | Proxies `http://localhost` |

CSS changes are injected directly into the browser without a full page reload. All other changes (JS, PHP, HTML, images) trigger a full reload.

### Aliases (inside CLI container)

```bash
theme             # cd into the theme root
theme_components  # cd into theme_components/
```

---

## PHP Debugging (XDebug)

XDebug 3 is installed in the `wordpress` container via `docker/wordpress/Dockerfile`. To debug:

1. Install the [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug) extension in VSCode
2. Start the **"Listen for XDebug"** configuration (`F5`)
3. Load any page at `http://localhost`

XDebug connects to your host on port `9003`. Path mappings in `.vscode/launch.json` are pre-configured.

---

## Importing an Existing Site

```bash
git clone your-project.git
cd your-project
docker compose up -d
docker compose exec cli_tools zsh
```

Then:

1. Copy your `plugins/` and `uploads/` into `wp-content/`
2. Import the database via phpMyAdmin at `http://localhost:8000`
3. Run a search-replace for the production URL:

```bash
wp search-replace https://production.com http://localhost --precise --all-tables
```

4. Activate the theme and run the build:

```bash
wp theme activate core-wp
cd wp-content/themes/core-wp
pnpm install && pnpm run build
```

### Import Troubleshooting

- If `wp search-replace` fails, temporarily rename the `plugins/` directory and retry
- Disable caching, security, and mailing plugins while working locally (WP Rocket, Wordfence, etc.)
- If phpMyAdmin won't accept your database file, increase `UPLOAD_LIMIT` in `compose.yml` (default: `1500M`)

---

## When Done

```bash
docker compose down
```

---

## Production Deployment

Production uses `compose.prod.yml` with a multi-stage Docker build. WordPress core comes from the official image; theme assets are compiled and plugins installed inside the build — no bind mounts, no dev tools in the final image.

### First-time Server Setup

Requirements: a server with Docker and Docker Compose installed, and Git access to the repo.

```bash
git clone your-project.git
cd your-project
cp .env-example .env
nano .env        # fill in production credentials
chmod 600 .env   # restrict to owner only
./deploy.sh
```

### Deploying Updates

```bash
git pull
./deploy.sh
```

`deploy.sh` always pulls the latest base images (`--pull`), rebuilds the WordPress image with the latest code, and restarts the services with zero manual steps.

### What persists across deploys

Named Docker volumes survive rebuilds and container replacements:

| Volume | Contents |
| --- | --- |
| `webroot` | WordPress core + compiled theme/plugin files — shared with nginx for static file serving |
| `uploads` | `wp-content/uploads/` — user-uploaded media |
| `db_data` | MariaDB data directory |

All other content (theme, plugins, PHP code) is baked into the image and replaced on every deploy.

> **Note:** `webroot` is populated from the WordPress image on first start. On redeploy, the volume is not automatically refreshed. To pick up new image content, run `docker compose -f compose.prod.yml down -v && ./deploy.sh`.

### HTTPS

`compose.prod.yml` serves on port `80` only. For HTTPS, place a reverse proxy (Certbot, Traefik, or a load balancer) in front of the stack to handle SSL termination. The internal nginx container does not need to change.

### Rolling back

To roll back to a previous build, re-tag or re-run the previous image:

```bash
docker compose -f compose.prod.yml down
# restore the previous image tag, then:
docker compose -f compose.prod.yml up -d
```

---

## Troubleshooting

### Docker / Services

**Port already in use (80, 3306, or 8000)**

Another process on your machine is using the port. Stop it, or change the host-side port in `compose.yml` (e.g. `8080:80`).

**WordPress container keeps restarting**

The database health check hasn't passed yet. Wait ~30 seconds, or check logs:

```bash
docker compose logs db
```

**Changed `.env` credentials and the database won't connect**

MariaDB initializes its root password and database on first run. If you change credentials after the first `docker compose up`, delete the persisted data and recreate:

```bash
docker compose down
rm -rf data/mysql
docker compose up -d
```

### Build Tools

**`pnpm: command not found`**

Build commands must run inside the `cli_tools` container, not on your host machine:

```bash
docker compose exec cli_tools zsh
cd wp-content/themes/core-wp
pnpm run build
```

**Build fails with missing packages**

Run `pnpm install` from the theme directory first.

### Pre-commit Hooks

**Hooks not running on commit**

The hooks path must be configured once per clone:

```bash
git config core.hooksPath .githooks
```

**Hook fails: `node: command not found`**

The hook auto-detects nvm. If you use a non-standard Node install, ensure `node` is on your `PATH` before committing.

**Hook fails with PHPCS errors**

Run `composer install` from `wp-content/` first to install PHPCS and the coding standards.

### XDebug

**Breakpoints not being hit**

1. Confirm `XDEBUG_MODE: debug` is set (not commented out) in `compose.yml`
2. Install the [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug) VSCode extension
3. Start the **"Listen for XDebug"** configuration (`F5`) before loading the page

**Check the XDebug log**

```bash
docker compose exec wordpress cat /tmp/xdebug.log
```

### WordPress / WP-CLI

**`wp` commands not found**

WP-CLI runs inside the `cli_tools` container:

```bash
docker compose exec cli_tools zsh
wp option get siteurl
```

**Site not loading after database import**

The imported database still has the production URL. Run a search-replace:

```bash
wp search-replace https://production.com http://localhost --precise --all-tables
```

---

## Stack

### Docker Services

| Service | Image | Port | Notes |
| --- | --- | --- | --- |
| nginx | `nginx:1.29-alpine` | `80` | Reverse proxy + static file serving |
| WordPress (PHP-FPM 8.3) | Custom — `docker/wordpress/Dockerfile` | — | FPM Alpine; XDebug 3 installed |
| Database | `mariadb:11.8` | `3306` (localhost only) | — |
| Redis | `redis:7-alpine` | — | Object cache; LRU eviction, no persistence |
| phpMyAdmin | `phpmyadmin` | `8000` (localhost only) | — |
| CLI Tools | `digitalblake/light-cli:5.0.0` | — | pnpm, WP-CLI, Composer, ImageMagick |

XDebug 3 is baked into the WordPress service image (`docker/wordpress/Dockerfile`) and listens on port `9003`. See the [PHP Debugging](#php-debugging-xdebug) section for VSCode setup.

### CLI Tools (`digitalblake/light-cli`)

| Package | Version |
| --- | --- |
| PHP | 8.3 |
| Node | 24 |
| pnpm | 10.32.1 |
| WP-CLI | latest |
| Composer | latest |
| ImageMagick | 7.1.2 (with JPEG support) |
| zsh | 5.9 |

### Theme Build Tools

| Tool | Purpose |
| --- | --- |
| Webpack 5 + Babel | JS bundling, ES2020 transpilation |
| Dart Sass 1.98 | SCSS compilation |
| PostCSS + cssnano | CSS autoprefixing and minification |
| Prettier | JS + SCSS formatting |
| ESLint 10 | JS linting (flat config) |
| Stylelint 16 | SCSS linting |
| Browser-Sync 3 | Live reload proxy with CSS injection |
| ImageMagick | Image optimization via `mogrify` |

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details on recent updates.
