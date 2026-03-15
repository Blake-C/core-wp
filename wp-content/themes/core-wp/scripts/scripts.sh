#!/usr/bin/env sh
# scripts.sh — JavaScript bundling pipeline
#
# Steps:
#   1. Clean output directory
#   2. Prettier formatting on source JS
#   3. Webpack bundling

set -e

echo "Cleaning scripts output..."
rm -rf ./assets/js/*

echo "Formatting with Prettier..."
prettier --write --log-level warn './theme_components/js/**/*.js'

echo "Bundling JS..."
webpack --env output="./assets/js"

echo "Completed: Scripts Task"
