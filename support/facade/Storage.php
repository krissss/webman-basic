<?php

namespace support\facade;

use app\components\Tools;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use support\Request;

class Storage extends \WebmanTech\LaravelFilesystem\Facades\Storage
{
    /**
     * 给浏览器提供用的临时 url
     * 在 ttl 有效期内，不会重复生成签名，防止浏览器缓存不起作用的问题
     * 但会导致每次调用该方式时实际生成的 url 并非在当前时间往后 ttl 时间内可用
     */
    public static function temporaryUrlForBrowser(string $path, array $options = [], int $ttl = 3600, ?Filesystem $disk = null): string
    {
        $disk ??= static::instance()->disk();
        if (!$disk instanceof FilesystemAdapter) {
            throw new \InvalidArgumentException('Disk must be instance of FilesystemAdapter');
        }
        $adapter = $disk->getAdapter();
        if ($adapter instanceof LocalFilesystemAdapter) {
            // local 不需要签名
            $url = $disk->url($path);
            if (str_starts_with($url, '/')) {
                // 非域名模式时，需要补充前缀
                $request = request();
                if (self::isRequestFromAppFront($request)) {
                    return get_env('APP_FRONT_PROXY_PREFIX', '/api/server') . $url;
                } else {
                    $url = '/' . ltrim($request->pathPrefix() . $url, '/');
                }
            }
            return $url;
        }
        if (!get_env('OSS_BUCKET_USE_PRIVATE')) {
            // 未使用 private 的 bucket 直接返回 url
            return $disk->url($path);
        }
        // 需要签名的，给签名的数据缓存一段时间
        $expiration = Carbon::now()->addSeconds($ttl);
        return cache()->remember(
            'storage:temp_url:' . Tools::buildKey([__CLASS__, __FUNCTION__, func_get_args()]),
            $ttl - 30,
            fn() => $disk->temporaryUrl($path, $expiration, $options)
        );
    }

    private static function isRequestFromAppFront(Request $request): bool
    {
        if ($request->header('x-proxy-from-node') === 'true') {
            // 前端代理主动设置的 header
            return true;
        }
        if ($request->header('user-agent') === 'node') {
            // 前端刷新页面时，会使用 node 后端来请求，此时的标志
            return true;
        }
        if ($referer = $request->header('referer')) {
            if (
                !str_contains($referer, '/api/') // 带有 / 后缀是为了更加精确
                && !str_contains($referer, '/admin') // 不带 / 后缀是因为 admin 的后台 amis 访问是不以 / 结尾的
            ) {
                // 有 referer，但是 referer 中不是 /api 或 /admin 端口的
                return true;
            }
        }
        return false;
    }
}
