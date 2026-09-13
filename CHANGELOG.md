# Changelog

All notable changes to this project will be documented in this file.

## [#.#.#] - Next

### Security

- Upgraded pnpm from v10 to v12.3.4. pnpm v11 replaced the `onlyBuiltDependencies` setting with the `allowBuilds` allowlist, which names the only dependencies allowed to run install scripts (`wp-content/themes/core-wp/package.json`, `wp-content/themes/core-wp/pnpm-workspace.yaml`)
- Created `pnpm-workspace.yaml` with these supply-chain settings (`wp-content/themes/core-wp/pnpm-workspace.yaml`):
  - `frozenLockfile`, `saveExact`, and `verifyStoreIntegrity`
  - `minimumReleaseAge: 10080`, which refuses package versions published less than 7 days ago
  - `blockExoticSubdeps`, which blocks transitive dependencies that resolve to a git repository or a tarball URL
  - `trustPolicy: no-downgrade`, which refuses a version whose publisher trust evidence is weaker than an earlier release of the same package
  - `verifyDepsBeforeRun: error`, which stops `pnpm run` and `pnpm exec` when `node_modules` does not match the lockfile
  - `engineStrict`, which refuses a dependency that declares itself incompatible with the running Node version
  - an `allowBuilds` allowlist that lets only `@parcel/watcher` run its build script
- Removed `confirmModulesPurge` and `strictDeprecatedDependencies` from `pnpm-workspace.yaml` and renamed `auditLevel` to `audit.level`. pnpm 12 fails a command on a setting it does not recognize, and it reports `confirmModulesPurge` as a pnpm v11 setting. `strictDeprecatedDependencies` was never a pnpm setting (`wp-content/themes/core-wp/pnpm-workspace.yaml`)
- Migrated pnpm settings from `.npmrc` to `pnpm-workspace.yaml`, because pnpm v11 and later read only auth and registry settings from `.npmrc` (`wp-content/themes/core-wp/.npmrc`)
- Added `ua-parser-js@1.0.41` to `trustPolicyExclude`. browser-sync requires `ua-parser-js ^1`, and the same maintainer publishes the MIT 1.x line without provenance and the AGPL 2.x line with it, so pnpm reads 1.0.41 as a trust downgrade (`wp-content/themes/core-wp/pnpm-workspace.yaml`)
- Forced patched transitive dependencies through pnpm `overrides` (`wp-content/themes/core-wp/pnpm-workspace.yaml`, `wp-content/themes/core-wp/pnpm-lock.yaml`):
  - `ws` 8.20.1 or later (CVE-2026-45736)
  - `js-yaml` 4.3.2 or later (CVE-2026-53550, GHSA-5p4m-2wfm-xmqj, GHSA-2883-xcg3-v3hh)
  - `postcss-selector-parser` 6.1.3 or later (CVE-2026-9358)
  - `brace-expansion` 5.0.9 or later (CVE-2026-13149, GHSA-mh99-v99m-4gvg, GHSA-rgw5-rvv9-x895)
  - `fast-uri` 3.1.6 or later (CVE-2026-13676 and six GHSA advisories for host confusion and SSRF)
  - `postcss` 8.5.28 or later for the copy `@stylistic/stylelint-plugin` pulls in (GHSA-r28c-9q8g-f849, GHSA-fxqj-rqcc-2cmp)
  - `nanoid` 3.3.18 or later (GHSA-28wg-ghj8-5hjv, GHSA-2v37-7h3g-55p8)
  - `socket.io-parser` 4.2.7 or later (GHSA-2m8v-j782-fhvr)
  - `browserslist` 4.28.7 or later (GHSA-c83g-rgw3-j3cx, GHSA-73wf-gq98-2v4g)
  - `baseline-browser-mapping` 2.11.0 or later (GHSA-w5vr-8v7q-w6rv)
  - `colord` 2.9.4 or later (GHSA-2wm5-q62r-hmrv)
  - `immutable` 5.1.8 or later on the 5.x line used by sass, and 3.8.4 or later on the 3.x line used by browser-sync (CVE-2026-59879, CVE-2026-59880)
