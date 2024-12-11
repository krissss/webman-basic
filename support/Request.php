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

namespace support;

/**
 * Class Request
 * @package support
 */
class Request extends \Webman\Http\Request
{
    /**
     * host 支持 x-forwarded
     * @inheritDoc
     */
    public function host($without_port = false): string
    {
        if ($host = $this->header('x-forwarded-host')) {
            if (str_contains($host, ',')) {
                $host = explode(',', $host)[0];
            }
            if ($without_port) {
                return $host;
            }
            if ($port = $this->header('x-forwarded-port')) {
                if (str_contains($port, ',')) {
                    $port = explode(',', $port)[0];
                }
                if (in_array($port, ['80', '443'])) {
                    return $host;
                }
                return $host . ':' . $port;
            }
            return $host;
        }
        return parent::host($without_port);
    }

    /**
     * path 前缀
     * @return string
     */
    public function pathPrefix(): string
    {
        return $this->header('x-forwarded-prefix', '');
    }

    /**
     * 获取传输协议
     * @return string http/https
     */
    public function getProto(): string
    {
        $proto = $this->header('x-forwarded-proto', 'http');
        if (str_contains($proto, ',')) {
            $proto = explode(',', $proto)[0];
        }
        return $proto;
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return $this->getProto() . '://' . $this->host() . $this->pathPrefix() . $this->path();
    }

    /**
     * @inheritDoc
     */
    public function fullUrl(): string
    {
        return $this->getProto() . '://' . $this->host() . $this->pathPrefix() . $this->uri();
    }
}
