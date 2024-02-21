[PHPStan](https://phpstan.org/)

# 使用方式

在当前目录下安装依赖（composer install）

执行检查：`composer analyse`

或自定义：`./vendor/bin/phpstan analyse xxx`

# 本地自定义配置

复制 phpstan.example.neon 为 phpstan.neon，修改其中的配置

此时执行 `composer analyse` 则是自动使用 `phpstan.neon`

# phpstorm 配置使用

设置 -> PHP -> 质量工具 -> PHPStan
                           
- 开启
- 配置：默认项目解释器，再点三个点的按钮，phpstan 路径选到 system\phpstan\vendor\bin\phpstan.bat
- 配置文件选 system\phpstan\phpstan.dist.neon 或 phpstan.neon

可以实时检查代码是否符合规范