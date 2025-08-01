<?php

namespace app\bootstrap;

use Illuminate\Database\Events\QueryExecuted;
use support\Db;
use support\facade\Logger;
use Webman\Bootstrap;

class EloquentLog implements Bootstrap
{
    /**
     * {@inheritDoc}
     */
    public static function start($worker)
    {
        Db::connection()->listen(function (QueryExecuted $event) {
            if (!config('log-ext.sql.enable', false)) {
                return;
            }

            $sql = $event->sql;

            // 检查是否需要记录 sql
            $shouldRecord =
                $sql !== 'select 1' // 心跳 sql
                && (
                    $event->time > config('log-ext.sql.info_time', 1000) // 大于指定时间
                    || preg_match("/^\s*(update|delete|insert|replace)\s*/i", $event->sql) // 增删改
                );
            if (!$shouldRecord) {
                return;
            }

            // sql 绑定参数
            if ($event->bindings) {
                foreach ($event->bindings as $v) {
                    $sql = preg_replace('/\\?/', "'" . (is_string($v) ? addslashes($v) : $v) . "'", (string)$sql, 1);
                }
            }

            $sqlTime = $event->time;
            $sqlLevel = 'info';
            if ($sqlTime >= config('log-ext.sql.warning_time', 1500)) {
                $sqlLevel = 'warning';
            } elseif ($sqlTime >= config('log-ext.sql.error_time', 10000)) {
                $sqlLevel = 'error';
            }
            Logger::sql('[{time}ms] {sql}', $sqlLevel, [
                'time' => $event->time,
                'sql' => $sql,
            ]);
        });
    }
}
