# 用于将项目构建成镜像
ARG WEBMAN_DOCKER_VERSION=8.2-cli-alpine

# https://github.com/krissss/docker-webman
FROM krisss/docker-webman:$WEBMAN_DOCKER_VERSION

# 增加额外的扩展
#RUN apk add --no-cache git
#RUN install-php-extensions imagick

# 设置配置文件
# 自定义 php 配置文件，如果需要的话
# 覆盖镜像自带的
#COPY environments/docker/php.ini "$PHP_INI_DIR/conf.d/app.ini"
# 扩展额外的
#COPY environments/docker/my_php.ini "$PHP_INI_DIR/conf.d/my_php.ini"
# 自定义 supervisor 配置，如果需要的话
# 覆盖镜像自带的
#COPY environments/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# 扩展额外的
#COPY environments/docker/my_supervisord.conf /etc/supervisor/conf.d/my_supervisord.conf

# 预先加载 Composer 包依赖，优化 Docker 构建镜像的速度
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --no-autoloader --no-scripts

# 复制项目代码
COPY . .

# 配置 env 环境
ARG APP_ENV=dev
RUN php init --env=$APP_ENV --overwrite=all

# 执行 Composer 自动加载和相关脚本
RUN SKIP_ENV_CI=1 composer install --no-interaction --no-dev && composer dump-autoload

COPY environments/docker/start.sh /start.sh
RUN chmod +x /start.sh
CMD ["/start.sh"]
