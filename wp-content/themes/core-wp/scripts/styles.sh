#!/usr/bin/env sh
# styles.sh — SCSS → minified CSS pipeline
#
# Steps:
#   1. Clean output directory              [skipped when SKIP_CLEAN=1]
#   2. Lint: Prettier + Stylelint          [skipped when SKIP_LINT=1]
#   3. Compile SCSS → intermediate CSS via Dart Sass (assets/css/.src/)
#   4. PostCSS (autoprefixer + cssnano) → .min.css in assets/css/
#   5. Remove intermediate .src/ directory
#   6. (Production only) Remove source map files
#
# SKIP_CLEAN=1 — skips rm -rf of assets/css/ so .min.css files are overwritten
#                in place rather than deleted and recreated. Browser-sync then
#                sees a single change event (not unlink+add) and CSS injection
#                stays reliable on every save. Set automatically by styles:watch.
#
# SKIP_LINT=1  — skips prettier + stylelint. Used by styles:watch so that
#                formatter writes do not re-trigger the chokidar watcher.
#
# BUILD_MODE=production — disables Sass source map generation and removes any
#                         residual .css.map files.

set -e

# Prevent concurrent pipeline runs. chokidar-cli explicitly documents that
# commands may run concurrently ("XXX: commands might be still run concurrently",
# index.js:162). Rapid saves can therefore trigger overlapping builds that race
# each other — the cleanup step of one run can delete .css files while the
# PostCSS step of a concurrent run is about to read them.
LOCK_FILE=/tmp/core-wp-styles.lock
if [ -f "$LOCK_FILE" ]; then
	echo "Styles build already running — skipping concurrent trigger."
	exit 0
fi
touch "$LOCK_FILE"
trap 'rm -f "$LOCK_FILE"' EXIT INT TERM

CSS_OUT="./assets/css"
CSS_SRC="./assets/css/.src"

if [ "${BUILD_MODE:-}" = "production" ]; then
	SASS_SOURCE_MAP_FLAG="--no-source-map"
else
	SASS_SOURCE_MAP_FLAG=""
fi

if [ "${SKIP_CLEAN:-}" != "1" ]; then
	echo "Cleaning styles output..."
	rm -rf "$CSS_OUT"
fi

mkdir -p "$CSS_OUT" "$CSS_SRC"

if [ "${SKIP_LINT:-}" != "1" ]; then
	echo "Linting..."
	prettier --write --log-level warn './theme_components/sass/**/*.scss'
	stylelint './theme_components/sass/**/*.scss' --fix
fi

echo "Compiling SCSS (mode: ${BUILD_MODE:-development})..."
sass "theme_components/sass/:$CSS_SRC" \
	--load-path=node_modules \
	--style=compressed \
	--no-error-css \
	--charset \
	${SASS_SOURCE_MAP_FLAG}

# PostCSS: process all intermediate .css files in a single invocation.
# postcss-cli expands the glob internally via tinyglobby and processes all
# files in parallel (Promise.all). --ext replaces .css with .min.css so
# global-styles.css → global-styles.min.css. --no-map suppresses source maps
# since .src/ is deleted immediately after and any map references would be stale.
echo "Running PostCSS (autoprefixer + cssnano → .min.css)..."
postcss "$CSS_SRC/*.css" --dir "$CSS_OUT" --ext .min.css --no-map

echo "Removing intermediate CSS files..."
rm -rf "$CSS_SRC"

if [ "${BUILD_MODE:-}" = "production" ]; then
	echo "Cleaning up map files..."
	find "$CSS_OUT" -type f -name '*.css.map' -delete
fi

echo "Completed: Styles Task"
