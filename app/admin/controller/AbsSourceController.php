<?php

namespace app\admin\controller;

use Kriss\WebmanAmisAdmin\Controller\AmisSourceController;
use Kriss\WebmanAmisAdmin\Repository\EloquentRepository;

/**
 * crud 基础控制器
 */
abstract class AbsSourceController extends AmisSourceController
{
    /**
     * @inheritdoc
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
