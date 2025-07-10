<?php

namespace app\admin\controller\repository;

use app\components\Component;
use app\enums\common\OnOffStatusEnum;
use app\model\Admin;
use Illuminate\Database\Eloquent\Model;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Helper\DTO\PresetItem;

class AdminRepository extends AbsRepository
{
    private const SCENE_RESET_PASSWORD = 'reset_password';

    public function __construct()
    {
        parent::__construct(Admin::class);

        $this->getPresetsHelper()
            ->withPresets([
                'username' => new PresetItem(
                    label: '用户名',
                    filter: 'like',
                    rule: 'required|string|min:4',
                ),
                'password' => new PresetItem(
                    label: '密码',
                    grid: false,
                    formExt: fn(FormField $field) => $field->typeInputPassword(),
                    formExtDynamic: fn(FormField $field, string $scene) => $field->required($scene === static::SCENE_CREATE),
                    detail: false,
                    rule: 'required|string|min:6|max:32',
                ),
                'access_token' => new PresetItem(
                    label: 'Access Token',
                    grid: false,
                    form: false,
                    detail: true,
                ),
                'new_password' => new PresetItem(
                    label: '新密码',
                    grid: false,
                    formExt: fn(FormField $field) => $field->typeInputPassword(),
                    rule: 'required|string|min:6|max:32|confirmed',
                ),
                'new_password_confirmation' => new PresetItem(
                    label: '重复密码',
                    grid: false,
                    formExt: fn(FormField $field) => $field->typeInputPassword(),
                    rule: 'required|string',
                ),
            ])
            ->withCrudSceneKeys(['id', 'name', 'username', 'password', 'status', 'access_token', 'created_at', 'updated_at'])
            ->withSceneKeys([
                self::SCENE_RESET_PASSWORD => ['new_password', 'new_password_confirmation'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function visibleAttributes(string $scene): array
    {
        if ($scene === static::SCENE_DETAIL) {
            return ['access_token'];
        }

        return parent::visibleAttributes($scene);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave(Model $model): void
    {
        /** @var Admin $model */
        if ($model->isDirty('password')) {
            $model->password = Component::security()->generatePasswordHash($model->password);
        }
        if ($model->status === null) {
            $model->status = OnOffStatusEnum::On->value;
        }
        parent::doSave($model);
    }

    /**
     * 重置密码
     */
    public function resetPassword(array $data, $id): void
    {
        $data = $this->validate($data, static::SCENE_RESET_PASSWORD);
        /** @var Admin $model */
        $model = $this->query()->findOrFail($id);
        $model->password = Component::security()->generatePasswordHash($data['new_password']);
        $model->refreshToken();
    }
}
