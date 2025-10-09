<?php

namespace app\components;

use app\model\traits\LikeScopedTrait;
use support\Model;

abstract class BaseModel extends Model
{
    use LikeScopedTrait;

    /**
     * {@inheritDoc}
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
