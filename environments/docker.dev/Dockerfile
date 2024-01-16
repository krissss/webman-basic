# 本地开发环境用
ARG WEBMAN_DOCKER_VERSION=7.4-cli-alpine

# https://github.com/krissss/docker-webman
FROM krisss/docker-webman:$WEBMAN_DOCKER_VERSION

# 修改 Entrypont
COPY environments/docker/entrypoint.sh /sbin/entrypoint.sh
RUN chmod 755 /sbin/entrypoint.sh

# dev 环境移除 build 的操作

ENTRYPOINT ["/sbin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
