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
            if ('select 1' === $sql) {
                return;
            }
            if ($event->bindings) {
                foreach ($event->bindings as $v) {
                    $sql = preg_replace('/\\?/', "'".(\is_string($v) ? addslashes($v) : $v)."'", $sql, 1);
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
