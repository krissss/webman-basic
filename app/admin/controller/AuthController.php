<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\ValidationException;
use app\model\Admin as Model;
use support\facade\Auth;
use support\Request;
use Webman\Http\Response;

/**
 * 授权.
 */
class AuthController
{
    /**
     * @var string|class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected string $model = Model::class;

    protected string $routeRedirect = '/admin/login';

    /**
     * 登录.
     */
    public function login(Request $request): Response
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        $data = $validator->validate();

        /** @var Model $modelClass */
        $modelClass = $this->model;
        /** @var Model|null $model */
        $model = $modelClass::query()->where('username', $data['username'])->first();
        if (!$model || !Component::security()->validatePassword($data['password'], $model->password)) {
            throw new ValidationException(['username' => trans('用户名或密码错误')]);
        }

        Auth::guard()->login($model);
        $model->refreshToken();
        $model->makeVisible(['access_token']);

        return admin_response($model);
    }

    /**
     * 退出登录.
     */
    public function logout(): Response
    {
        if (!Auth::guard()->isGuest()) {
            /** @var Model $user */
            $user = Auth::guard()->getUser();
            $user->refreshToken(null);
            Auth::guard()->logout();
        }

        return admin_redirect($this->routeRedirect, '退出成功');
    }
}
