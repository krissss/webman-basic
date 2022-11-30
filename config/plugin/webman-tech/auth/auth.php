<?php

use WebmanTech\Auth\Authentication\Method\CompositeMethod;
use WebmanTech\Auth\Authentication\Method\HttpHeaderMethod;
use WebmanTech\Auth\Authentication\Method\SessionMethod;
use WebmanTech\Auth\Interfaces\IdentityRepositoryInterface;

return [
    'default' => 'user',
    'guards' => [
        'user' => [
            'identityRepository' => function () {
                return new app\model\User();
            },
            'authenticationMethod' => function (IdentityRepositoryInterface $identityRepository) {
                return new CompositeMethod([
                    new SessionMethod($identityRepository, ['tokenType' => 'session']),
                    new HttpHeaderMethod($identityRepository, ['tokenType' => 'token']),
                ]);
            },
            'authenticationFailureHandler' => function () {
                return new WebmanTech\Auth\Authentication\FailureHandler\ThrowExceptionHandler();
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
                return new WebmanTech\Auth\Authentication\FailureHandler\ThrowExceptionHandler();
            },
        ],
        'api_user' => [
            'identityRepository' => function () {
                return new app\model\User();
            },
            'authenticationMethod' => function (IdentityRepositoryInterface $identityRepository) {
                return new HttpHeaderMethod($identityRepository, ['tokenType' => 'api_token']);
            },
            'authenticationFailureHandler' => function () {
                return new WebmanTech\Auth\Authentication\FailureHandler\ThrowExceptionHandler();
            },
        ],
    ],
];
