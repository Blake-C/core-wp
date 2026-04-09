#!/usr/bin/env sh
# styles.sh — SCSS → minified CSS pipeline
#
# Steps:
#   1. Clean output directory          [skipped when SKIP_CLEAN=1]
#   2. Lint: Prettier + Stylelint      [skipped when SKIP_LINT=1]
#   3. Compile SCSS → CSS via Dart Sass
#   4. PostCSS (autoprefixer + cssnano) reading *.css → writing *.min.css directly
#   5. Remove intermediate *.css files
#   6. (Production only) Remove source map files
#
# SKIP_CLEAN=1 — skips rm -rf of assets/css/ so CSS files are overwritten in
#                place rather than deleted and recreated. Browser-sync then sees
#                a single change event (not unlink+add) and CSS injection stays
#                reliable on every save. Set automatically by styles:watch.
#
# SKIP_LINT=1  — skips prettier + stylelint. Used by styles:watch so that
#                formatter writes do not re-trigger the chokidar watcher and
#                cause concurrent builds.
#
# BUILD_MODE=production — disables Sass source map generation and removes any
#                         residual .css.map files.

set -e

if [ "${BUILD_MODE:-}" = "production" ]; then
	SASS_SOURCE_MAP_FLAG="--no-source-map"
else
	SASS_SOURCE_MAP_FLAG=""
fi

if [ "${SKIP_CLEAN:-}" != "1" ]; then
	echo "Cleaning styles output..."
	rm -rf ./assets/css/*
fi

if [ "${SKIP_LINT:-}" != "1" ]; then
	echo "Linting..."
	prettier --write --log-level warn './theme_components/sass/**/*.scss'
	stylelint './theme_components/sass/**/*.scss' --fix
fi

echo "Compiling SCSS (mode: ${BUILD_MODE:-development})..."
sass theme_components/sass/:assets/css \
	--load-path=node_modules \
	--style=compressed \
	--no-error-css \
	--charset \
	${SASS_SOURCE_MAP_FLAG}

# PostCSS: process each intermediate .css file and write directly to .min.css
# in a single step. Combining rename + postcss into one write means browser-sync
# sees exactly one change event per file per save instead of two (rename then
# postcss overwrite), which prevents double-injection and pending queue buildup.
echo "Running PostCSS (autoprefixer + cssnano → .min.css)..."
ls ./assets/css \
	| grep -Ev '\.min\.css|\.map' \
	| xargs -I '{}' basename '{}' .css \
	| xargs -I '{}' postcss ./assets/css/{}.css -o ./assets/css/{}.min.css

echo "Removing intermediate CSS files..."
find ./assets/css -maxdepth 1 -name '*.css' ! -name '*.min.css' -delete

if [ "${BUILD_MODE:-}" = "production" ]; then
	echo "Cleaning up map files..."
	find ./assets/css -type f \( -iname '*.css.map' ! -iname '*.min.*' \) -exec rm -rf {} +
fi

echo "Completed: Styles Task"
