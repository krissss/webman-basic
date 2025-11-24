<?php

namespace app\command\framework;

use Illuminate\Console\Command;
use Illuminate\Process\Pool;
use WebmanTech\LaravelProcess\Facades\Process;

class EnvironmentCiCommand extends Command
{
    public const COMMAND_NAME = 'environment:ci';

    protected $signature = self::COMMAND_NAME;
    protected $description = '环境搭建的 ci 流程';

    public function handle()
    {
        if (get_env('SKIP_ENV_CI')) {
            echo 'skip by SKIP_ENV_CI' . PHP_EOL;
            return;
        }

        $commandArtisan = 'php artisan ';
        $commandComposer = 'composer ';

        if (!file_exists(base_path('env.php')) && !file_exists(base_path('env.local.php'))) {
            $this->runParallelCommands('php init --env=dev.local --overwrite=skip');
        }
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

        Process::instance()
            ->pool(fn(Pool $pool) => collect($commands)
                ->mapWithKeys(fn($command, $name) => [is_numeric($name) ? $command : $name => $command])
                ->each(fn($command, $name) => $pool
                    ->as($command)
                    ->command($command)
                    ->env([
                        'COMPOSER_ALLOW_SUPERUSER' => 1,
                    ])
                )
            )
            ->start(fn(string $type, string $output, string $name) => $this->log($name, $output, $type))
            ->wait();
    }

    private function log(string $title, string $output, string $type)
    {
        $time = date('Y-m-d H:i:s');
        $this->line("[$time][$title][$type]: \n{$output}");
    }
}
