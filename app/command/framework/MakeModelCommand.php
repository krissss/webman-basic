<?php

namespace app\command\framework;

use app\command\framework\traits\ModelClassTrait;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MakeModelCommand extends Command
{
    use ModelClassTrait;

    protected $signature = 'make:model ' . self::SIGNATURE_MODEL_CLASS . ' {tableName : 表名：product}';

    public function handle()
    {
        $modelClass = $this->getModelClassByArgument();
        if (class_exists($modelClass)) {
            $this->line('class 已存在，将覆盖: ' . $modelClass);
        }
        $tableName = $this->argument('tableName');

        $classInfo = $this->getClassInfo($modelClass);

        $content = strtr($this->getTemplate(), [
            '{{namespace}}' => $classInfo['namespace'],
            '{{class}}' => $classInfo['className'],
            '{{tableName}}' => $tableName,
        ]);
        file_put_contents($classInfo['filename'], $content);
        $this->info('create file success: ' . $classInfo['filename']);

        $artisan = $_SERVER['SCRIPT_NAME'] ?? 'artisan';
        $command = "php {$artisan} make:model-doc \"{$modelClass}\"";

        if (!$tableName) {
            $this->line('未设置 tableName，跳过生成 doc，请补充 tableName 后执行以下命令');
            $this->line($command);
        }

        // 通过子进程触发，因为新建时本次还不能通过 class_exist 获取到这个类的状态
        $process = Process::fromShellCommandline($command);
        $isOk = $process->run();
        if (self::SUCCESS === $isOk) {
            $this->info($process->getOutput());
        } else {
            $this->error($process->getErrorOutput());
        }
    }

    protected function getTemplate(): string
    {
        return <<<EOL
<?php

namespace {{namespace}};

use app\components\BaseModel;

/**
 * doc
 */
class {{class}} extends BaseModel
{
    protected \$table = '{{tableName}}';
    protected \$primaryKey = 'id';
}

EOL;
    }
}
