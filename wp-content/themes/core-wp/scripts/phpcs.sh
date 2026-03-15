#!/usr/bin/env sh
# phpcs.sh — PHP CodeSniffer pipeline
#
# Steps:
#   1. PHPCBF: auto-fix violations (exits non-zero when fixes are made — intentionally ignored)
#   2. PHPCS:  report any remaining violations

echo "Starting PHPCS & PHPCBF Tasks"

# Run PHPCBF first; ignore exit code since it exits 1 when it successfully fixes files
php ../../vendor/bin/phpcbf --standard=phpcs.xml --basepath=./ ; true

# Always run PHPCS after, even if PHPCBF made changes
php ../../vendor/bin/phpcs --standard=phpcs.xml --basepath=./

echo "Completed: PHPCS Task"
