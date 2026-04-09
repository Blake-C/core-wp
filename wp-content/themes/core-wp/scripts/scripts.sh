#!/usr/bin/env sh
# scripts.sh — JavaScript bundling pipeline
#
# Steps:
#   1. Prettier formatting on source JS  [skipped when SKIP_LINT=1]
#   2. Webpack bundling (output dir is cleaned by webpack output.clean: true)
#
# BUILD_MODE=production uses full Terser minification + full source maps.
# Default (development) skips Terser for faster rebuilds.
#
# SKIP_LINT=1 — skips prettier. Set automatically by scripts:watch so that
#               formatter writes do not re-trigger the chokidar watcher and
#               cause concurrent builds.

set -e

WEBPACK_MODE="${BUILD_MODE:-development}"

if [ "${SKIP_LINT:-}" != "1" ]; then
	echo "Formatting with Prettier..."
	prettier --write --log-level warn './theme_components/js/**/*.js'
fi

echo "Bundling JS (mode: ${WEBPACK_MODE})..."
webpack --env output="./assets/js" --env mode="${WEBPACK_MODE}"

echo "Completed: Scripts Task"
