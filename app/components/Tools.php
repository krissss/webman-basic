<?php

namespace app\components;

use Symfony\Component\Process\Process;

class Tools
{
    private static ?string $localIp = null;

    /**
     * 获取本机ip
     * @return string
     */
    public static function getLocalIp(): string
    {
        if (static::$localIp !== null) {
            return static::$localIp;
        }

        $fn = function () {
            $envIp = get_env('server.local_ip', 'localhost');
            if ($envIp && $envIp !== 'localhost') {
                return $envIp;
            }
            // windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $process = Process:: fromShellCommandline('ipconfig | findstr /i "IPv4"');
                $process->run();
                if (!$process->isSuccessful()) {
                    throw new \RuntimeException('获取本机IP失败，请手动指定');
                }
                $output = $process->getOutput();
                preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $output, $matches);
                if (!isset($matches[0][0])) {
                    throw new \RuntimeException('获取本机IP失败，请手动指定');
                }
                return $matches[0][0];
            }
            // unix
            $process = Process:: fromShellCommandline("ip a | grep 'inet' | grep -v inet6 | grep -v 127* | awk '{print $2}'|awk -F '/' '{print $1}'");
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException('获取本机IP失败，请手动指定');
            }
            return trim($process->getOutput());
        };

        return static::$localIp = $fn();
    }

    private static ?int $localPort = null;

    /**
     * 获取本服务端口
     * @return int
     */
    public static function getLocalServerPort(): int
    {
        if (static::$localPort !== null) {
            return static::$localPort;
        }
        return static::$localPort = parse_url(config('server.listen'))['port'];
    }

    /**
     * 构建缓存键
     * @param mixed $keys
     * @return string
     */
    public static function buildCacheKey($keys): string
    {
        if (is_string($keys) && strlen($keys) <= 32) {
            return $keys;
        }
        return md5(serialize($keys));
    }

    /**
     * 递归创建目录
     * @param string $dir
     * @return bool
     */
    public static function makeDirectory(string $dir): bool
    {
        if (file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }
        return mkdir($dir, 0755, true);
    }
}
