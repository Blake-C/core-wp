#!/usr/bin/env sh
# styles.sh — SCSS → minified CSS pipeline
#
# Steps:
#   1. Clean output directory
#   2. Lint: Prettier formatting + Stylelint auto-fix
#   3. Compile SCSS → CSS via Dart Sass
#   4. Rename compiled files to .min.css
#   5. PostCSS: autoprefixer + cssnano minification
#   6. (Production only) Remove source map files
#
# BUILD_MODE=production disables Sass source map generation and removes any
# residual .css.map files. Default (development) keeps source maps for DevTools.

set -e

if [ "${BUILD_MODE:-}" = "production" ]; then
	SASS_SOURCE_MAP_FLAG="--no-source-map"
else
	SASS_SOURCE_MAP_FLAG=""
fi

echo "Cleaning styles output..."
rm -rf ./assets/css/*

echo "Linting..."
prettier --write --log-level warn './theme_components/sass/**/*.scss'
stylelint './theme_components/sass/**/*.scss' --fix

echo "Compiling SCSS (mode: ${BUILD_MODE:-development})..."
sass theme_components/sass/:assets/css \
	--load-path=node_modules \
	--style=compressed \
	--no-error-css \
	--charset \
	${SASS_SOURCE_MAP_FLAG}

echo "Renaming to .min.css..."
ls ./assets/css \
	| grep -Ev '\.min\.css|\.map' \
	| xargs -I '{}' basename '{}' .css \
	| xargs -I '{}' mv ./assets/css/{}.css ./assets/css/{}.min.css

echo "Running PostCSS..."
postcss './assets/css/*.css' --replace

if [ "${BUILD_MODE:-}" = "production" ]; then
	echo "Cleaning up map files..."
	find ./assets/css -type f \( -iname '*.css.map' ! -iname '*.min.*' \) -exec rm -rf {} +
fi

echo "Completed: Styles Task"
