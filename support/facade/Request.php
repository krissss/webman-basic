<?php

namespace support\facade;

/**
 * @deprecated 使用 support\Request
 */
class Request extends \support\Request
{
    /**
     * 替换数据.
     *
     * @return void
     * @deprecated 应该用不到了
     */
    public function replace(string $type, array $values = [])
    {
        $this->_data[$type] = $values;
    }

    /**
     * 替换 get 数据.
     *
     * @return void
     * @deprecated 使用 setGet
     */
    public function replaceGet(array $values = [])
    {
        $this->setGet($values);
    }

    /**
     * 替换 post 数据.
     *
     * @return void
     * @deprecated 使用 setPost
     */
    public function replacePost(array $values = [])
    {
        $this->setPost($values);
    }
}
