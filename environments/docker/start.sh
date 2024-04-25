#!/bin/sh

set -o errexit

php artisan environment:ci

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
