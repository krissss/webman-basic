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

//return new Webman\Container;

$container = new Illuminate\Container\Container();
foreach (app\components\Component::dependence() as $name => $config) {
    if ($config['singleton']) {
        $container->singleton($name, $config['singleton']);
    }
    $config['alias'] = $config['alias'] ?? [];
    foreach ((array)$config['alias'] as $alias) {
        $container->alias($name, $alias);
    }
}

return $container;
