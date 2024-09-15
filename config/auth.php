<?php

return [
    'authentication' => [
        'storage' => 'session',
        'adapter' => 'password',
        'users' => [
            'admin' => [
                'password' => 'password123'
            ]
        ]
    ]
];
