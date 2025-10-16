<?php

namespace app\bootstrap;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use support\Db;
use support\facade\Logger;
use support\Log;
use Webman\Bootstrap;

final class EloquentLog implements Bootstrap
{
    private static ?EloquentSQLMessage $message = null;

    public static function start($worker)
    {
        if (self::$message === null) {
            self::$message = new EloquentSQLMessage(array_merge([
                'channel' => Logger::CHANNEL_SQL,
            ], get_env('LOG_ELOQUENT_SQL_CONFIG', [])));
        }
        $message = self::$message;
        $message->bindConnection(Db::connection());
    }
}

/**
 * SQL 日志
 */
class EloquentSQLMessage
{
    private bool $enable = true;
    private string $channel = 'sql';
    private array $ignoreSql = [
        'select 1', // 心跳 SQL
    ]; // 忽略的 sql
    private array $ignoreSqlPattern = []; // 正则忽略的 sql
    private int $logMinTimeMS = 1000; // 仅记录大于该时间的 sql，单位毫秒
    private int $warningTimeMS = 2000; // 超过该时间，记为 warning
    private int $errorTimeMS = 10000; // 超过该时间，记为 error
    private bool $logNotSelect = true; // 记录所有非 select 语句
    private bool $bindSQLBindings = true; // 是否绑定 SQL 参数
    private bool $showConnectionName = false; // 是否显示连接名称

    public function __construct(array $config = [])
    {
        $config = array_filter($config, fn($value) => $value !== null);
        foreach ($config as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->{$key} = $value;
        }
    }

    final public function appendIgnoreSql(string|array $sql): static
    {
        $this->ignoreSql = array_unique(array_merge($this->ignoreSql, (array)$sql));

        return $this;
    }

    final public function appendIgnoreSqlPattern(string|array $pattern): static
    {
        $this->ignoreSqlPattern = array_unique(array_merge($this->ignoreSqlPattern, (array)$pattern));

        return $this;
    }

    public function bindConnection(Connection $connection): void
    {
        if (!$this->enable) {
            return;
        }

        $connection->listen($this->handle(...));
    }

    public function handle(QueryExecuted $event): void
    {
        if (!$this->enable) {
            return;
        }

        $sql = $event->sql;
        $sqlTime = $event->time;

        // 检查是否需要记录
        if (in_array($sql, $this->ignoreSql, true)) {
            return;
        }
        foreach ($this->ignoreSqlPattern as $pattern) {
            if (preg_match($pattern, $sql)) {
                return;
            }
        }
        if ($sqlTime < $this->logMinTimeMS) {
            $shouldReturn = true;
            if ($this->logNotSelect) {
                if (preg_match("/^\s*(update|delete|insert|replace|create|alter|drop|truncate)\s*/i", $sql)) {
                    $shouldReturn = false;
                }
            }
            if ($shouldReturn) {
                return;
            }
        }

        // sql 绑定参数
        if ($this->bindSQLBindings && $event->bindings) {
            $sql = $event->toRawSql();
        }

        $logLevel = 'info';
        if ($this->warningTimeMS > 0 && $sqlTime >= $this->warningTimeMS) {
            $logLevel = 'warning';
        }
        if ($this->errorTimeMS > 0 && $sqlTime >= $this->errorTimeMS) {
            $logLevel = 'error';
        }

        $context = [
            'cost' => $sqlTime,
        ];
        if ($this->showConnectionName) {
            $context['connectionName'] = $event->connectionName;
        }

        Log::channel($this->channel)->log($logLevel, $sql, $context);
    }
}
