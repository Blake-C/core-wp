#!/usr/bin/env sh
# build.sh — Full theme build
#
# Runs all four asset pipelines in parallel for maximum speed:
#   static:assets, scripts, styles, images
#
# Note: output may appear out of order because tasks run concurrently.

set -e

echo ""
echo "Starting build — tasks run in parallel, output may be interleaved."
echo ""

rm -rf assets ../core-wp-build
mkdir -p ./assets/

pnpm run static:assets &
pnpm run scripts &
pnpm run styles &
pnpm run images &
wait

echo ""
echo "Build Complete"
