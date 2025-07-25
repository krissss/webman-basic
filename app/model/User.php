<?php

namespace app\model;

use app\components\BaseModel;
use app\components\Component;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;
use WebmanTech\Auth\Interfaces\IdentityInterface;
use WebmanTech\Auth\Interfaces\IdentityRepositoryInterface;

/**
 * app\model\User
 *
 * @property int $id (主键)
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $name 名称
 * @property string|null $access_token Access Token
 * @property string|null $api_token Api Token
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 修改时间
 * @property string|null $deleted_at 删除时间
 *
 * others
 */
#[OA\Schema]
class User extends BaseModel implements IdentityInterface, IdentityRepositoryInterface
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

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
     * {@inheritdoc}
     */
    protected $fillable = [
        'username',
        'name',
        'status',
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'password',
        'access_token',
        'api_token',
    ];

    /**
     * {@inheritDoc}
     */
    public function getId(): ?string
    {
        return $this->{$this->primaryKey};
    }

    /**
     * {@inheritDoc}
     */
    public function refreshIdentity()
    {
        return $this->refresh();
    }

    /**
     * {@inheritDoc}
     */
    public function findIdentity(string $token, string $type = null): ?IdentityInterface
    {
        $model = null;
        if ($type === 'session') {
            $model = static::find($token);
        } elseif ($type === 'token') {
            $model = static::query()->where('access_token', $token)->first();
        } elseif ($type === 'api_token') {
            $model = static::query()->where('api_token', $token)->first();
        }
        /* @var static|null $model */
        return $model;
    }

    /**
     * 刷新 token.
     *
     * @param false|string|null $token
     */
    public function refreshToken($token = false)
    {
        if ($token === false) {
            $token = Component::security()->generateRandomString(32);
        }
        $this->access_token = $token;
        $this->save();
    }
}
