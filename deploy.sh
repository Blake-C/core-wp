#!/usr/bin/env sh
#
# Production deploy script.
# Rebuilds the WordPress image, recreates the webroot volume from it, and restarts services.
#
# Usage:
#   ./deploy.sh

set -e

compose="docker compose -f compose.prod.yml"

$compose build --pull

# Docker copies image files into a named volume only when the volume is created, so webroot
# is recreated to ship the new build. db_data and uploads are left untouched.
webroot_volume="$($compose config | awk '/^name:/{print $2}')_webroot"
$compose rm -sf wordpress nginx
if docker volume inspect "$webroot_volume" >/dev/null 2>&1; then
	docker volume rm "$webroot_volume"
fi

$compose up -d
