<?php

namespace app\components;

use DateTimeInterface;
use support\Model;

class BaseModel extends Model
{
    /**
     * @inheritDoc
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
