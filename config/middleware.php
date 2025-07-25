<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    '' => [
        app\middleware\RequestPathPrefixMiddleware::class,
        app\middleware\OperateLogMiddleware::class,
        //app\middleware\Cors::class,
        app\middleware\TrimStings::class,
        //app\middleware\ConvertEmptyStringsToNull::class,
        app\middleware\Lang::class,
    ],
];
