#!/usr/bin/env sh
#
# Production deploy script.
# Pulls the latest base images, rebuilds the WordPress image, and restarts services.
#
# Usage:
#   ./deploy.sh

set -e

docker compose -f compose.prod.yml build --pull
docker compose -f compose.prod.yml up -d
