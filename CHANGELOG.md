# Changelog

All notable changes to this project will be documented in this file.

## [#.#.#] - Next

### Changed

- `compose.yml` — `wordpress` service now builds from a custom Dockerfile; `cli_tools` updated to `digitalblake/light-cli:5.0.0`
- `.vscode/launch.json` — updated XDebug port from `9000` (XDebug 2) to `9003` (XDebug 3) and fixed path mappings to match actual Docker volume mounts (`/var/www/html/wp-content` → `./wp-content`, `/var/www/html` → `./wordpress`)
- `wp-content/themes/core-wp/functions.php` — removed legacy `add_theme_support('html5')`, `add_theme_support('editor-color-palette')`, and `register_sidebar()` calls; added `add_theme_support('wp-block-styles')`; color palette moved to `theme.json`

### Added

- `docker/wordpress/Dockerfile` — custom WordPress image that installs and enables the XDebug PHP extension via `pecl`
- `docker/wordpress/xdebug.ini` — XDebug 3 configuration (debug mode, connects to `host.docker.internal:9003`)
- `wp-content/themes/core-wp/theme.json` — global styles configuration for block theme: color palette, layout sizes, and typography scale
- `wp-content/themes/core-wp/templates/` — block template HTML files replacing the PHP template hierarchy (`index`, `single`, `page`, `archive`, `search`, `404`, `front-page`, `blank`)
- `wp-content/themes/core-wp/parts/` — block template parts replacing `header.php` and `footer.php` (`header.html`, `footer.html`)
- `wp-content/themes/core-wp/patterns/` — registered block patterns replacing PHP template parts (`content-post`, `content-page`, `content-search`, `content-none`)

### Removed

- `wp-content/themes/core-wp/*.php` — all root-level PHP template files replaced by `templates/*.html`
- `wp-content/themes/core-wp/template-parts/` — PHP template parts replaced by `parts/` and `patterns/`
- `wp-content/themes/core-wp/page-templates/` — custom page templates replaced by `templates/`
