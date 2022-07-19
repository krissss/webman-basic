<?php

use Kriss\WebmanAuth\Authentication\FailureHandler\RedirectHandler;
use Kriss\WebmanAuth\Authentication\Method\CompositeMethod;
use Kriss\WebmanAuth\Authentication\Method\HttpHeaderMethod;
use Kriss\WebmanAuth\Authentication\Method\SessionMethod;
use Kriss\WebmanAuth\Interfaces\IdentityRepositoryInterface;

return [
    'default' => 'user',
    'guards' => [
        'user' => [
            'identityRepository' => function () {
                return new User();
            },
            'authenticationMethod' => function (IdentityRepositoryInterface $identityRepository) {
                return new CompositeMethod([
                    new SessionMethod($identityRepository, ['tokenType' => 'session']),
                    new HttpHeaderMethod($identityRepository, ['tokenType' => 'token']),
                ]);
            },
            'authenticationFailureHandler' => function () {
                return new RedirectHandler(route('/auth/login'));
            },
        ],
        'admin' => [
            'identityRepository' => function () {
                return new app\model\Admin();
            },
            'authenticationMethod' => function (IdentityRepositoryInterface $identityRepository) {
                return new CompositeMethod([
                    new SessionMethod($identityRepository, ['tokenType' => 'session']),
                    new HttpHeaderMethod($identityRepository, ['tokenType' => 'token']),
                ]);
            },
            'authenticationFailureHandler' => function () {
                return new Kriss\WebmanAuth\Authentication\FailureHandler\ThrowExceptionHandler();
            },
        ],
    ],
];
