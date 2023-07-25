<?php

namespace app\admin\controller;

use WebmanTech\AmisAdmin\Controller\AmisSourceController;
use WebmanTech\AmisAdmin\Repository\EloquentRepository;

/**
 * crud 基础控制器.
 */
abstract class AbsSourceController extends AmisSourceController
{
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