- Updated the PHPCS toolchain to fix three high-severity advisories, and raised the `composer.json` floors so an update cannot resolve below the fixed versions (`wp-content/composer.json`, `wp-content/composer.lock`):
  - `wp-coding-standards/wpcs` 3.3.0→3.4.1 (CVE-2026-45293, arbitrary code execution)
  - `squizlabs/php_codesniffer` 3.13.5→3.13.6 (CVE-2026-67434, OS command injection)
  - `phpcsstandards/phpcsutils` 1.2.2→1.2.3 (CVE-2026-65954, arbitrary code execution)
- Removed XDebug from the production image. The base image enables the extension, and with no `XDEBUG_MODE` set in production it ran in its default `develop` mode on every request (`docker/wordpress/Dockerfile.prod`)

### Added

- Added `MAINTENANCE.md`, an update and upgrade runbook for pnpm and Composer packages, the `light-cli` and `core-wp-wordpress` images, WordPress, and base images, with the SemVer and changelog conventions to follow (`MAINTENANCE.md`, `README.md`, `CLAUDE.md`)

### Changed

- Updated theme dependencies (`wp-content/themes/core-wp/package.json`):
  - `@wordpress/stylelint-config` 23.33.0→24.3.0
  - `autoprefixer` 10.4.27→10.5.5
  - `core-js` 3.48.0→3.50.0
  - `eslint` 10.0.3→10.10.0
  - `eslint-plugin-prettier` 5.5.5→5.5.6
  - `globals` 17.4.0→17.12.0
  - `postcss` 8.5.10→8.5.28
  - `postcss-cli` 11.0.1→12.0.0
  - `prettier` 3.8.1→3.9.6
  - `sass` 1.98.0→1.104.0
  - `webpack` 5.105.4→5.110.3
  - `webpack-cli` 7.0.0→7.2.3
- Upgraded Babel to 8: `@babel/core` and `@babel/preset-env` 7.29.0→8.0.x. Babel 8 removed the preset-env `useBuiltIns`/`corejs` options, so the equivalent usage-based corejs3 polyfilling now runs through `babel-plugin-polyfill-corejs3` (`method: usage-global`), and the JS bundle output is unchanged (`wp-content/themes/core-wp/package.json`, `wp-content/themes/core-wp/babel.config.json`)
- Updated `cssnano` and `cssnano-preset-advanced` 7.x→8.0.10. The minified CSS grew by about 1%. cssnano 8.0.10 now splits some `padding` shorthands into longhands, merges selectors with `:is()`, and writes 8-digit hex colors, and every browser in the theme's browserslist supports `:is()` and 8-digit hex colors (`wp-content/themes/core-wp/package.json`)
- Updated Composer plugins (`wp-content/composer.json`, `wp-content/composer.lock`):
  - `wp-plugin/wordfence` 8.2.2→9.0.1
  - `wp-plugin/wordpress-seo` 27.6→28.4
  - `wp-plugin/redirection` 5.7.5→5.10.0
  - `wp-plugin/wps-hide-login` 1.9.18→1.9.19
  - `wp-plugin/query-monitor` 4.0.6→4.0.7
- Held these packages back (`MAINTENANCE.md`):
  - `stylelint` at 16.x, because `@wordpress/stylelint-config` 24.3.0 requires `stylelint ^16.8.2`. `@wordpress/stylelint-config` 25.0.0 moves to stylelint 17 and passes the 7-day `minimumReleaseAge` on 2026-09-17.
  - `cssnano` and `cssnano-preset-advanced` at 8.0.10. Releases 8.0.3 to 8.0.10 were published with a maintainer approval and every 9.0.x release was not, so `trustPolicy: no-downgrade` rejects 9.x.
  - `squizlabs/php_codesniffer` at 3.x, because WPCS 3.4.1 requires `^3.13.5`.
