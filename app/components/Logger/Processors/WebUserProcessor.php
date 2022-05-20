<?php

namespace app\components\Logger\Processors;

use Monolog\Processor\ProcessorInterface;

class WebUserProcessor implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $record)
    {
        if (!isset($record['context']['ip'])) {
            $ip = '0.0.0.0';
            if ($request = request()) {
                $ip = $request->getRealIp();
            }
            $record['context']['ip'] = $ip;
        }
        if (!isset($record['context']['userId'])) {
            $userId = 0;
            // TODO user 实现
            $record['context']['userId'] = $userId;
        }
        return $record;
    }
}
