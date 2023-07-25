<?php

namespace app\model\traits;

use Ramsey\Uuid\Uuid as Generator;

/**
 * 使用前安装依赖：composer require ramsey/uuid
 * https://github.com/goldspecdigital/laravel-eloquent-uuid.
 */
trait UuidPrimaryKey
{
    /**
     * Boot uuid trait.
     *
     * @return void
     */
    protected static function bootUuidPrimaryKey()
    {
        static::creating(function (self $model) {
            if (!$model->{$model->getKeyName()} || !Generator::isValid($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Generator::uuid4()->toString();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'uuid';
    }
}
