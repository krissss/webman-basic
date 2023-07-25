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
}
