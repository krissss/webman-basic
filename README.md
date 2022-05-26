# webman-basic

webman 基础模版

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
composer phinx seed:run
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
docker build -t {镜像名}:{tag} .
```
