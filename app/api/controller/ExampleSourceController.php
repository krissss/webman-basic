<?php

namespace app\api\controller;

use app\api\controller\form\example\ExampleCreateForm;
use app\api\controller\form\example\ExampleListSearchForm;
use app\api\controller\form\example\ExampleUpdateForm;
use app\exception\UserSeeException;
use app\model\Admin as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;
use support\facade\Auth;
use Webman\Http\Request;
use Webman\Http\Response;
use WebmanTech\Swagger\DTO\SchemaConstants;

#[OA\Tag(name: 'crudExample')]
final class ExampleSourceController
{
    #[OA\Get(
        path: '/crud',
        summary: '列表',
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => ExampleListSearchForm::class . '@handle',
        ],
    )]
    public function index(): Response
    {
        return ExampleListSearchForm::fromRequest()
            ->handle()
            ->toResponse();
    }

    #[OA\Get(
        path: '/crud/{id}',
        summary: '详情',
        x: [
            SchemaConstants::X_SCHEMA_RESPONSE => Model::class,
        ]
    )]
    public function show(Request $request, #[OA\PathParameter] int $id): Response
    {
        $model = Model::findOrFail($id);

        return json_success($model);
    }

    #[OA\Post(
        path: '/crud',
        summary: '新建',
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => ExampleCreateForm::class . '@handle',
        ],
    )]
    public function store(): Response
    {
        return json_success(
            ExampleCreateForm::fromRequest()
                ->handle()
        );
    }

    #[OA\Put(
        path: '/crud/{id}',
        summary: '更新',
        x: [
            SchemaConstants::X_SCHEMA_REQUEST => ExampleUpdateForm::class . '@handle',
        ],
    )]
    public function update(): Response
    {
        return json_success(
            ExampleUpdateForm::fromRequest()
                ->handle()
        );
    }

    #[OA\Delete(
        path: '/crud/{id}',
        summary: '删除',
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
    )]
    public function recovery(Request $request, #[OA\PathParameter] int $id): Response
    {
        if (!Model::whereKey($id)->restore()) {
            throw new ModelNotFoundException();
        }

        return json_success(Model::find($id));
    }
}
