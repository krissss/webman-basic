<?php

namespace app\command\framework\traits;

trait ModelClassTrait
{
    private const SIGNATURE_MODEL_CLASS = '{modelClass : 模型名：Product 或 app/model/Product 或 app\model\Product}';
    private const SIGNATURE_MODEL_CLASS_OR_PATH = '{modelClassOrPath : 模型名：Product 或 app/model/Product 或 app\model\Product，模型路径：app/model}';

    /**
     * @return string
     */
    private function getModelClassByArgument(): string
    {
        $modelClass = $this->argument('modelClass');
        $modelClass = str_replace('/', '\\', $modelClass);
        if (!str_starts_with($modelClass, 'app\\model\\')) {
            $modelClass = 'app\\model\\' . $modelClass;
        }

        return $modelClass;
    }

    private function getModelClassOrPathByArgument(): array
    {
        $modelClassOrPath = $this->argument('modelClassOrPath');
        $modelClass = str_replace('/', '\\', $modelClassOrPath);
        if (!str_starts_with($modelClass, 'app\\model\\')) {
            $modelClass = 'app\\model\\' . $modelClass;
        }
        if (class_exists($modelClass)) {
            return ['class', $modelClass];
        }

        $modelPath = base_path(str_replace('\\', '/', $modelClassOrPath));
        if (is_dir($modelPath)) {
            return ['path', $modelPath];
        }

        throw new \InvalidArgumentException("{$modelClassOrPath} 不是一个有效的模型类或路径");
    }

    /**
     * @param string $fullClassName
     * @return array{namespace: string, className: string, filename: string}
     */
    private function getClassInfo(string $fullClassName): array
    {
        return [
            'namespace' => substr($fullClassName, 0, strrpos($fullClassName, '\\')),
            'className' => substr($fullClassName, strrpos($fullClassName, '\\') + 1),
            'filename' => base_path(str_replace(['\\'], '/', $fullClassName) . '.php'),
        ];
    }
}
