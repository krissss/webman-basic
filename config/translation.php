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

/**
 * Multilingual configuration
 */

use app\enums\common\LangEnum;

return [
    // Default language
    'locale' => LangEnum::ZH_CN,
    // Fallback language
    'fallback_locale' => [], // 不提供语言降级，因为需要通过中文来快速翻译
    // Folder where language files are stored
    'path' => base_path() . '/resource/translations',
];
