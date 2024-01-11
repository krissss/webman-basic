#!/bin/sh

set -o errexit

#echo "---- init nacos config"
#php webman init-nacos-config

echo "---- DB migration"
./vendor/bin/phinx migrate

echo "---- init data"
php webman init-data

echo "---- storage:link"
php webman storage:link

echo "---- INIT OVER"

# Execute CMD
exec "$@"
