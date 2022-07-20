<?php

namespace app\model;

use app\components\Component;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kriss\WebmanAuth\Interfaces\IdentityInterface;
use Kriss\WebmanAuth\Interfaces\IdentityRepositoryInterface;
use support\Model;

/**
 * @property integer $id (主键)
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $name 名称
 * @property string $access_token Access Token
 * @property integer $status 状态
 * @property mixed $created_at 创建时间
 * @property mixed $updated_at 修改时间
 * @property mixed $deleted_at 删除时间
 */
class User extends Model implements IdentityInterface, IdentityRepositoryInterface
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
     * @inheritdoc
     */
    protected $fillable = [
        'username',
        'name',
        'status',
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'password',
        'access_token',
    ];

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
    public function findIdentity(string $token, string $type = null): ?IdentityInterface
    {
        $model = null;
        if ($type === 'session') {
            $model = static::find($token);
        } elseif ($type === 'token') {
            $model = static::query()->where('access_token', $token)->first();
        }
        /** @var static|null $model */
        return $model;
    }

    /**
     * 刷新 token
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
