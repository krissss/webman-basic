<?php

namespace app\components\Logger\Processors;

use Monolog\Processor\ProcessorInterface;

class WebRouteProcessor implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $record)
    {
        if (!isset($record['context']['route'])) {
            $path = '/';
            if ($request = request()) {
                $path = $request->path();
            }
            $record['context']['route'] = $path;
        }
        return $record;
    }
}
