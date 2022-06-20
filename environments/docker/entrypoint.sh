#!/bin/sh

set -o errexit

# 迁移数据库
echo "DB migration"
./vendor/bin/phinx migrate
# 初始化数据
echo "DB init"
php webman init-data

# Execute CMD
exec "$@"
