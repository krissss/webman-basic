<?php

namespace app\command\framework;

use app\command\framework\traits\ModelClassTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Symfony\Component\Finder\Finder;

class MakeModelDocCommand extends Command
{
    use ModelClassTrait;

    protected $signature = 'make:model-doc ' . self::SIGNATURE_MODEL_CLASS_OR_PATH;

    public function handle()
    {
        [$type, $value] = $this->getModelClassOrPathByArgument();

        if ($type === 'class') {
            $this->handleModelProperty($value);
            return;
        }
        if ($type === 'path') {
            foreach (Finder::create()->in($value)->files()->name('*.php') as $file) {
                $modelClass = ltrim(str_replace([base_path(), \DIRECTORY_SEPARATOR, '.php'], ['', '\\', ''], $file->getRealPath()), '\\');
                $this->handleModelProperty($modelClass);
            }
            return;
        }

        throw new \InvalidArgumentException('Not support type: ' . $type);
    }

    protected function handleModelProperty($modelClass): void
    {
        $model = new $modelClass();
        if (!$model instanceof Model) {
            $this->info('skip: ' . $modelClass);

            return;
        }
        $modelClass = $model::class;

        // 读取数据库
        $table = $model->getTable();
        $connectionName = $model->getConnectionName() ?? config('database.default');
        $database = config("database.connections.{$connectionName}.database");
        $columns = $model->getConnection()
            ->select('select COLUMN_NAME,DATA_TYPE,COLUMN_KEY,COLUMN_COMMENT,IS_NULLABLE from INFORMATION_SCHEMA.COLUMNS where table_name = ? and TABLE_SCHEMA = ? ORDER BY ORDINAL_POSITION', [
                $table,
                $database,
            ]);
        $properties = [
            '/**',
            ' * ' . $modelClass,
            ' *',
        ];
        $dates = $model->getDates();
        $primaryKey = $model->getKey();
        foreach ($columns as $item) {
            if ('PRI' === $item->COLUMN_KEY) {
                $primaryKey = $item->COLUMN_NAME;
                $item->COLUMN_COMMENT .= '(主键)';
            }
            $type = $this->getType($item->DATA_TYPE, 'YES' === $item->IS_NULLABLE);
            if ('string' === $type && \in_array($item->COLUMN_NAME, $dates)) {
                $type = '\\' . Carbon::class;
            }
            $properties[] = implode(' ', array_filter([
                " * @property $type",
                '$' . $item->COLUMN_NAME,
                str_replace("\r\n", ' ', $item->COLUMN_COMMENT ?? ''),
            ]));
        }
        $properties[] = ' *';
        $properties[] = ' * others';
        $properties[] = ' */';
        $docProperty = implode("\n", $properties);

        // 写入
        $modelClass = $model::class;
        $filename = base_path(str_replace('\\', \DIRECTORY_SEPARATOR, $modelClass) . '.php');
        $content = file_get_contents($filename);
        // 替换 doc
        preg_match('/\/\*\*(.*?)class /s', $content, $matches);
        if (isset($matches[1]) && $matches[1]) {
            $content = str_replace('/**' . $matches[1], "{$docProperty}\n", $content);
        }
        // 更新 primaryKey
        $content = preg_replace("/protected \\\$primaryKey = '(.*)';/", "protected \$primaryKey = '{$primaryKey}';", $content);

        file_put_contents($filename, $content);
        $this->info('write doc success: ' . $modelClass);
    }

    protected function getType(string $type, bool $nullable = false): string
    {
        $type = match ($type) {
            'varchar', 'string', 'text', 'longtext', 'date', 'time', 'guid', 'datetimetz', 'datetime', 'enum', 'timestamp', 'bigint' => 'string',
            'decimal' => 'numeric',
            'boolean', 'int', 'tinyint', 'smallint' => 'int',
            'float' => 'float',
            default => 'mixed',
        };

        return $nullable ? ($type . '|null') : $type;
    }
}
