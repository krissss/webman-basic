<?php

namespace app\command\framework;

use app\components\BaseTask;
use process\Task;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrontabTaskListCommand extends Command
{
    protected static $defaultName = 'crontab:task-list';
    protected static $defaultDescription = '展示 cron task 进程的定时任务名和执行时间';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = ['name', 'class', 'interval'];

        $rows = [];
        foreach (Task::processes() as $name => $processClass) {
            $process = new $processClass();
            if ($process instanceof BaseTask) {
                $rows[] = [$name, $processClass, $process->getCrontab()];
            }
        }

        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();

        return self::SUCCESS;
    }

}
