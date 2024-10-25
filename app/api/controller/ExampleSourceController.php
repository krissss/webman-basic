<?php

namespace app\api\controller;

use app\api\controller\schema\traits\PaginationTrait;
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
use WebmanTech\Swagger\DTO\SchemaConstants;
use WebmanTech\Swagger\SchemaAnnotation\BaseSchema;

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
        responses: [
            new OA\Response(response: 200, description: '列表数据'),
        ],
        x: [
            SchemaConstants::X_SCHEMA_TO_PARAMETERS => ExampleListSearchSchema::class,
        ]
    )]
    public function index(Request $request): Response
    {
        $schema = ExampleListSearchSchema::create($request->get(), validator());

        $query = Model::query();
        if ($value = $schema->username) {
            $query->where('username', $value);
        }
        if ($value = $schema->status) {
            $query->where('status', $value);
        }

        return json_success($query->paginate($schema->limit));
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
                new OA\JsonContent(ref: ExampleCreateSchema::class)
            ]
        ),
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '新建后的明细'),
        ],
    )]
    public function store(Request $request): Response
    {
        $schema = ExampleCreateSchema::create($request->post(), validator());
        if (Model::query()->where('username', $schema->username)->exists()) {
            throw new UserSeeException('username 已存在');
        }

        $model = new Model($schema->toArray());
        $model->password = Component::security()->generatePasswordHash($schema->password);
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
                new OA\JsonContent(ref: ExampleUpdateSchema::class)
            ]
        ),
        tags: ['crud'],
        responses: [
            new OA\Response(response: 200, description: '更新后的明细'),
        ],
    )]
    public function update(Request $request, #[OA\PathParameter] int $id): Response
    {
        $schema = ExampleCreateSchema::create($request->post(), validator());
        $model = Model::findOrFail($id);
        $model->fill($schema->toArray());

        if ($model->isDirty('username') && Model::query()->where('username', $model->username)->whereKeyNot($model->id)->exists()) {
            throw new UserSeeException('username 已存在');
        }

        if ($schema->password) {
            // 修改密码才刷新 token
            $model->password = Component::security()->generatePasswordHash($schema->password);
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

#[OA\Schema]
class ExampleListSearchSchema extends BaseSchema
{
    use PaginationTrait;

    #[OA\Property(description: '用户名', example: 'admin')]
    public ?string $username = null;

    #[OA\Property(description: '状态', example: 0)]
    public ?int $status = null;
}

#[OA\Schema(required: ['username', 'password', 'name'])]
class ExampleCreateSchema extends BaseSchema
{
    #[OA\Property(description: '用户名', maxLength: 64, example: 'admin')]
    public string $username;

    #[OA\Property(description: '密码', maxLength: 64, example: '123456')]
    public string $password;

    #[OA\Property(description: '名称', example: '测试用户')]
    public string $name;

    protected function validationExtraRules(): array
    {
        return [
            'username' => 'max:64',
            'password' => 'max:64',
        ];
    }
}

#[OA\Schema(required: [])]
class ExampleUpdateSchema extends BaseSchema
{
    #[OA\Property(description: '用户名', maxLength: 64, example: 'admin')]
    public ?string $username = null;

    #[OA\Property(description: '密码', maxLength: 64, example: '123456')]
    public ?string $password = null;

    #[OA\Property(description: '名称', example: '测试用户')]
    public ?string $name = null;

    #[OA\Property(description: '状态', example: 0)]
    public ?int $status = null;

    protected function validationExtraRules(): array
    {
        return [
            'username' => 'max:64',
            'password' => 'max:64',
            'status' => [Rule::in(AdminStatus::getValues())],
        ];
    }
}
