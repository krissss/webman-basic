<?php

use app\components\Security;

test('it validates generated password hashes', function () {
    $security = new Security();
    $hash = $security->generatePasswordHash('secret');

    expect($security->validatePassword('secret', $hash))->toBeTrue()
        ->and($security->validatePassword('invalid', $hash))->toBeFalse();
});

test('it generates random strings with requested length', function () {
    $security = new Security();

    expect($security->generateRandomString(24))->toHaveLength(24);
});
