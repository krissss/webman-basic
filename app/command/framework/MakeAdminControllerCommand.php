<?php

namespace app\command\framework;

use app\admin\controller\SystemController;
use app\command\framework\traits\ModelClassTrait;
use Illuminate\Console\Command;

class MakeAdminControllerCommand extends Command
{
    use ModelClassTrait;

    protected $signature = 'make:admin-controller ' . self::SIGNATURE_MODEL_CLASS . ' {--f|overwrite : 强制覆盖已经存在的文件}';

    public function handle()
    {
        $modelClass = $this->getModelClassByArgument();
        $modelClassInfo = $this->getClassInfo($modelClass);

        $isOverwrite = $this->option('overwrite');
        // 创建 repository
        $repositoryClass = "app\\admin\\controller\\repository\\{$modelClassInfo['className']}Repository";
        $repositoryClassInfo = $this->getClassInfo($repositoryClass);
        if (!$isOverwrite && file_exists($repositoryClassInfo['filename'])) {
            $this->info('skip repository: ' . $repositoryClassInfo['filename']);
        } else {
            $content = strtr($this->getRepositoryTemplate(), [
                '{{namespace}}' => $repositoryClassInfo['namespace'],
                '{{class}}' => $repositoryClassInfo['className'],
                '{{modelClass}}' => $modelClass,
                '{{modelClassName}}' => $modelClassInfo['className'],
            ]);
            file_put_contents($repositoryClassInfo['filename'], $content);
            $this->info('create file success: ' . $repositoryClassInfo['filename']);
        }

        // 创建 controller
        $controllerClass = "app\\admin\\controller\\{$modelClassInfo['className']}Controller";
        $controllerClassInfo = $this->getClassInfo($controllerClass);
        if (!$isOverwrite && file_exists($controllerClassInfo['filename'])) {
            $this->info('skip controller: ' . $controllerClassInfo['filename']);
        } else {
            $content = strtr($this->getControllerTemplate(), [
                '{{namespace}}' => $controllerClassInfo['namespace'],
                '{{class}}' => $controllerClassInfo['className'],
                '{{repositoryClass}}' => $repositoryClass,
                '{{repositoryClassName}}' => $repositoryClassInfo['className'],
            ]);
            file_put_contents($controllerClassInfo['filename'], $content);
            $this->info('create file success: ' . $controllerClassInfo['filename']);
        }

        // 配置
        $name = lcfirst($modelClassInfo['className']);

        // 配置 route
        $routeText = "Route::resource('{$name}', controller\\{$controllerClassInfo['className']}::class, ['name_prefix' => 'admin.']);";
        $routeFile = base_path('app/admin/route.php');
        $routeFileContent = file_get_contents($routeFile);
        if (str_contains($routeFileContent, substr($routeText, 0, strpos($routeText, ',')))) {
            $this->info('skip route: ' . $routeFile);
        } else {
            file_put_contents($routeFile, (str_ends_with($routeFileContent, "\n") ? '' : "\n") . $routeText, FILE_APPEND);
            $this->info('write route success: ' . $routeFile);
        }

        // 配置菜单
        $menuText = "['label' => '{$name}管理', 'icon' => 'fa fa-circle-o', 'url' => '/{$name}', 'schemaApi' => route('admin.{$name}.index')],";
        $this->info('请将以下配置加到菜单下：' . $this->getClassInfo(SystemController::class)['filename']);
        $this->comment($menuText);
    }

    private function getRepositoryTemplate(): string
    {
        return <<<EOL
<?php

namespace {{namespace}};

use {{modelClass}};
use WebmanTech\AmisAdmin\Amis\DetailAttribute;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Controller\AmisSourceController;

class {{class}} extends AbsRepository
{
    public function __construct()
    {
        parent::__construct({{modelClassName}}::class);

        \$this->getPresetsHelper()
            ->withPresets([

            ]);
    }
}

EOL;

    }

    private function getControllerTemplate(): string
    {
        return <<<EOL
<?php

namespace {{namespace}};

use {{repositoryClass}};
use WebmanTech\AmisAdmin\Repository\RepositoryInterface;

/**
 * @method {{repositoryClassName}} repository()
 */
class {{class}} extends AbsSourceController
{
    /**
     * {@inheritdoc}
     */
    protected function createRepository(): RepositoryInterface
    {
        return new {{repositoryClassName}};
    }
}

EOL;

    }
}
