<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\ValidationException;
use app\model\Admin as Model;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;

/**
 * 授权
 */
class AuthController
{
    /**
     * @var string|Model
     */
    protected string $model = Model::class;
    /**
     * @var string
     */
    protected string $routeRedirect = '/admin/login';

    /**
     * 登录
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        $data = $validator->validate();

        /** @var Model $model */
        $model = $this->model::query()->where('username', $data['username'])->first();
        if (!$model || !Component::security()->validatePassword($data['password'], $model->password)) {
            throw new ValidationException([
                'username' => trans('用户名或密码错误'),
            ]);
        }

        Auth::guard()->login($model);
        $model->refreshToken();
        $model->makeVisible(['access_token']);

        return admin_response($model);
    }

    /**
     * 退出登录
     * @return Response
     */
    public function logout(): Response
    {
        if (!Auth::guard()->isGuest()) {
            Auth::guard()->getUser()->refreshToken(null);
            Auth::guard()->logout();
        }

        return admin_redirect($this->routeRedirect, '退出成功');
    }
}
