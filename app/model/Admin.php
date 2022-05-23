<?php

namespace app\model;

use app\components\BaseModel;
use Kriss\WebmanAuth\Interfaces\IdentityInterface;
use Kriss\WebmanAuth\Interfaces\IdentityRepositoryInterface;
use Kriss\WebmanAuth\Interfaces\IdentityRepositoryWithTokenInterface;

/**
 * app\model\Admin
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $name
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 修改时间
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @mixin \Eloquent
 */
class Admin extends BaseModel implements IdentityInterface, IdentityRepositoryInterface, IdentityRepositoryWithTokenInterface
{
    public const SUPER_ADMIN_ID = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return $this->{$this->primaryKey};
    }

    /**
     * @inheritDoc
     */
    public function refreshIdentity()
    {
        return $this->refresh();
    }

    /**
     * @inheritDoc
     */
    public function findIdentity(string $id): ?IdentityInterface
    {
        return static::find($id);
    }

    /**
     * @inheritDoc
     */
    public function findIdentityByToken(string $token, string $type = null): ?IdentityInterface
    {
        return static::query()->where('username', $token)->first();
    }
}
