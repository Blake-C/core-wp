# Changelog

All notable changes to this project will be documented in this file.

## [#.#.#] - Next

### Changed

- Updated `wordpress` service to build from a custom Dockerfile; `cli_tools` updated to `digitalblake/light-cli:5.0.0` - `compose.yml`
- Updated XDebug port from `9000` (XDebug 2) to `9003` (XDebug 3); fixed path mappings to match Docker volume mounts - `.vscode/launch.json`
- Removed legacy `add_theme_support` and `register_sidebar` calls; added `add_theme_support('wp-block-styles')`; moved color palette to `theme.json` - `functions.php`
- Updated MariaDB to v11.8 LTS - `compose.yml`
- Added health checks for `wordpress` and `db` services; `wordpress` now depends on `db` being healthy - `compose.yml`
- Added `XDEBUG_MODE: debug` environment variable to control XDebug without rebuilding - `compose.yml`
- Changed `xdebug.mode` default to `off`; mode now controlled by the `XDEBUG_MODE` env var - `docker/wordpress/xdebug.ini`
- Added `add_theme_support('html5', [...])`, `add_theme_support('custom-logo')`, and pattern category registration - `functions.php`
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
