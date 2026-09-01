<?php

// phpunit 的 <env> 注入发生在 bootstrap 加载之前（Application::run 中 PhpHandler 先于
// loadBootstrapScript），已 putenv 并写入 $_ENV；项目 get_env()（EnvAttr）优先读 $_SERVER，
// 这里回灌一份，使测试进程与 server 子进程（继承 putenv）读到一致的测试 env，
// 因此 phpunit.xml 只需写 <env>，无需再写 <server> 双份。
foreach ($_ENV as $name => $value) {
    $_SERVER[$name] = $value;
}

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../support/bootstrap.php';
