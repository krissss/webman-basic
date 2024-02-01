[php-cs-fixer](https://cs.symfony.com/)

# 使用方式

在当前目录下安装依赖（composer install）

fix：`composer fixer` 或 `./vendor/bin/php-cs-fixer fix`

# phpstorm 配置使用

1. 设置 -> PHP -> 质量工具 -> PHP CS Fixer

- 开启
- 配置：默认项目解释器，再点三个点的按钮，路径选到 system\php-cs-fixer\vendor\bin\php-cs-fixer.bat
- 规则集：选择 custom，然后选到 system\php-cs-fixer\.php-cs-fixer.dist.php 或 .php-cs-fixer.php

2. 设置 -> PHP -> 质量工具

- 外部格式化程序：选择 PHP CS Fixer
