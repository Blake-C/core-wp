#!/usr/bin/env sh
# scripts.sh — JavaScript bundling pipeline
#
# Steps:
#   1. Clean output directory
#   2. Prettier formatting on source JS
#   3. Webpack bundling
#
# BUILD_MODE=production uses full Terser minification + full source maps.
# Default (development) skips Terser for faster rebuilds.

set -e

WEBPACK_MODE="${BUILD_MODE:-development}"

echo "Cleaning scripts output..."
rm -rf ./assets/js/*

echo "Formatting with Prettier..."
prettier --write --log-level warn './theme_components/js/**/*.js'

echo "Bundling JS (mode: ${WEBPACK_MODE})..."
webpack --env output="./assets/js" --env mode="${WEBPACK_MODE}"

echo "Completed: Scripts Task"