- Updated `digitalblake/light-cli` image to `6.4.0` (Alpine 3.24.1, Node 24.18.1, PHP 8.4.25, Composer 2.10.3, pnpm 12.3.4). The image installs npm 11.19.1 as a pinned global and removes Alpine's npm package, which bundled tar, sigstore, and pacote versions that Snyk rates as high severity. Bumped the dev `cli_tools` reference and both `Dockerfile.prod` builder stages (`compose.yml`, `docker/wordpress/Dockerfile.prod`)
- Standardized pnpm on 12.3.4 across the image and theme. The image pins it through Corepack with `COREPACK_DEFAULT_TO_LATEST=0`, and the theme pins it in the `packageManager` field (`wp-content/themes/core-wp/package.json`, `wp-content/themes/core-wp/pnpm-lock.yaml`)
- Fixed the `Dockerfile.prod` theme build stage, which failed with `ERR_PNPM_LOCKFILE_CONFIG_MISMATCH` because it installed without `pnpm-workspace.yaml`. The stage now copies `pnpm-workspace.yaml` and `.npmrc` with the lockfile, owned by `webdev`, so the production install applies the overrides and supply-chain settings (`docker/wordpress/Dockerfile.prod`)
- Fixed production deploys shipping stale code. Docker copies image files into the `webroot` volume only when the volume is created, so `deploy.sh` kept serving the previous build. `deploy.sh` now removes and recreates only `webroot`, and the `compose.prod.yml` comment no longer recommends `docker compose down -v`, which also deletes the `db_data` and `uploads` volumes (`deploy.sh`, `compose.prod.yml`, `README.md`)
- Fixed the "Cannot load Xdebug - it was already loaded" warning in the dev container by removing the `zend_extension` line from `xdebug.ini`, because the image already loads the extension through `docker-php-ext-xdebug.ini` (`docker/wordpress/xdebug.ini`, `CLAUDE.md`)
- Replaced `parse_url()` with `wp_parse_url()` in the Docker loopback mu-plugin and cleared its remaining PHPCS warnings (`wp-content/mu-plugins/docker-loopback.php`)
- Fixed the nginx healthcheck so the container no longer reports unhealthy when WordPress answers the check with a canonical 301 redirect away from `127.0.0.1`. The dev check sends `Host: localhost` so WordPress returns a 200, and the prod check accepts any served `200/301/302` response because its canonical host is unknown at build time (`compose.yml`, `compose.prod.yml`)
- Bumped base images: `nginx` 1.29-alpine→1.31-alpine, `redis` 7-alpine→8-alpine, and `mariadb` 11.8→12.3 LTS. Both compose files set `MARIADB_AUTO_UPGRADE` so the image runs `mariadb-upgrade` on an existing data directory (`compose.yml`, `compose.prod.yml`)
- Updated the WordPress environment to 7.1 through the rebuilt `digitalblake/core-wp-wordpress` image `php8.4-1.2.1` (Alpine 3.24.1, PHP 8.4.25, XDebug 3.4.7→3.5.3, `apk upgrade` for base CVEs). The rolling `php8.4-latest` tag is unchanged. The image entrypoint copies core only into an empty `./wordpress` directory, so an existing install updates through `wp core update` or WordPress automatic updates (`compose.yml`, `docker/wordpress/Dockerfile.prod`)

### Removed

## [0.0.3] - 2026-04-30

### Security

- Switched WordPress dev and prod images to `digitalblake/core-wp-wordpress:php8.4-latest` - centralised image with `apk upgrade` and XDebug baked in; security patches now applied once in the published image rather than per-project at build time - `compose.yml`, `docker/wordpress/Dockerfile.prod`

### Changed

- Replaced per-project WordPress `build:` in `compose.yml` with `image: digitalblake/core-wp-wordpress:php8.4-latest`; PHP config files (`xdebug.ini`, `php-opcache-dev.ini`, `www.conf`) are now volume-mounted so all projects share one cached image - `compose.yml`
- Updated `Dockerfile.prod` stage 3 base from `wordpress:php8.4-fpm-alpine` + `apk upgrade` to `digitalblake/core-wp-wordpress:php8.4-latest` - `docker/wordpress/Dockerfile.prod`
- Updated `Dockerfile.prod` builder stages from `digitalblake/light-cli:5.0.0` to `6.0.2` - `docker/wordpress/Dockerfile.prod`

### Removed

- Removed `docker/wordpress/Dockerfile` - no longer needed; dev environment pulls directly from the published image

## [0.0.2] - 2026-04-10

