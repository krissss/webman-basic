<?php

namespace app\components;

use app\model\traits\LikeScopedTrait;
use Illuminate\Database\Eloquent\Builder;
use support\Model;

/**
 * @method static Builder|static query()
 * @method static Builder|static newQuery()
 * @method static Builder|static newModelQuery()
 */
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
