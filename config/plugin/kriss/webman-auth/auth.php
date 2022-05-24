<?php

use Kriss\WebmanAuth\Authentication\FailureHandler\RedirectHandler;
use Kriss\WebmanAuth\Authentication\Method\SessionMethod;
use Kriss\WebmanAuth\Interfaces\IdentityRepositoryInterface;
use Kriss\WebmanAuth\Interfaces\IdentityRepositoryWithTokenInterface;

return [
    'default' => 'user',
    'guards' => [
        'user' => [
            'identityRepository' => function () {
                return new User();
            },
            'authenticationMethod' => function (IdentityRepositoryInterface $identityRepository) {
                return new SessionMethod($identityRepository);
            },
            'authenticationFailureHandler' => function () {
                return new RedirectHandler(route('/auth/login'));
            },
        ],
        'admin' => [
            'identityRepository' => function () {
                return new app\model\Admin();
            },
            'authenticationMethod' => function (IdentityRepositoryWithTokenInterface $identityRepository) {
                return new Kriss\WebmanAuth\Authentication\Method\RequestHeaderMethod($identityRepository);
            },
            'authenticationFailureHandler' => function () {
                return new Kriss\WebmanAuth\Authentication\FailureHandler\ThrowExceptionHandler();
            },
        ],
    ],
];
