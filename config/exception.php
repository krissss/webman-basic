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

use app\enums\common\AppModuleEnum;

return [
    //'' => support\exception\Handler::class,
    '' => app\exception\handlers\ExceptionHandler::class,
    AppModuleEnum::Admin->value => app\exception\handlers\ExceptionHandlerAmis::class,
    AppModuleEnum::User->value => app\exception\handlers\ExceptionHandlerAmis::class,
    AppModuleEnum::Api->value => app\exception\handlers\ExceptionHandlerApi::class,
];
