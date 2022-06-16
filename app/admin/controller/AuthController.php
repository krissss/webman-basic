<?php

namespace app\admin\controller;

use app\components\Component;
use app\exception\UserSeeException;
use app\model\Admin;
use OpenApi\Annotations as OA;
use support\facade\Auth;
use support\Request;
use support\Response;

/**
 * @OA\Tag(name="auth", description="授权登录")
 */
class AuthController
{
    /**
     * 登录
     *
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"username", "password"},
     *                 @OA\Property(property="username", description="用户名", type="string", example="admin"),
     *                 @OA\Property(property="password", description="密码", type="string", example="123456"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="ok"),
     * )
     */
    public function login(Request $request): Response
    {
        $validator = validator($request->post(), [
            'username' => 'required|string|max:32',
            'password' => 'required|string|max:32',
        ]);
        $data = $validator->validate();

        $admin = Admin::query()->where('username', $data['username'])->first();
        if (!$admin || !Component::security()->validatePassword($data['password'], $admin->password)) {
            throw new UserSeeException(trans('auth.user_password_error'));
        }

        Auth::guard()->login($admin);
        $admin->refreshToken();

        return json_success($admin);
    }

    /**
     * 退出登录
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"auth"},
     *     security={{"api_key": {}}},
     *     @OA\Response(response="200", description="ok"),
     * )
     */
    public function logout(): Response
    {
        if (Auth::guard()->isGuest()) {
            return json_success('guest');
        }

        Auth::identityAdmin()->refreshToken(null);
        Auth::guard()->logout();

        return json_success('logout');
    }
}
