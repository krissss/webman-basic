<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class TransformRequest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        /** @var \support\facade\Request $request */
        $request->replaceGet($this->cleanArray($request->get()));
        $request->replacePost($this->cleanArray($request->post()));

        return $handler($request);
    }

    /**
     * @param array $values
     * @param string $keyPrefix
     * @return array
     */
    protected function cleanArray(array $values, string $keyPrefix = ''): array
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->cleanValue($keyPrefix . $key, $value);
        }
        return $values;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function cleanValue(string $key, $value)
    {
        if (is_array($value)) {
            return $this->cleanArray($value, $key . '.');
        }

        return $this->transform($key, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function transform(string $key, $value)
    {
        return $value;
    }
}