### Security

- Replaced `phpcs:ignore` bypass on echo with `wp_kses()` for safe HTML output - `inc/components/site-icons.php`
- Added `esc_attr()` escaping to `$label` in password form attributes - `inc/components/password-form.php`
- Fixed Docker loopback Host header causing WordPress canonical redirect loop; added PHPCS config for mu-plugins directory - `wp-content/mu-plugins/`

### Added

- Added tabs block with mobile accordion conversion on small screens - `inc/blocks/tab-item/`
- Added accordion block - `inc/blocks/accordion-item/`
- Added copyright custom block (converted from shortcode) - `inc/blocks/copyright/`
- Added social share custom block (converted from shortcode) - `inc/blocks/social-share/`
- Added related posts block - `inc/blocks/related-posts/`
- Added post featured image fallback and listing card wrap components - `inc/components/`
- Added base pagination styles - `theme_components/sass/layout/_post-listing.scss`
- Added global left/right padding - `theme_components/sass/layout/_body.scss`
- Added `file_exists()` guard and request-level cache to `core_wp_cache_bust()` - `inc/utility-functions.php`
- Added null guard on `get_node()` result - `inc/custom-admin-functions/change-howdy-text.php`
- Added webp to watchable image files in build system - `package.json`

### Changed

- Reformatted post listing styles into BEM naming convention - `theme_components/sass/layout/_post-listing.scss`
- Refactored midline CTA block patterns into BEM naming convention - `patterns/`
- Standardized theme font sizes and spacing; switched from em to variable px units (converted to rem) - `theme_components/sass/`
- Renamed block pattern category from "Sections" to "Core WP" - `functions.php`
- Updated listing pages to display a grid of posts with featured images
- Updated site footer for styling - `parts/footer.html`, `templates/`
- Updated supported browsers in `package.json` to exclude dead browsers
- Adjusted stylelint selector pattern to support BEM naming convention - `.stylelintrc`
- Updated core plugin setup with proper prefix - `wp-content/plugins/core-wp/core-wp.php`
- Changed nginx healthcheck from `localhost` to `127.0.0.1` in dev and prod compose for DNS independence - `compose.yml`, `compose.prod.yml`
- Updated styling readme guide - `theme_components/sass/README.md`
- Updated plugin registry URL from wpackagist.org to wp-packages.org - `README.md`

### Removed

- Removed legacy embed flex-video styles - `theme_components/sass/global-styles.scss`
- Removed old pagination styles - `theme_components/sass/layout/`

## [0.0.1] - 2026-04-09

### Security

- Upgraded nginx image from `nginx:1.27-alpine` to `nginx:1.29-alpine` - resolves critical `libxml2` CVEs (Expired Pointer Dereference, Out-of-bounds Read) and high `libpng`, `expat`, `openssl` vulnerabilities - `compose.yml`, `compose.prod.yml`
- Added `apk upgrade --no-cache` to WordPress dev and prod Dockerfiles - patches upstream Alpine packages including `imagemagick` and `expat` CVEs - `docker/wordpress/Dockerfile`, `docker/wordpress/Dockerfile.prod`
- Added `server_tokens off` to dev nginx config - hides nginx version from response headers - `docker/nginx/nginx.conf`

### Added

