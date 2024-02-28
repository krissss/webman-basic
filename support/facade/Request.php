<?php

namespace support\facade;

class Request extends \support\Request
{
    /**
     * 替换数据.
     *
     * @return void
     */
    public function replace(string $type, array $values = [])
    {
        $this->_data[$type] = $values;
    }

    /**
     * 替换 get 数据.
     *
     * @return void
     */
    public function replaceGet(array $values = [])
    {
        $this->replace('get', $values);
    }

    /**
     * 替换 post 数据.
     *
     * @return void
     */
    public function replacePost(array $values = [])
    {
        $this->replace('post', $values);
    }

    /**
     * host 支持 x-forwarded
     * @inheritDoc
     */
    public function host($without_port = false): string
    {
        if ($host = $this->header('x-forwarded-host')) {
            if (strpos($host, ',') !== false) {
                $host = explode(',', $host)[0];
            }
            if ($without_port) {
                return $host;
            }
            if ($port = $this->header('x-forwarded-port')) {
                if (strpos($port, ',') !== false) {
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
        if (strpos($proto, ',') !== false) {
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
