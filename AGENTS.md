# AGENTS.md

## 项目简介

这是一个基于 `Webman` 的 `PHP 8.2` 项目，主要分为 `admin`、`api`、`user` 三套业务入口，并配套中间件、组件、枚举、迁移、测试和若干
`Webman` 插件配置。

## 工作原则

- 优先沿用现有目录结构、命名风格和现成组件，不要为了“更现代”而重构。
- 改动尽量小而清晰，避免顺手扩大范围。
- 涉及公共中间件、路由、组件、模型基类、异常处理时，要先考虑对多个入口的影响。
- 注意 `Webman` / `Workerman` 常驻进程特性，避免请求间状态污染、静态缓存滥用和内存泄露。
- 不要随意修改 `public/assets` 下的静态资源，除非是在同步第三方依赖版本。
- 需要查库、框架、SDK、CLI 文档时，优先使用 `ctx7`。

## 目录导航

```text
.
├── app
│   ├── admin        后台管理端代码：路由、控制器、数据源、仓储；使用 `webman-tech/amis-admin` 开发
│   ├── api          对外或通用接口：API 路由、控制器、请求表单；使用 `webman-tech/dto` + `webman-tech/swagger` 开发
│   ├── user         用户端入口：登录、用户信息、用户侧系统能力；使用 `webman-tech/amis-admin` 开发
│   ├── components   跨模块复用组件、基类、工具类、上传和安全能力
│   │   └── Component.php   组件统一入口和使用出口，类似 `container`
│   ├── middleware   请求中间件：鉴权、跨域、语言、日志、请求清洗；使用 `php artisan make:middleware` 创建
│   ├── model        数据库模型、模型 trait、查询 scope；使用 `php artisan make:model` 创建，`php artisan make:model-doc` 更新注释
│   ├── enums        常量和业务字段枚举；优先使用 `PHP enum`，不建议继续使用 `BaseEnum`
│   │   ├── common   通用枚举
│   │   ├── traits   枚举 `trait`
│   │   └── {ModelName}{ModelAttribute}Enum   模型字段枚举，如 `UserStatusEnum`
│   ├── exception    业务异常和不同入口的异常处理器
│   │   └── handlers   全局异常处理器
│   ├── bootstrap    应用启动期注册和初始化逻辑，使用 `php artisan make:bootstrap` 创建
│   ├── command      自定义 `CLI` 命令、初始化和代码生成命令；优先使用 `Laravel Command` 基类，即 `signature` 形式
│   │   └── framework   框架级命令
│   ├── queue        `webman/redis-queue` 异步队列任务，继承 `BaseConsume`
│   ├── process      `Webman` 自定义常驻进程
│   ├── event        `webman/event` 事件目录；非必要不直接使用，避免增加复杂度
│   └── functions.php   业务相关全局函数
├── config           框架、插件、数据库、日志、路由、中间件等配置
├── resource
│   ├── database
│   │   └── migrations  数据库迁移文件，使用 `composer phinx create MyMigration`
│   ├── translations    多语言文案
│   └── phpstan         `PHPStan` 配置、扩展和 `stub`
├── support          框架级支撑代码，不涉及业务逻辑
│   ├── facade       通用组件入口，项目中建议通过 `facade` 使用组件
│   └── functions.php   框架级全局函数
├── tests            `Pest` 测试用例
│   ├── Feature      功能测试
│   ├── Fixture      测试数据
│   └── Unit         单元测试，目录结构尽量与 `app` 保持一致
├── docs             项目说明和开发文档
├── public           公开静态资源，不放业务 PHP 代码
│   ├── assets       由 `kriss/composer-assets-plugin` 根据 `composer.json` 的 `extra.assets-pkgs` 下载安装
│   └── storage      `webman-tech/laravel-filesystem` 通过 `php artisan storage:link` 创建的软链目录
├── runtime          运行期生成内容，不放需版本管理的源码或配置，使用 `runtime_path()` 定位
├── storage          `Storage` 本地存储目录
├── environments     项目环境配置，配合 `php init` 使用
├── artisan          `Laravel artisan` 命令入口
├── start.php        `Webman` 启动入口
├── env.local.php    本地 `env`，可覆盖 `env.php`
├── env.php          由 `environments` 管理的环境配置，不要直接修改
├── init             初始化 `environments` 配置的脚本
└── phinx.php        数据库迁移工具配置
```

## 组件说明

`AGENTS.md` 只记录项目入口和额外约定，具体写法以对应 `SKILL.md` 或 `README.md` 或 `源码` 为准。

### 组件 skills

涉及对应组件开发时，先阅读对应 `SKILL.md`，再动代码。

```text
vendor/webman-tech/components-monorepo/packages
├── amis-admin
│   └── skills/webman-tech-amis-admin-best-practices/SKILL.md    管理后台、CRUD、Repository、PresetItem
├── auth
│   └── skills/webman-tech-auth-best-practices/SKILL.md          认证授权、guard、登录态、中间件
├── common-utils
│   └── skills/webman-tech-common-utils-best-practices/SKILL.md  跨框架公共 API、Request/Response、Runtime、Helper
├── crontab-task
│   └── skills/webman-tech-crontab-task-best-practices/SKILL.md  定时任务、任务进程、TaskException
├── dto
│   └── skills/webman-tech-dto-best-practices/SKILL.md           DTO、请求表单、响应结构、配置结构
├── logger
│   └── skills/webman-tech-logger-best-practices/SKILL.md        多 channel 日志、Processor、请求链路日志
└── swagger
    └── skills/webman-tech-swagger-best-practices/SKILL.md       OpenAPI 文档、DTO schema、接口注解
```

