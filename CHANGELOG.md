# Changelog

All notable changes to this project will be documented in this file.

## [#.#.#] - Next

### Security

- Upgraded pnpm from v10 to v11 — mitigates Mini Shai-Hulud supply chain attack vector; pnpm v11's `allowBuilds` allowlist now blocks all postinstall/preinstall scripts by default — `wp-content/themes/core-wp/package.json`, `wp-content/themes/core-wp/pnpm-workspace.yaml`
- Created `pnpm-workspace.yaml` with explicit security settings: `frozenLockfile`, `verifyStoreIntegrity`, `blockExoticSubdeps`, `minimumReleaseAge` (7 days), `allowBuilds: []` — `wp-content/themes/core-wp/pnpm-workspace.yaml`
- Migrated pnpm security config from `.npmrc` to `pnpm-workspace.yaml` (required for pnpm v11) — `wp-content/themes/core-wp/.npmrc`
- Forced transitive `ws` to 8.21.0 via pnpm `overrides` — fixes CVE-2026-45736 (use of uninitialized resource) in the `ws` pulled by `browser-sync` through `socket.io` — `wp-content/themes/core-wp/pnpm-workspace.yaml`
- Forced transitive `js-yaml` to ≥ 4.2.0 (CVE-2026-53550, inefficient algorithmic complexity) and `postcss-selector-parser` to ≥ 6.1.3 (CVE-2026-9358, uncontrolled recursion) via pnpm `overrides` — both flagged by Snyk in the build toolchain — `wp-content/themes/core-wp/pnpm-workspace.yaml`

### Added

- Added `MAINTENANCE.md` — an update/upgrade runbook for pnpm and Composer packages, the `light-cli` and `core-wp-wordpress` images, WordPress, and base images, plus the SemVer and changelog conventions to follow — `MAINTENANCE.md`, `README.md`, `CLAUDE.md`

### Changed

- Updated theme dependencies (patch/minor): `@wordpress/stylelint-config` 23.33.0→23.41.0, `autoprefixer` 10.4.27→10.5.2, `core-js` 3.48.0→3.49.0, `eslint` 10.0.3→10.6.0, `eslint-plugin-prettier` 5.5.5→5.5.6, `globals` 17.4.0→17.7.0, `postcss` 8.5.10→8.5.15, `prettier` 3.8.1→3.9.1, `sass` 1.98.0→1.101.0, `webpack` 5.105.4→5.108.1, `webpack-cli` 7.0.0→7.1.0 — `wp-content/themes/core-wp/package.json`
- Upgraded Babel to 8: `@babel/core` and `@babel/preset-env` 7.29.0→8.0.x. Babel 8 removed the preset-env `useBuiltIns`/`corejs` options, so the equivalent usage-based corejs3 polyfilling now runs through `babel-plugin-polyfill-corejs3` (`method: usage-global`); JS bundle output is unchanged — `wp-content/themes/core-wp/package.json`, `wp-content/themes/core-wp/babel.config.json`
- Updated `cssnano` and `cssnano-preset-advanced` 7.x→8.0.2 (major) — requires Node ≥ 24.11 and PostCSS ≥ 8.5.14, both satisfied; production build pipeline verified — `wp-content/themes/core-wp/package.json`
- Updated Composer plugins: `wp-plugin/wordpress-seo` 27.6→27.9, `wp-plugin/redirection`→5.8.1, `wp-plugin/query-monitor`→4.0.7 — `wp-content/composer.lock`
- Held `stylelint` at 16.x (`@wordpress/stylelint-config@23.41.0` requires `stylelint ^16.8.2`), `squizlabs/php_codesniffer` at 3.x (WPCS 3.x is not compatible with PHP_CodeSniffer 4.0), and `postcss` at 8.5.15 (8.5.16 blocked by the 7-day `minimumReleaseAge` gate)
- Updated `digitalblake/light-cli` image to `6.3.0` (Alpine 3.24.0→3.24.1, Node 24.16→24.17, PHP 8.4.22→8.4.23, Composer 2.10.1→2.10.2, pnpm 11.6.0→11.10.0) — bumped the dev `cli_tools` reference and both `Dockerfile.prod` builder stages — `compose.yml`, `docker/wordpress/Dockerfile.prod`
- Standardized pnpm on 11.10.0 across the image and theme — pinned it deterministically in the image via Corepack (`COREPACK_DEFAULT_TO_LATEST=0`) and bumped the theme's `packageManager` field — `wp-content/themes/core-wp/package.json`
- Fixed the nginx healthcheck so a WordPress canonical 301 redirect from `127.0.0.1` no longer reports the container as unhealthy — dev sends `Host: localhost` for a direct 200; prod accepts any served `200/301/302` response since its canonical host is unknown at build time — `compose.yml`, `compose.prod.yml`
- Bumped base images: `nginx` 1.29-alpine→1.31-alpine and `redis` 7-alpine→8-alpine (`mariadb` held at 11.8 LTS) — `compose.yml`, `compose.prod.yml`
- Updated the WordPress environment to 7.0 via the rebuilt `digitalblake/core-wp-wordpress` image `php8.4-1.2.0` (Alpine 3.24.1, PHP 8.4.23, XDebug 3.4.7→3.5.3, `apk upgrade` for base CVEs); the rolling `php8.4-latest` tag is unchanged, so the upgrade lands on rebuild/pull — `compose.yml`, `docker/wordpress/Dockerfile.prod`

