<?php

namespace app\user\controller;

use app\admin\controller\repository\UserRepository;

/**
 * 当前登录用户信息.
 */
class InfoController extends \app\admin\controller\InfoController
{
    /**
     * {@inheritdoc}
     */
    protected string $repository = UserRepository::class;
    /**
     * {@inheritdoc}
     */
    protected string $routeInfo = 'user.info';

    protected string $routeInfoUpdate = 'user.info.update';

    protected string $routeChangePassword = 'user.info.changePassword';
}
