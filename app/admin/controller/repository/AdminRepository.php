<?php

namespace app\admin\controller\repository;

use app\components\Component;
use app\enums\AdminStatus;
use app\model\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use WebmanTech\AmisAdmin\Repository\EloquentRepository;

class AdminRepository extends EloquentRepository
{
    public const SCENE_RESET_PASSWORD = 'reset_password';

    public function __construct()
    {
        parent::__construct(Admin::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'name' => '名称',
            'password' => '密码',
            'access_token' => 'Access Token',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'new_password' => '新密码',
            'new_password_confirmation' => '重复密码',
        ];
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
    protected function rules(string $scene): array
    {
        if ($scene === static::SCENE_CREATE) {
            return [
                'username' => 'required|string|min:4',
                'name' => 'required|string|max:32',
                'password' => 'required|string|min:6|max:32',
            ];
        }
        if ($scene === static::SCENE_UPDATE) {
            return [
                'username' => 'string|min:4',
                'name' => 'string|max:32',
                'status' => Rule::in(AdminStatus::getValues()),
            ];
        }
        if ($scene === static::SCENE_RESET_PASSWORD) {
            return [
                'new_password' => 'required|string|min:6|max:32|confirmed',
                'new_password_confirmation' => 'string',
            ];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function searchableAttributes(): array
    {
        return [
            'username' => fn (Builder $query, $value, $attribute) => $query->where($attribute, $value),
            'name' => fn (Builder $query, $value, $attribute) => $query->where($attribute, 'like', '%'.$value.'%'),
            'status' => fn (Builder $query, $value, $attribute) => $query->where($attribute, $value),
            'created_at' => fn (Builder $query, $value, $attribute) => $query
                ->whereBetween($attribute, array_map(
                    fn ($timestamp) => date('Y-m-d H:i:s', (int) $timestamp),
                    explode(',', $value)
                )),
        ];
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
        if (null === $model->status) {
            $model->status = AdminStatus::ENABLE;
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
