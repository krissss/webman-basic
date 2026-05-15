<?php

use app\components\Tools;

describe('buildKey', function () {
    test('it keeps short string cache keys unchanged', function () {
        expect(Tools::buildKey('user:1'))->toBe('user:1');
    });

    test('it hashes long or composite cache keys deterministically', function () {
        $longKey = str_repeat('a', 33);
        $compositeKey = ['module' => 'admin', 'id' => 1];

        expect(Tools::buildKey($longKey))->toBe(md5(serialize($longKey)))
            ->and(Tools::buildKey($compositeKey))->toBe(md5(serialize($compositeKey)))
            ->and(Tools::buildKey($compositeKey))->toBe(Tools::buildKey($compositeKey));
    });
});

describe('formatBytes', function () {
    test('it formats byte sizes', function (int|null $size, string $expected) {
        expect(Tools::formatBytes($size))->toBe($expected);
    })->with([
        [null, '0B'],
        [0, '0B'],
        [512, '512B'],
        [1024, '1KB'],
        [1536, '1.5KB'],
        [-1024, '-1KB'],
    ]);
});
