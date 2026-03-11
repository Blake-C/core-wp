# Changelog

All notable changes to this project will be documented in this file.

## [#.#.#] - Next

### Changed

- `compose.yml` — `wordpress` service now builds from a custom Dockerfile instead of pulling the official image directly
- `.vscode/launch.json` — updated XDebug port from `9000` (XDebug 2) to `9003` (XDebug 3) and fixed path mappings to match actual Docker volume mounts (`/var/www/html/wp-content` → `./wp-content`, `/var/www/html` → `./wordpress`)

### Added

- `docker/wordpress/Dockerfile` — custom WordPress image that installs and enables the XDebug PHP extension via `pecl`
- `docker/wordpress/xdebug.ini` — XDebug 3 configuration (debug mode, connects to `host.docker.internal:9003`)
