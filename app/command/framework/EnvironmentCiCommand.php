<?php

namespace app\command\framework;

use Illuminate\Console\Command;
use Kriss\MultiProcess\MultiProcess;
use Kriss\MultiProcess\PendingProcess;

class EnvironmentCiCommand extends Command
{
    public const COMMAND_NAME = 'environment:ci';

    protected $signature = self::COMMAND_NAME;
    protected $description = '环境搭建的 ci 流程';

    public function handle()
    {
        $commandArtisan = 'php artisan ';
        $commandComposer = 'composer ';

        if (!file_exists(base_path('env.php')) && !file_exists(base_path('env.local.php'))) {
            $this->runParallelCommands('php init --env=dev.local --overwrite=skip');
        }
        $this->runParallelCommands($commandArtisan . 'init-nacos-config');
        if (file_exists(base_path('phinx.php'))) {
            $this->runParallelCommands($commandComposer . 'phinx migrate');
        }
        $this->runParallelCommands([
            $commandArtisan . 'init-data',
            $commandArtisan . 'storage:link',
        ]);
    }

    private function runParallelCommands($commands = [])
    {
        if (is_string($commands)) {
            $commands = [$commands];
        }
        $mp = MultiProcess::create();

        collect($commands)
            ->mapWithKeys(fn ($command, $name) => [is_numeric($name) ? $command : $name => $command])
            ->each(fn ($command, $name) => $mp->add(
                PendingProcess::createFromCommand($command)
                    ->setStartCallback(fn ($type, $buffer) => $this->log($name, $buffer, $type)),
                $name
            ));

        $mp->wait();
    }

    private function log(string $title, string $output, string $type)
    {
        $this->line("[$title][$type]: \n{$output}");
    }
}
