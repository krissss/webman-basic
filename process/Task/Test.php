<?php

namespace process\Task;

class Test
{
    public static function handle()
    {
        echo date('Y-m-d H:i:s') . ' Test task' . PHP_EOL;
    }
}
