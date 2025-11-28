<?php

namespace app\bootstrap;

use support\Db;
use support\facade\Logger;
use Webman\Bootstrap;
use WebmanTech\Logger\Message\EloquentSQLMessage;

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
