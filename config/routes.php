<?php
use Horde\Core\Middleware\AuthHordeSession;
use Horde\Core\Middleware\RedirectToLogin;
use Horde\Passwd\Middleware\ReactInit;

$mapper->connect(
    'Api',
    '/api/:action',
    [
        'controller' => 'ApiHandler',
        'stack' => [

        ],
    ]
);

$mapper->connect(
    'ReactInit',
    '/react',
    [
        'controller' => 'ChangePasswordReact',
        'stack' => [
            AuthHordeSession::class,
            RedirectToLogin::class,
        ]
    ]
);
