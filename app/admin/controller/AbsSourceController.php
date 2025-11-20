<?php

namespace app\admin\controller;

//use app\model\Admin;
//use support\facade\Auth;
use WebmanTech\AmisAdmin\Controller\AmisSourceController;
use WebmanTech\AmisAdmin\Repository\EloquentRepository;

/**
 * crud 基础控制器.
 */
abstract class AbsSourceController extends AmisSourceController
{
//    protected ?array $defaultDialogConfig = [
//        'size' => 'lg',
//    ];

//    public function __construct()
//    {
//        $this->hiddenDestroy = !get_env('ADMIN_CAN_SUPERADMIN_DESTROY', false) || Auth::getId() != Admin::SUPER_ADMIN_ID;
//    }

    /**
     * {@inheritdoc}
     */
    protected function authRecovery($id = null): bool
    {
        $repository = $this->repository();
        if ($repository instanceof EloquentRepository) {
            return method_exists($repository->model(), 'trashed');
        }

        return parent::authDestroy($id);
    }
}
