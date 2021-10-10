<?php
use Horde\Core\Middleware\AuthHordeSession;
use Horde\Core\Middleware\RedirectToLogin;
use Horde\Passwd\Middleware\RenderReactApp;
use Horde\Core\Middleware\ReturnSessionToken;
use Horde\Core\Middleware\DemandAuthenticatedUser;
use Horde\Core\Middleware\CsrfPrevention;

$mapper->connect(
    'Api',
    '/api/:action',
    [
        'controller' => 'ApiHandler',
        'stack' => [
            AuthHordeSession::class,
            DemandAuthenticatedUser::class,
            CsrfPrevention::class,
        ],
    ]
);

$mapper->connect(
    'ReactInit',
    '/react',
    [
        'controller' => 'ReactInit',
        'stack' => [
            AuthHordeSession::class,
            RedirectToLogin::class,
        ]
    ]
);

$mapper->connect(
    'ReactInitM',
    '/reactm',
    [
        'stack' => [
            AuthHordeSession::class,
            RedirectToLogin::class,
            RenderReactApp::class,
        ]
    ]
);
