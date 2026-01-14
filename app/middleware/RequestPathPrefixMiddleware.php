<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use Workerman\Protocols\Http\Session;

/**
 * 部署域名的多级目录访问支持
 * 如部署到 www.xxx.com/xxx/yyy 时，此时配置 app.domain_path 为 /xxx/yyy
 */
class RequestPathPrefixMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $pathPrefix = '';
        if ($request instanceof \support\Request) {
            $pathPrefix = $request->pathPrefix();
        }
        if ($pathPrefix) {
            // 处理 cookie
            Session::$cookiePath = rtrim($pathPrefix . Session::$cookiePath, '/');
        }

        /** @var Response $response */
        $response = $handler($request);

        if ($pathPrefix) {
            // 处理 html 上的静态资源的绝对路劲（添加 pathPrefix）
            $content = $response->rawBody();
            if (str_contains($content, '<html') && str_contains($content, '<head') && str_contains($content, '<body')) {
                $pathPrefix = ltrim($pathPrefix, '/');
                $pattern = '/(href|src)="\/(.*?)\.(css|js|jpg|png|gif|svg|ico)(\?[^"]*)?"/';
                $replacement = '$1="/' . $pathPrefix . '/$2.$3"';
                $content = preg_replace($pattern, $replacement, $content);
                $response->withBody($content);
            }
        }

        return $response;
    }
}
