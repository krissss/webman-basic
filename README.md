# webman-basic

webman 基础模版

支持组件：

- [console](https://www.workerman.net/doc/webman/plugin/console.html)
- database: [eloquent](https://learnku.com/docs/laravel/8.x/eloquent/9400)
- [redis](https://www.workerman.net/doc/webman/db/redis.html)：使用 `support\facade\Redis` 类
- cache: 使用 `support\facade\Cache` 类，配置：`config/cache.php`
- log: 使用 `support\facade\Logger` 类，配置：`config/log.php` 和 `config/log-channel.php`
- [lock](https://www.workerman.net/plugin/55): 使用 `support\facade\Locker` 类
- [container](https://laravel.com/docs/8.x/container): 使用 `support\facade\Container` 类
- [auth](https://www.workerman.net/plugin/54): 使用 `support\facade\Auth` 类
- [validate](https://laravel.com/docs/8.x/validation)：使用 `validator()` 函数或 `support\facade\Validator` 类
- [db-migration](https://github.com/cakephp/phinx): 详见 `phinx.php`
- [translation-symfony](https://www.workerman.net/doc/webman/components/translation.html)：使用 `trans()` 函数或 `support\facade\Translation` 类
- [translation-laravel](https://laravel.com/docs/8.x/localization)：使用 `support\facade\TranslationLaravel` 类
- env: 使用 `get_env()` 函数
- [redis-queue](https://www.workerman.net/doc/webman/queue/redis.html)：使用 `support\facade\Queue` 类
- [crontab](https://www.workerman.net/doc/webman/components/crontab.html)
- [amis-admin](https://github.com/krissss/webman-amis-admin)
- [debugbar](https://github.com/krissss/webman-debugbar)
- [log-reader](https://github.com/krissss/webman-log-reader)

## 初始化项目步骤

```bash
# 1. 初始化 .env，选择 dev 环境
php init
# 2. 修改 .env 配置适配本机
# 3. 安装依赖
composer install
# 4. 初始化数据库结构迁移（如果需要）
composer phinx migrate
# 5. 初始化数据库初始数据（如果需要）
php webman init-data
```

## 启动服务

```bash
# windows
php windows.php
# linux
php start.php start
```


## docker 相关

### 纯 docker 环境开发

首次启动：因为项目还没初始化，所有无法启动服务，因此需要使用镜像进入后进行一次安装

启动服务并进入容器: `docker run --rm --name webman -v $pwd/.:/app -it --entrypoint /bin/sh krisss/docker-webman:7.4-cli-alpine`

在容器内执行[初始化项目步骤](#初始化项目步骤)

初始化完成后可以通过 `exit` 退出容器，容器会自动销毁

后续启动：项目初始化完成后

```bash
# 1. 启动容器，后台启动请加 -d
docker-compose up
```

其他操作

- 进入容器内：`docker exec -it {你的容器名字或id} /bin/sh`
- 停止容器：`docker-compose down`

### 构建系统镜像

```bash
docker build -t {镜像名}:{tag} --build-arg APP_ENV=dev .
```
