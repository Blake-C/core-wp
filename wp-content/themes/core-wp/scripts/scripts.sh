#!/usr/bin/env sh
# scripts.sh — JavaScript bundling pipeline
#
# Steps:
#   1. Clean output directory
#   2. Prettier formatting on source JS
#   3. Webpack bundling + Modernizr generation (run in parallel)

set -e

echo "Cleaning scripts output..."
rm -rf ./assets/js/*

echo "Formatting with Prettier..."
prettier --write --log-level warn './theme_components/js/**/*.js'

echo "Bundling JS and generating Modernizr (parallel)..."
webpack --env output="./assets/js" &
modernizr -c modernizr-config.json -d ./assets/js/vendors/modernizr.js &
wait

echo "Completed: Scripts Task"
