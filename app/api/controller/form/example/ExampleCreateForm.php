<?php

namespace app\api\controller\form\example;

use app\components\Component;
use app\exception\UserSeeException;
use app\model\Admin as Model;
use WebmanTech\DTO\Attributes\ValidationRules;
use WebmanTech\DTO\BaseRequestDTO;

final class ExampleCreateForm extends BaseRequestDTO
{
    /**
     * 用户名
     * @example admin
     */
    #[ValidationRules(maxLength: 64)]
    public string $username;

    /**
     * 密码
     * @example 123456
     */
    #[ValidationRules(maxLength: 64)]
    public string $password;

    /**
     * 名称
     * @example 测试用户
     */
    public string $name;

    public function handle(): Model
    {
        if (Model::query()->where('username', $this->username)->exists()) {
            throw new UserSeeException('username 已存在');
        }

        $model = new Model($this->toArray());
        $model->password = Component::security()->generatePasswordHash($this->password);
        $model->refreshToken();
        $model->refresh();

        return $model;
    }
}
