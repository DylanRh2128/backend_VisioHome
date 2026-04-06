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
        env('FRONTEND_URL', 'http://localhost:5173'),
        env('NGROK_URL', 'https://brosy-urochordal-zoie.ngrok-free.dev')
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];