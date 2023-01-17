<?php

namespace support\facade\Notification;

use WebmanTech\Logger\Middleware\RequestUid;

class AlarmTemplate
{
    protected string $title = '';
    protected array $infos = [];
    protected int $debugBackTraceIndex = 0; // 按照继承当前类的层级定义

    public function __construct(array $infos, string $title = '')
    {
        $this->title = $title;
        $this->infos = array_merge(
            [
                '环境' => config('app.name') . (config('app.debug') ? '(debug)' : ''),
                '触发' => $this->getTriggerFile(),
                'uid' => request() ? request()->{RequestUid::REQUEST_UID_KEY} : 'console',
            ],
            $infos,
        );
        //dd($this->infos);
    }

    public function __toString()
    {
        $texts = [];
        if ($this->title) {
            $texts[] = '### ' . $this->title;
        }
        foreach ($this->infos as $key => $value) {
            $texts[] = "- **{$key}**: {$value}";
        }

        return implode(PHP_EOL, $texts);
    }

    private function getTriggerFile(): string
    {
        $debugBackTraceIndex = $this->debugBackTraceIndex + 1; // 由于方法调用 +1
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $debugBackTraceIndex + 1)[$debugBackTraceIndex] ?? [];

        return $this->formatOriginFileLine($trace['file'] ?? '[internal]', $trace['line'] ?? 0);
    }

    protected function formatOriginFileLine(string $filepath, int $line): string
    {
        $filepath = str_replace(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR, '', $filepath);
        return $filepath . ':' . $line;
    }
}