### Removed

## [0.0.3] - 2026-04-30

### Security

- Switched WordPress dev and prod images to `digitalblake/core-wp-wordpress:php8.4-latest` — centralised image with `apk upgrade` and XDebug baked in; security patches now applied once in the published image rather than per-project at build time — `compose.yml`, `docker/wordpress/Dockerfile.prod`

### Changed

- Replaced per-project WordPress `build:` in `compose.yml` with `image: digitalblake/core-wp-wordpress:php8.4-latest`; PHP config files (`xdebug.ini`, `php-opcache-dev.ini`, `www.conf`) are now volume-mounted so all projects share one cached image — `compose.yml`
- Updated `Dockerfile.prod` stage 3 base from `wordpress:php8.4-fpm-alpine` + `apk upgrade` to `digitalblake/core-wp-wordpress:php8.4-latest` — `docker/wordpress/Dockerfile.prod`
- Updated `Dockerfile.prod` builder stages from `digitalblake/light-cli:5.0.0` to `6.0.2` — `docker/wordpress/Dockerfile.prod`

### Removed

- Removed `docker/wordpress/Dockerfile` — no longer needed; dev environment pulls directly from the published image

## [0.0.2] - 2026-04-10

### Security

- Replaced `phpcs:ignore` bypass on echo with `wp_kses()` for safe HTML output — `inc/components/site-icons.php`
- Added `esc_attr()` escaping to `$label` in password form attributes — `inc/components/password-form.php`
- Fixed Docker loopback Host header causing WordPress canonical redirect loop; added PHPCS config for mu-plugins directory — `wp-content/mu-plugins/`

### Added

- Added tabs block with mobile accordion conversion on small screens — `inc/blocks/tab-item/`
- Added accordion block — `inc/blocks/accordion-item/`
- Added copyright custom block (converted from shortcode) — `inc/blocks/copyright/`
- Added social share custom block (converted from shortcode) — `inc/blocks/social-share/`
- Added related posts block — `inc/blocks/related-posts/`
- Added post featured image fallback and listing card wrap components — `inc/components/`
- Added base pagination styles — `theme_components/sass/layout/_post-listing.scss`
- Added global left/right padding — `theme_components/sass/layout/_body.scss`
- Added `file_exists()` guard and request-level cache to `core_wp_cache_bust()` — `inc/utility-functions.php`
- Added null guard on `get_node()` result — `inc/custom-admin-functions/change-howdy-text.php`
- Added webp to watchable image files in build system — `package.json`

### Changed

- Reformatted post listing styles into BEM naming convention — `theme_components/sass/layout/_post-listing.scss`
- Refactored midline CTA block patterns into BEM naming convention — `patterns/`
- Standardized theme font sizes and spacing; switched from em to variable px units (converted to rem) — `theme_components/sass/`
- Renamed block pattern category from "Sections" to "Core WP" — `functions.php`
- Updated listing pages to display a grid of posts with featured images
- Updated site footer for styling — `parts/footer.html`, `templates/`
- Updated supported browsers in `package.json` to exclude dead browsers
- Adjusted stylelint selector pattern to support BEM naming convention — `.stylelintrc`
- Updated core plugin setup with proper prefix — `wp-content/plugins/core-wp/core-wp.php`
- Changed nginx healthcheck from `localhost` to `127.0.0.1` in dev and prod compose for DNS independence — `compose.yml`, `compose.prod.yml`
- Updated styling readme guide — `theme_components/sass/README.md`
- Updated plugin registry URL from wpackagist.org to wp-packages.org — `README.md`

### Removed

- Removed legacy embed flex-video styles — `theme_components/sass/global-styles.scss`
- Removed old pagination styles — `theme_components/sass/layout/`

