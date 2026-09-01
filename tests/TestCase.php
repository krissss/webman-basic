<?php

namespace Tests;

use WebmanTech\Testing\TestCase as BaseTestCase;

/**
 * Feature 测试基类（真实 webman 进程模式）
 *
 * 数据库就绪由组件 setUpDatabase() 一行完成（迁移 + 注入连接 + 清空业务表）：
 * - 迁移：进程级一次性执行 phinx（复用项目 phinx.php），不删除数据库文件，
 *   避免 server 进程已持有的连接指向被删除的 inode
 * - 连接：测试进程自动从 config('database') 构建 sqlite 直连 PDO
 * - 隔离：每测试清空 config/testing.php database.truncate 的业务表
 */
abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase(['sqlite' => 'testing.sqlite']);
    }

    protected function tearDown(): void
    {
        $this->flushHeaders();
        $this->flushCookies();

        parent::tearDown();
    }
}
