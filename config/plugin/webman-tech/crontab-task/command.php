<?php

use WebmanTech\CrontabTask\Commands\CrontabTaskExecCommand;
use WebmanTech\CrontabTask\Commands\CrontabTaskListCommand;
use WebmanTech\CrontabTask\Commands\MakeTaskCommand;

return [
    CrontabTaskListCommand::class,
    CrontabTaskExecCommand::class,
    MakeTaskCommand::class,
];
