<?php

return [

    'paths' => [
        'api/*',
        'storage/*',
        'login',
        'logout',
        'register',
        'forgot-password',
        'reset-password',
    ],


    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL'),
    ],


    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];