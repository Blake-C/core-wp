# Changelog

All notable changes to this project will be documented in this file.

## [#.#.#] - Next

### Security

- Upgraded nginx image from `nginx:1.27-alpine` to `nginx:1.29-alpine` — resolves critical `libxml2` CVEs (Expired Pointer Dereference, Out-of-bounds Read) and high `libpng`, `expat`, `openssl` vulnerabilities — `compose.yml`, `compose.prod.yml`
- Added `apk upgrade --no-cache` to WordPress dev and prod Dockerfiles — patches upstream Alpine packages including `imagemagick` and `expat` CVEs — `docker/wordpress/Dockerfile`, `docker/wordpress/Dockerfile.prod`

### Added

- Added nginx service (`nginx:1.27-alpine`) as reverse proxy + static file server — dev (`compose.yml`) and prod (`compose.prod.yml`)
- Added dev nginx config (`docker/nginx/nginx.conf`) — WordPress permalink routing, FPM proxy, static file serving, upload size limit, httpoxy mitigation, uploaded-file PHP execution protection
- Added production nginx config (`docker/nginx/nginx.prod.conf`) — all dev settings plus `server_tokens off`, gzip, security headers (X-Frame-Options, X-Content-Type-Options, XSS-Protection, Referrer-Policy), rate limiting on `wp-login.php`, blocked `xmlrpc.php`, blocked PHP execution in uploads, immutable cache headers
- Added `webroot` named volume to prod compose — populated from WordPress image on first start; shared read-only with nginx for static file serving
- Added Midline CTA block patterns — 4 layouts × 2 color modes (`patterns/`)

### Changed

- Switched WordPress dev image from `wordpress:6.9-php8.3` (Apache) to `wordpress:6.9.4-php8.3-fpm-alpine` (PHP-FPM) — `docker/wordpress/Dockerfile`
- Switched WordPress prod image from `wordpress:6.9-php8.3` to `wordpress:6.9.4-php8.3-fpm-alpine` — `docker/wordpress/Dockerfile.prod`
- Updated xdebug install to use `apk` + `$PHPIZE_DEPS` for Alpine compatibility; pinned to `xdebug-3.4.2` — `docker/wordpress/Dockerfile`
- Moved HTTP port `80` from `wordpress` service to `nginx` service; `wordpress` now only exposes FPM on port `9000` — `compose.yml`, `compose.prod.yml`
- Updated wordpress service healthcheck from `curl http://localhost/` to `nc -z 127.0.0.1 9000` (TCP check against FPM socket) — `compose.yml`, `compose.prod.yml`
- Updated browser-sync Docker proxy target from `http://wordpress` to `http://nginx` — `bs-config.cjs`
- Restricted MariaDB port binding to `127.0.0.1:3306` (localhost only) — `compose.yml`
- Restricted phpMyAdmin port binding to `127.0.0.1:8000` (localhost only) — `compose.yml`
- Pinned `composer/installers` from `*` to `^2.3` — `wp-content/composer.json`

## [0.0.0] - 2026-03-16

### Changed

- Updated `wordpress` service to build from a custom Dockerfile; `cli_tools` updated to `digitalblake/light-cli:5.0.0` - `compose.yml`
- Updated XDebug port from `9000` (XDebug 2) to `9003` (XDebug 3); fixed path mappings to match Docker volume mounts - `.vscode/launch.json`
- Removed legacy `add_theme_support` and `register_sidebar` calls; added `add_theme_support('wp-block-styles')`; moved color palette to `theme.json` - `functions.php`
- Updated MariaDB to v11.8 LTS - `compose.yml`
- Added health checks for `wordpress` and `db` services; `wordpress` now depends on `db` being healthy - `compose.yml`
- Added `XDEBUG_MODE: debug` environment variable to control XDebug without rebuilding - `compose.yml`
- Changed `xdebug.mode` default to `off`; mode now controlled by the `XDEBUG_MODE` env var - `docker/wordpress/xdebug.ini`
- Added `add_theme_support('html5', [...])` and pattern category registration - `functions.php`
- Added `no-js` class to `<html>` via `language_attributes` filter; JS swaps it to `js` on load - `inc/components/body-classes.php`
- Added `clearfix` mixin replacing Foundation's version - `theme_components/sass/helpers/_mixins.scss`
- Replaced `@include button()` Foundation mixin with explicit CSS - `theme_components/sass/editor/_general-styles.scss`
- Removed `foundation-everything()` block; file now contains only `@use` imports - `theme_components/sass/editor-styles.scss`
- Added `:focus` state to `.screen-reader-text` and `.skip-link-target:focus-visible` outline - `theme_components/sass/partials/_generic-styles.scss`
- Added `anchor: main-content` to main group block across all templates; added `tagName: main` to front-page - `templates/*.html`
- Added skip link as `wp:html` block before the header group - `parts/header.html`
- Replaced `_skip-link-focus-fix` import with `_skip-link` module; added `no-js`/`js` class swap - `theme_components/js/global-scripts.js`
- Updated PHP constraint from `~8.0` to `~8.3` to match Docker image - `wp-content/composer.json`
- Updated MariaDB version and added pre-commit hook activation step to setup instructions - `README.md`

### Added

- Added custom WordPress image installing and enabling XDebug via `pecl` - `docker/wordpress/Dockerfile`
- Added XDebug 3 configuration connecting to `host.docker.internal:9003` - `docker/wordpress/xdebug.ini`
- Added global styles configuration for block theme: color palette, layout sizes, and typography scale - `theme.json`
- Added block template HTML files replacing the PHP template hierarchy (`index`, `single`, `page`, `archive`, `search`, `404`, `front-page`, `blank`) - `templates/`
- Added block template parts replacing `header.php` and `footer.php` - `parts/`
- Added registered block patterns replacing PHP template parts (`content-post`, `content-page`, `content-search`, `content-none`) - `patterns/`
- Added pre-commit hook running phpcs, eslint, and stylelint on staged files - `.githooks/pre-commit`
- Added `render_block` filter injecting `tabindex="-1"` and `skip-link-target` class onto `<main>` - `inc/components/skip-link-tabindex.php`
- Added skip link hide-after-activation module - `theme_components/js/modules/_skip-link.js`
- Added `@media print` styles - `theme_components/sass/partials/_print.scss`
- Added hero, CTA, and feature-grid block patterns in the `core-wp-sections` category - `patterns/hero.php`, `patterns/cta.php`, `patterns/feature-grid.php`

### Removed

- Removed all root-level PHP template files replaced by `templates/*.html` - `themes/core-wp/*.php`
- Removed PHP template parts replaced by `parts/` and `patterns/` - `template-parts/`
- Removed custom page templates replaced by `templates/` - `page-templates/`
- Removed obsolete IE/old WebKit skip link focus fix - `theme_components/js/modules/_skip-link-focus-fix.js`
- Removed redundant Babel config superseded by inline config in `webpack.config.mjs` - `.babelrc`
- Removed legacy ESLint config superseded by flat config - `.eslintrc.json`
