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
| `pnpm run scripts` | Bundle JS (Prettier → Webpack → Modernizr) |
| `pnpm run images` | Copy and optimize images via ImageMagick |
| `pnpm run static:assets` | Copy fonts and icons |
| `pnpm run watch` | Watch mode for styles, scripts, images, and static assets |
| `pnpm run serve` | Watch + Browser-Sync livereload proxy at `http://localhost` |

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

## Stack

### Docker Services

| Service | Image | Port | Notes |
| --- | --- | --- | --- |
| WordPress (PHP 8.3) | Custom — `docker/wordpress/Dockerfile` | `80` | XDebug 3 installed |
| Database | `mariadb:12` | `3306` | — |
| phpMyAdmin | `phpmyadmin` | `8000` | — |
| CLI Tools | `digitalblake/light-cli:4.0.1` | — | pnpm, WP-CLI, Composer, ImageMagick |

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
| Modernizr | Feature detection bundle |
| ImageMagick | Image optimization via `mogrify` |

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details on recent updates.
