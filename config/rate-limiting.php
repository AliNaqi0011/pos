<?php

return [
    'default' => [
        'requests' => 60,
        'per_minutes' => 1,
    ],

    'routes' => [
        'login' => [
            'requests' => 5,
            'per_minutes' => 1,
            'key' => 'ip',
        ],
        'register' => [
            'requests' => 3,
            'per_minutes' => 1,
            'key' => 'ip',
        ],
        'pos' => [
            'requests' => 200,
            'per_minutes' => 1,
            'key' => 'user',
        ],
        'api' => [
            'requests' => 100,
            'per_minutes' => 1,
            'key' => 'user',
        ],
    ],

    'bypass_roles' => [
        'super_admin',
    ],
];