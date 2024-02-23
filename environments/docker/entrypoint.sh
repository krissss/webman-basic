#!/bin/sh

set -o errexit

php artisan environment:ci

# Execute CMD
exec "$@"
