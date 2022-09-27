<?php

namespace support\facade;

use Illuminate\Http\Client\PendingRequest;

/**
 * @method static PendingRequest httpbin()
 */
class Http extends \WebmanTech\LaravelHttpClient\Facades\Http
{
    public static function getAllMacros(): array
    {
        return [
            'httpbin' => function() {
                return Http::baseUrl('https://httpbin.org')
                    ->asJson();
            }
        ];
    }
}
