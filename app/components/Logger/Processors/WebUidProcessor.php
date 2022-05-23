<?php

namespace app\components\Logger\Processors;

use Monolog\Processor\UidProcessor;

class WebUidProcessor extends UidProcessor
{
    public const REQUEST_ATTRIBUTE = 'log_uid';

    /**
     * {@inheritDoc}
     */
    public function __invoke(array $record): array
    {
        $uid = null;
        if ($request = request()) {
            $uid = $request->{static::REQUEST_ATTRIBUTE};
        }

        $record['extra']['uid'] = $uid ?: $this->getUid();

        return $record;
    }

    public static function generateUid2(): string
    {
        $length = 7;
        return 'r' . substr(bin2hex(random_bytes((int)ceil($length / 2))), 0, $length);
    }
}
