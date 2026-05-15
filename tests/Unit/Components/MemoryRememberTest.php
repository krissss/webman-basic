<?php

use app\components\MemoryRemember;

test('it stores values with scalar and composite keys', function () {
    $remember = new MemoryRemember();

    $remember->set('plain-key', 'value');
    $remember->set(['module' => 'admin', 'id' => 1], ['name' => 'root']);

    expect($remember->get('plain-key'))->toBe('value')
        ->and($remember->get(['module' => 'admin', 'id' => 1]))->toBe(['name' => 'root'])
        ->and($remember->get('missing', 'fallback'))->toBe('fallback');
});
