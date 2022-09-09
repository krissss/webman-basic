<?php

namespace app\user\controller;

use app\admin\controller\repository\UserRepository;

/**
 * 当前登录用户信息
 */
class InfoController extends \app\admin\controller\InfoController
{
    /**
     * @inheritdoc
     */
    protected string $repository = UserRepository::class;
    /**
     * @inheritdoc
     */
    protected string $routeInfo = 'user.info';
    /**
     * @var string
     */
    protected string $routeInfoUpdate = 'user.info.update';
    /**
     * @var string
     */
    protected string $routeChangePassword = 'user.info.changePassword';
}
