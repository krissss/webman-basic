<?php

namespace app\components;

use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class Tools
{
    private static ?string $localIp = null;

    /**
     * 获取本机ip.
     */
    public static function getLocalIp(): string
    {
        if (null !== self::$localIp) {
            return self::$localIp;
        }

        $fn = function () {
            $envIp = get_env('SERVER_LOCAL_IP', 'localhost');
            if ($envIp && 'localhost' !== $envIp) {
                return $envIp;
            }
            // windows
            if ('WIN' === strtoupper(substr(\PHP_OS, 0, 3))) {
                $process = Process::fromShellCommandline('ipconfig | findstr /i "IPv4"');
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
            $process = Process::fromShellCommandline("ip address show eth0 | head -n4 | grep inet | awk '{print$2}' | awk -F '/' '{print $1}'");
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException('获取本机IP失败，请手动指定');
            }

            return trim($process->getOutput());
        };

        return self::$localIp = $fn();
    }

    private static ?int $localPort = null;

    /**
     * 获取本服务端口.
     */
    public static function getLocalServerPort(): int
    {
        if (null !== self::$localPort) {
            return self::$localPort;
        }

        return self::$localPort = parse_url(config('server.listen'))['port'];
    }

    /**
     * 构建缓存键.
     *
     * @param mixed $keys
     */
    public static function buildKey($keys): string
    {
        if (\is_string($keys) && \strlen($keys) <= 32) {
            return $keys;
        }

        return md5(serialize($keys));
    }

    /**
     * 递归创建目录.
     */
    public static function makeDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            $dir = \dirname($dir);
        }
        if (file_exists($dir)) {
            return true;
        }

        return mkdir($dir, 0755, true);
    }

    /**
     * 构造并抛出 ValidationException.
     */
    public static function buildValidationException(array $errors): ValidationException
    {
        $validator = validator()->make([], []);
        foreach ($errors as $key => $value) {
            $validator->errors()->add($key, $value);
        }

        return new ValidationException($validator);
    }

    /**
     * 格式化 Bytes.
     *
     * @param string|int|null $size
     */
    public static function formatBytes($size, int $precision = 2): string
    {
        if (0 === $size || null === $size) {
            return '0B';
        }

        $sign = $size < 0 ? '-' : '';
        $size = abs($size);

        $base = log($size) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return $sign.round(1024 ** ($base - floor($base)), $precision).$suffixes[(int) floor($base)];
    }
}