涉及 `components-monorepo` 的本项目约定：

- 开发 `admin` / `user` 页面时先看 `webman-tech-amis-admin-best-practices`；控制器继承 `AbsSourceController`，仓储通常继承
  `AbsRepository`。
- 新增后台 CRUD 优先使用 `php artisan make:admin-controller`，后台路由优先使用项目扩展过的
  `support\facade\Route::resource()`。
- 非标准 Admin 页面可参考 `app/admin/controller/FilesystemController.php` 和 `app/admin/controller/InfoController.php`。
- 开发 `api` 接口时先看 `webman-tech-dto-best-practices` 和 `webman-tech-swagger-best-practices`；API 文档入口是
  `/api/openapi`。
- 日志 `channel` 集中维护在 `support\facade\Logger`。

### Laravel 兼容组件

`webman-tech/laravel-monorepo` 下的组件主要是 Laravel 组件在 Webman 中的兼容层，常规用法可参考 Laravel 官方文档；遇到
Webman 差异时，再看对应组件的 `README.md`。

```text
vendor/webman-tech/laravel-monorepo/src
├── LaravelCache        兼容 `illuminate/cache`，缓存、锁、限流
├── LaravelConsole      兼容 `illuminate/console`，提供 `artisan` 命令体系
├── LaravelDatabase     兼容 `illuminate/database`，查询构造器和 Eloquent
├── LaravelFilesystem   兼容 `illuminate/filesystem`，文件和 Storage
├── LaravelHttp         兼容 `illuminate/http`，HTTP Client、Request、上传文件包装
├── LaravelProcess      兼容 `illuminate/process`，进程执行
├── LaravelRedis        兼容 `illuminate/redis`，Redis facade 用法
├── LaravelTranslation  兼容 `illuminate/translation`，多语言和本地化
└── LaravelValidation   兼容 `illuminate/validation`，验证器和验证规则
```

项目中常用的 Laravel 风格入口已经封装在 `support/facade` 下，业务代码优先使用这些项目级 facade：

```text
support/facade
├── Cache.php              参考 Laravel `Cache`
├── CacheLocker.php        参考 Laravel cache lock 用法
├── CacheRateLimiter.php   参考 Laravel `RateLimiter`
├── File.php               参考 Laravel `File`
├── Storage.php            参考 Laravel `Storage`
├── Http.php               参考 Laravel `Http` Client，可在类内维护项目 HTTP macro
├── Validator.php          参考 Laravel `Validator`
└── TranslationLaravel.php 参考 Laravel translation/localization
```

涉及 `laravel-monorepo` 和 `support/facade` 的本项目约定：

- 外部 HTTP 请求的项目级 `macro` 集中维护在 `support\facade\Http`。
- 业务缓存锁集中维护在 `support\facade\CacheLocker`。
- 缓存优先使用 `support\facade\Cache` 或 `cache()`，不要直接依赖 `Redis`。
- 验证优先使用 `validator()`，需要 facade 时再使用 `support\facade\Validator`。

注意：这里是 Webman 环境下的 Laravel 组件兼容，不是完整 Laravel 应用。涉及配置文件、上传文件、命令入口、运行时上下文时，优先看本项目
`config/plugin/webman-tech/*` 和对应组件 `README.md`。

## 常用命令

### composer 命令

常用的工程级命令

- 安装依赖：`composer install`
- 开发启动：`composer dev`
- Windows 启动：`composer dev-win`
- 单测：`composer test`
- 静态分析：`composer analyse`
- 迁移工具：`composer phinx`

迁移配置和常用命令见 `phinx.php`，迁移文件默认放在 `resource/database/migrations`，默认基类是
`app\components\BaseMigration`。

### artisan/webman 命令

常用的业务开发辅助命令和业务脚本优先通过 `php artisan` 调用。

## 代码约定

- 使用当前 `PHP` 版本支持的新语法。
- `trait` 就近放置。例如模型相关 `trait` 放在 `app/model/traits` 下；确实全局通用的 `trait` 可放在 `app/components/traits`
  下。
- 枚举尽量使用 `PHP enum`；枚举说明通过 `description()` 方法提供。
- 模型查询建议从 `Model::query()` 开始，便于 IDE 提示和 `phpstan` 类型检查。
- 获取环境配置统一使用 `get_env()`；仅允许在 `environments` 相关配置中使用 `put_env()`，其他业务代码不得通过 `put_env()`
  修改环境变量。

## 修改后检查

- 改公共逻辑时，至少跑相关测试；必要时补充或更新 Pest 用例。
- 改配置、路由、中间件、模型基类时，检查是否影响 admin/api/user 三套入口。
- 改数据库相关内容时，确认迁移文件与代码引用一致。
- 改文档或接口行为时，顺手更新 `docs/` 下对应说明。
