<?php

namespace app\api\controller\form\example;

use app\components\Component;
use app\enums\common\OnOffStatusEnum;
use app\exception\UserSeeException;
use app\model\Admin as Model;
use WebmanTech\DTO\Attributes\RequestPropertyInPath;
use WebmanTech\DTO\Attributes\ValidationRules;
use WebmanTech\DTO\BaseRequestDTO;

final class ExampleUpdateForm extends BaseRequestDTO
{
    #[RequestPropertyInPath]
    public int $id;

    /**
     * 用户名
     * @example admin
     */
    #[ValidationRules(maxLength: 64)]
    public ?string $username = null;

    /**
     * 密码
     * @example 123456
     */
    #[ValidationRules(maxLength: 64)]
    public ?string $password = null;

    /**
     * 名称
     * @example 测试用户
     */
    public ?string $name = null;

    /**
     * 状态
     */
    public ?OnOffStatusEnum $status = null;

    public function handle(): Model
    {
        $model = Model::findOrFail($this->id);
        $model->fill($this->toArray());

        if ($model->isDirty('username') && Model::query()->where('username', $model->username)->whereKeyNot($model->id)->exists()) {
            throw new UserSeeException('username 已存在');
        }

        if ($this->password) {
            // 修改密码才刷新 token
            $model->password = Component::security()->generatePasswordHash($this->password);
            $model->refreshToken();
        } else {
            $model->save();
        }

        return $model;
    }
}
