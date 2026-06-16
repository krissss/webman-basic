<?php

namespace Tests;

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use WebmanTech\CommonUtils\RuntimeCustomRegister;
use WebmanTech\CommonUtils\Testing\TestRequest;
use WebmanTech\LaravelDatabase\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    private const TEST_DATABASE = 'testing.sqlite';

    private static bool $migrated = false;

    protected function setUp(): void
    {
        parent::setUp();

        TestRequest::clear();
        RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_REQUEST, fn() => TestRequest::instance());

        $this->ensureTestDatabase();
        $this->migrateDatabase();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        TestRequest::clear();

        parent::tearDown();
    }

    private function migrateDatabase(): void
    {
        if (self::$migrated) {
            return;
        }

        $this->resetTestDatabase();

        $manager = new Manager(
            Config::fromPhp(base_path('phinx.php')),
            new ArrayInput([]),
            new NullOutput(),
        );
        $manager->migrate('development');

        self::$migrated = true;
    }

    private function ensureTestDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || $this->databasePath() !== runtime_path(self::TEST_DATABASE)) {
            throw new \RuntimeException('Feature tests must run against runtime/testing.sqlite database.');
        }
    }

    private function resetTestDatabase(): void
    {
        DB::getInstance()->getDatabaseManager()->purge('sqlite');

        $database = $this->databasePath();
        if (file_exists($database)) {
            unlink($database);
        }

        touch($database);
    }

    private function databasePath(): string
    {
        $database = config('database.connections.sqlite.database');

        if (str_starts_with($database, '/') || preg_match('/^[A-Za-z]:[\/\\\\]/', $database) === 1) {
            return $database;
        }

        return base_path($database);
    }
}
