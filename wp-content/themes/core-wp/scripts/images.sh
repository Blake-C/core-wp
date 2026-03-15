#!/usr/bin/env sh
# images.sh — Image copy and optimization pipeline
#
# Steps:
#   1. Clean output directory
#   2. Copy source images to assets/
#   3. Optimize with ImageMagick mogrify (if installed)
#      - Strips metadata, caps at 1500×1500, compresses to 70% quality
#      - Processes 4 images in parallel via xargs -P 4
#
# Requires: brew install imagemagick (macOS) or equivalent

set -e

echo "Cleaning images output..."
rm -rf ./assets/images/*

echo "Copying images..."
cp -r ./theme_components/images ./assets/

if command -v mogrify >/dev/null 2>&1; then
	echo "Optimizing images..."
	find ./assets/images -type f \
		| grep -E 'png|jpg|jpeg' \
		| xargs -P 4 -I '{}' mogrify -strip -resize '1500x1500>' -quality 70 '{}'
	echo "Completed: Image Optimizations"
else
	echo "Warning: ImageMagick not found — images copied but not optimized." >&2
	echo "Install with: brew install imagemagick" >&2
fi

echo "Completed: Images Task"