- Added nginx service (`nginx:1.27-alpine`) as reverse proxy + static file server - dev (`compose.yml`) and prod (`compose.prod.yml`)
- Added dev nginx config (`docker/nginx/nginx.conf`) - WordPress permalink routing, FPM proxy, static file serving, upload size limit, httpoxy mitigation, uploaded-file PHP execution protection
- Added production nginx config (`docker/nginx/nginx.prod.conf`) - all dev settings plus `server_tokens off`, gzip, security headers (X-Frame-Options, X-Content-Type-Options, XSS-Protection, Referrer-Policy), rate limiting on `wp-login.php`, blocked `xmlrpc.php`, blocked PHP execution in uploads, immutable cache headers
- Added `webroot` named volume to prod compose - populated from WordPress image on first start; shared read-only with nginx for static file serving
- Added Midline CTA block patterns - 4 layouts × 2 color modes (`patterns/`)
- Added Redis service (`redis:7-alpine`) to dev and prod stacks - memory-capped with LRU eviction, RDB persistence disabled - `compose.yml`, `compose.prod.yml`
- Added Redis Object Cache plugin via Composer - `wp-content/composer.json`
- Added Docker loopback fix mu-plugin - resolves DNS resolution issues for WordPress HTTP requests inside the container - `wp-content/mu-plugins/`
- Added PHP-FPM pool config to dev and prod WordPress containers - dynamic process manager with request recycling (`pm.max_requests`) to prevent memory leaks - `docker/wordpress/`
- Added OPcache JIT in tracing mode to prod PHP config - `docker/wordpress/`
- Added PHP-FPM slowlog to dev container - logs requests exceeding 5s for identifying slow scripts - `docker/wordpress/`
- Added `open_file_cache` to dev and prod nginx configs - reduces repeated filesystem stat calls for static assets
- Added FastCGI buffer tuning to dev and prod nginx configs - reduces disk buffering for PHP-FPM responses
- Added keepalive upstream connections to dev nginx config - reduces TCP handshake overhead between nginx and PHP-FPM
- Added `innodb_buffer_pool_instances` to prod MariaDB config - improves concurrency for large buffer pool sizes
- Added `innodb_log_buffer_size` and `thread_cache_size` to dev MariaDB config
- Added `XDEBUG_MODE` variable to `.env-example`
- Added `BUILD_MODE=production` flag support to styles pipeline - disables Sass source maps and strips residual map files - `scripts/styles.sh`
- Added dev/prod mode support to webpack build - source maps in development only - `webpack.config.mjs`
- Added `SKIP_LINT=1` to `scripts:watch` in `package.json` - prevents chokidar feedback loop from formatter writes re-triggering the JS watcher
- Added Babel config at project level - `babel.config.json`

### Changed

- Switched WordPress dev image from `wordpress:6.9-php8.3` (Apache) to `wordpress:6.9.4-php8.3-fpm-alpine` (PHP-FPM) - `docker/wordpress/Dockerfile`
- Switched WordPress prod image from `wordpress:6.9-php8.3` to `wordpress:6.9.4-php8.3-fpm-alpine` - `docker/wordpress/Dockerfile.prod`
- Updated xdebug install to use `apk` + `$PHPIZE_DEPS` for Alpine compatibility; pinned to `xdebug-3.4.2` - `docker/wordpress/Dockerfile`
- Moved HTTP port `80` from `wordpress` service to `nginx` service; `wordpress` now only exposes FPM on port `9000` - `compose.yml`, `compose.prod.yml`
- Updated wordpress service healthcheck from `curl http://localhost/` to `nc -z 127.0.0.1 9000` (TCP check against FPM socket) - `compose.yml`, `compose.prod.yml`
- Updated browser-sync Docker proxy target from `http://wordpress` to `http://nginx` - `bs-config.cjs`
- Restricted MariaDB port binding to `127.0.0.1:3306` (localhost only) - `compose.yml`
- Restricted phpMyAdmin port binding to `127.0.0.1:8000` (localhost only) - `compose.yml`
- Pinned `composer/installers` from `*` to `^2.3` - `wp-content/composer.json`
- Raised OPcache `max_accelerated_files` from 10000 to 20000 in dev and prod PHP configs
- Lowered MariaDB dev slow query threshold from 2s to 0.5s - surfaces slow queries earlier during development
- Disabled OPcache JIT in dev PHP config - JIT active in prod only; avoids JIT-related debugging interference
- Bumped prod Redis memory limit from 128mb to 256mb
- Refactored styles watch pipeline - restricted browser-sync `files` to `global-styles.min.css` only (other stylesheets have no `<link>` on frontend pages and caused fallback full-page reloads cancelling in-flight CSS injection); replaced xargs loop with single `postcss` invocation using `--dir`/`--ext`/`--no-map` against an isolated `assets/css/.src/` intermediate directory; added concurrent build lock and 500ms chokidar debounce - `scripts/styles.sh`, `bs-config.cjs`, `package.json`
- Fixed `images.sh` grep pattern to correctly match file extensions - `scripts/images.sh`
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
