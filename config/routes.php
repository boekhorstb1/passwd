<?php

$mapper->connect(
    'Api',
    '/api/:action',
    [
        'controller' => 'ApiHandler',
    ]
);

$mapper->connect(
    'Home',
    '/',
    [
        'controller' => 'Base',
        'action' => 'show',
    ]
);
