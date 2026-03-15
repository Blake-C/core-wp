#!/usr/bin/env sh
# styles.sh — SCSS → minified CSS pipeline
#
# Steps:
#   1. Clean output directory
#   2. Lint: Prettier formatting + Stylelint auto-fix
#   3. Compile SCSS → CSS via Dart Sass
#   4. Rename compiled files to .min.css
#   5. PostCSS: autoprefixer + cssnano minification
#   6. Remove leftover .css.map files

set -e

echo "Cleaning styles output..."
rm -rf ./assets/css/*

echo "Linting..."
prettier --write --log-level warn './theme_components/sass/**/*.scss'
stylelint './theme_components/sass/**/*.scss' --fix

echo "Compiling SCSS..."
sass theme_components/sass/:assets/css \
	--load-path=node_modules \
	--style=compressed \
	--no-error-css \
	--charset \
	--silence-deprecation=import \
	--silence-deprecation=global-builtin \
	--silence-deprecation=if-function

echo "Renaming to .min.css..."
ls ./assets/css \
	| grep -Ev '\.min\.css|\.map' \
	| xargs -I '{}' basename '{}' .css \
	| xargs -I '{}' mv ./assets/css/{}.css ./assets/css/{}.min.css

echo "Running PostCSS..."
postcss './assets/css/*.css' --replace

echo "Cleaning up map files..."
find ./assets/css -type f \( -iname '*.css.map' ! -iname '*.min.*' \) -exec rm -rf {} +

echo "Completed: Styles Task"
