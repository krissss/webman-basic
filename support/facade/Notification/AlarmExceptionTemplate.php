<?php

namespace support\facade\Notification;

class AlarmExceptionTemplate extends AlarmTemplate
{
    protected int $debugBackTraceIndex = 1;

    public function __construct(\Throwable $e, string $title = '', array $infos = [])
    {
        $infos = array_merge(
            [
                '异常' => mb_substr($e->getMessage(), 0, 500),
                '来源' => $this->formatOriginFileLine($e->getFile(), $e->getLine()),
            ],
            $infos,
        );

        parent::__construct($infos, $title);
    }
}
