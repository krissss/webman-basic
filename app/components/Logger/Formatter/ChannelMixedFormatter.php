<?php

namespace app\components\Logger\Formatter;

class ChannelMixedFormatter extends ChannelFormatter
{
    public function __construct()
    {
        parent::__construct('[%channel%]');
    }
}
