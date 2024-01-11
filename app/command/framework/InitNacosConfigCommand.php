<?php

namespace app\command\framework;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workbunny\WebmanNacos\Client;

class InitNacosConfigCommand extends Command
{
    protected static $defaultName = 'init-nacos-config';
    protected static $defaultDescription = '初始化 nacos 需要监听的配置';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = config('plugin.workbunny.webman-nacos.app', []);
        if (!($config['enable'] ?? false)) {
            $output->writeln('disabled');
            return self::SUCCESS;
        }
        $listeners = $config['config_listeners'] ?? [];
        if (!$listeners) {
            $output->writeln('empty listeners');
            return self::SUCCESS;
        }
        $client = new Client();
        foreach ($config['config_listeners'] as $listener) {
            [$dataId, $group, $tenant, $configPath] = $listener;
            $output->writeln('get ' . $dataId . ' => ' . $configPath);
            $res = $client->config->get($dataId, $group, $tenant);
            file_put_contents($configPath, $res, LOCK_EX);
        }

        return self::SUCCESS;
    }
}
