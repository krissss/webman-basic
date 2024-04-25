<?php

namespace app\api\controller;

use app\components\Component;
use app\enums\AdminStatus;
use app\exception\UserSeeException;
use app\model\Admin as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use support\facade\Auth;
use support\Request;
use support\Response;

#[OA\Tag(name: 'crud', description: 'crud 例子')]
class ExampleSourceController
{
    #[OA\Get(
        path: '/crud',
        summary: '列表',
        security: [
            ['api_key' => []]
        ],
        tags: ['crud'],
        parameters: [
            ...[
                new OA\Parameter(name: 'page', description: '页数', in: 'query', schema: new OA\Schema(type: 'integer')),
                new OA\Parameter(name: 'page_size', description: '每页数量', in: 'query', schema: new OA\Schema(type: 'integer')),
            ],
            new OA\Parameter(name: 'username', description: '用户名', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', description: '状态', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: '列表数据'),
        ],
    )]
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

    #[OA\Get(
        path: '/crud/{id}',
        summary: '详情',
        security: [
            ['api_key' => []]
        ],
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '明细'),
        ],
    )]
    public function show(Request $request, #[OA\PathParameter] int $id): Response
    {
        $model = Model::findOrFail($id);

        return json_success($model);
    }

    #[OA\Post(
        path: '/crud',
        summary: '新建',
        security: [
            ['api_key' => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        required: ['username', 'password', 'name'],
                        properties: [
                            new OA\Property(property: 'username', description: '用户名', type: 'string', maxLength: 64, example: 'admin'),
                            new OA\Property(property: 'password', description: '密码', type: 'string', maxLength: 64, example: '123456'),
                            new OA\Property(property: 'name', description: '名称', type: 'string', example: '测试用户'),
                        ],
                        type: 'object'
                    ),
                )
            ]
        ),
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '新建后的明细'),
        ],
    )]
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

    #[OA\Put(
        path: '/crud/{id}',
        summary: '更新',
        security: [
            ['api_key' => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'username', description: '用户名', type: 'string', maxLength: 64, example: 'admin'),
                            new OA\Property(property: 'password', description: '密码', type: 'string', maxLength: 64, example: '123456'),
                            new OA\Property(property: 'name', description: '名称', type: 'string', example: '测试用户'),
                            new OA\Property(property: 'status', description: '状态', type: 'integer', example: 0),
                        ],
                        type: 'object'
                    ),
                )
            ]
        ),
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '更新后的明细'),
        ],
    )]
    public function update(Request $request, #[OA\PathParameter] int $id): Response
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

    #[OA\Delete(
        path: '/crud/{id}',
        summary: '删除',
        security: [
            ['api_key' => []]
        ],
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '无返回数据'),
        ],
    )]
    public function destroy(Request $request, #[OA\PathParameter] int $id): Response
    {
        if ($id == Auth::guard()->getId()) {
            throw new UserSeeException('不能删除自己');
        }

        $model = Model::findOrFail($id);
        $model->delete();

        return json_success(null);
    }

    #[OA\Put(
        path: '/crud/{id}/recovery',
        summary: '恢复',
        security: [
            ['api_key' => []]
        ],
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '明细'),
        ]
    )]
    public function recovery(Request $request, #[OA\PathParameter] int $id): Response
    {
        if (!Model::whereKey($id)->restore()) {
            throw new ModelNotFoundException();
        }

        return json_success(Model::find($id));
    }
}
