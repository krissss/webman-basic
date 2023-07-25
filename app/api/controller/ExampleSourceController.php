<?php

namespace app\api\controller;

use app\components\Component;
use app\enums\AdminStatus;
use app\exception\UserSeeException;
use app\model\Admin as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;
use support\facade\Auth;
use support\Request;
use support\Response;

/**
 * @OA\Tag(name="crud", description="crud 例子")
 */
class ExampleSourceController
{
    /**
     * 列表.
     *
     * @OA\Get(
     *     path="/crud",
     *     tags={"crud"},
     *
     *     @OA\Parameter(name="page", in="query", description="页数", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", description="每页数量", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="username", in="query", description="用户名", @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="状态", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="列表数据"),
     *     security={{"api_key": {}}},
     * )
     */
    public function index(Request $request): Response
    {
        $query = Model::query();
        if ($value = $request->get('username')) {
            $query->where('username', $value);
        }
        if ($value = $request->get('status')) {
            $query->where('status', $value);
        }

        return json_success($query->paginate($request->get('page_size')));
    }

    /**
     * 详情.
     *
     * @OA\Get(
     *     path="/crud/{id}",
     *     tags={"crud"},
     *
     *     @OA\Parameter(name="id", in="path", description="id", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="明细"),
     *     security={{"api_key": {}}},
     * )
     */
    public function show(Request $request, int $id): Response
    {
        $model = Model::findOrFail($id);

        return json_success($model);
    }

    /**
     * 新建.
     *
     * @OA\Post(
     *     path="/crud",
     *     tags={"crud"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"adminname", "password", "name"},
     *
     *                 @OA\Property(property="username", description="用户名", type="string", example="admin"),
     *                 @OA\Property(property="password", description="密码", type="string", example="123456"),
     *                 @OA\Property(property="name", description="名称", type="string", example="测试用户"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="新建后的明细"),
     *     security={{"api_key": {}}},
     * )
     */
    public function store(Request $request): Response
    {
        $data = validator($request->post(), [
            'username' => 'required|string|max:64',
            'password' => 'required|string|max:64',
            'name' => 'required|string',
        ])->validate();
        if (Model::query()->where('username', $data['username'])->exists()) {
            throw new UserSeeException('username 已存在');
        }

        $model = new Model($data);
        $model->password = Component::security()->generatePasswordHash($data['password']);
        $model->refreshToken();
        $model->refresh();

        return json_success($model);
    }

    /**
     * 更新.
     *
     * @OA\Put(
     *     path="/crud/{id}",
     *     tags={"crud"},
     *
     *     @OA\Parameter(name="id", in="path", description="id", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(property="username", description="用户名", type="string", example="admin"),
     *                 @OA\Property(property="password", description="密码", type="string", example="123456"),
     *                 @OA\Property(property="name", description="名称", type="string", example="测试用户"),
     *                 @OA\Property(property="status", description="状态", type="integer", example="0"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="新建后的明细"),
     *     security={{"api_key": {}}},
     * )
     */
    public function update(Request $request, int $id): Response
    {
        $model = Model::findOrFail($id);
        $data = validator($request->post(), [
            'username' => 'string|max:64',
            'password' => 'string|max:64',
            'name' => 'string',
            'status' => ['integer', Rule::in(AdminStatus::getValues())],
        ])->validate();
        $model->fill($data);

        if ($model->isDirty('username') && Model::query()->where('username', $data['username'])->whereKeyNot($model->id)->exists()) {
            throw new UserSeeException('username 已存在');
        }

        if (isset($data['password']) && $data['password']) {
            // 修改密码才刷新 token
            $model->password = Component::security()->generatePasswordHash($data['password']);
            $model->refreshToken();
        } else {
            $model->save();
        }

        return json_success($model);
    }

    /**
     * 删除.
     *
     * @OA\Delete(
     *     path="/crud/{id}",
     *     tags={"crud"},
     *
     *     @OA\Parameter(name="id", in="path", description="id", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="无返回数据"),
     *     security={{"api_key": {}}},
     * )
     */
    public function destroy(Request $request, int $id): Response
    {
        if ($id == Auth::guard()->getId()) {
            throw new UserSeeException('不能删除自己');
        }

        $model = Model::findOrFail($id);
        $model->delete();

        return json_success(null);
    }

    /**
     * 恢复.
     *
     * @OA\Put(
     *     path="/crud/{id}/recovery",
     *     tags={"crud"},
     *
     *     @OA\Parameter(name="id", in="path", description="id", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="明细"),
     *     security={{"api_key": {}}},
     * )
     */
    public function recovery(Request $request, int $id): Response
    {
        if (!Model::whereKey($id)->restore()) {
            throw new ModelNotFoundException();
        }

        return json_success(Model::find($id));
    }
}
