<?php

namespace app\model;

use DateTimeInterface;
use support\Model;

class AbsModel extends Model
{
    /**
     * @inheritDoc
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