## [0.0.1] - 2026-04-09

### Security

- Upgraded nginx image from `nginx:1.27-alpine` to `nginx:1.29-alpine` — resolves critical `libxml2` CVEs (Expired Pointer Dereference, Out-of-bounds Read) and high `libpng`, `expat`, `openssl` vulnerabilities — `compose.yml`, `compose.prod.yml`
- Added `apk upgrade --no-cache` to WordPress dev and prod Dockerfiles — patches upstream Alpine packages including `imagemagick` and `expat` CVEs — `docker/wordpress/Dockerfile`, `docker/wordpress/Dockerfile.prod`
- Added `server_tokens off` to dev nginx config — hides nginx version from response headers — `docker/nginx/nginx.conf`

### Added

- Added nginx service (`nginx:1.27-alpine`) as reverse proxy + static file server — dev (`compose.yml`) and prod (`compose.prod.yml`)
- Added dev nginx config (`docker/nginx/nginx.conf`) — WordPress permalink routing, FPM proxy, static file serving, upload size limit, httpoxy mitigation, uploaded-file PHP execution protection
- Added production nginx config (`docker/nginx/nginx.prod.conf`) — all dev settings plus `server_tokens off`, gzip, security headers (X-Frame-Options, X-Content-Type-Options, XSS-Protection, Referrer-Policy), rate limiting on `wp-login.php`, blocked `xmlrpc.php`, blocked PHP execution in uploads, immutable cache headers
- Added `webroot` named volume to prod compose — populated from WordPress image on first start; shared read-only with nginx for static file serving
- Added Midline CTA block patterns — 4 layouts × 2 color modes (`patterns/`)
- Added Redis service (`redis:7-alpine`) to dev and prod stacks — memory-capped with LRU eviction, RDB persistence disabled — `compose.yml`, `compose.prod.yml`
- Added Redis Object Cache plugin via Composer — `wp-content/composer.json`
- Added Docker loopback fix mu-plugin — resolves DNS resolution issues for WordPress HTTP requests inside the container — `wp-content/mu-plugins/`
- Added PHP-FPM pool config to dev and prod WordPress containers — dynamic process manager with request recycling (`pm.max_requests`) to prevent memory leaks — `docker/wordpress/`
- Added OPcache JIT in tracing mode to prod PHP config — `docker/wordpress/`
- Added PHP-FPM slowlog to dev container — logs requests exceeding 5s for identifying slow scripts — `docker/wordpress/`
- Added `open_file_cache` to dev and prod nginx configs — reduces repeated filesystem stat calls for static assets
- Added FastCGI buffer tuning to dev and prod nginx configs — reduces disk buffering for PHP-FPM responses
- Added keepalive upstream connections to dev nginx config — reduces TCP handshake overhead between nginx and PHP-FPM
- Added `innodb_buffer_pool_instances` to prod MariaDB config — improves concurrency for large buffer pool sizes
- Added `innodb_log_buffer_size` and `thread_cache_size` to dev MariaDB config
- Added `XDEBUG_MODE` variable to `.env-example`
- Added `BUILD_MODE=production` flag support to styles pipeline — disables Sass source maps and strips residual map files — `scripts/styles.sh`
- Added dev/prod mode support to webpack build — source maps in development only — `webpack.config.mjs`
- Added `SKIP_LINT=1` to `scripts:watch` in `package.json` — prevents chokidar feedback loop from formatter writes re-triggering the JS watcher
- Added Babel config at project level — `babel.config.json`

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
- Raised OPcache `max_accelerated_files` from 10000 to 20000 in dev and prod PHP configs
- Lowered MariaDB dev slow query threshold from 2s to 0.5s — surfaces slow queries earlier during development
- Disabled OPcache JIT in dev PHP config — JIT active in prod only; avoids JIT-related debugging interference
- Bumped prod Redis memory limit from 128mb to 256mb
- Refactored styles watch pipeline — restricted browser-sync `files` to `global-styles.min.css` only (other stylesheets have no `<link>` on frontend pages and caused fallback full-page reloads cancelling in-flight CSS injection); replaced xargs loop with single `postcss` invocation using `--dir`/`--ext`/`--no-map` against an isolated `assets/css/.src/` intermediate directory; added concurrent build lock and 500ms chokidar debounce — `scripts/styles.sh`, `bs-config.cjs`, `package.json`
- Fixed `images.sh` grep pattern to correctly match file extensions — `scripts/images.sh`
- Removed dead `webpack.config.babel.js` reference from `eslint.config.js`
- Updated digitalblake/light-cli from v5.0.0 to v5.0.1

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
