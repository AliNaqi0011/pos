<?php

return [
    'rate_limits' => [
        'login' => env('RATE_LIMIT_LOGIN_ATTEMPTS', 5),
        'api' => env('RATE_LIMIT_PER_MINUTE', 60),
        'pos' => 120, // Higher limit for POS operations
    ],

    'session' => [
        'secure_cookie' => env('SESSION_SECURE_COOKIE', false),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'strict'),
    ],

    'headers' => [
        'hsts_max_age' => 31536000,
        'content_type_options' => 'nosniff',
        'frame_options' => 'DENY',
        'xss_protection' => '1; mode=block',
    ],

    'encryption' => [
        'sensitive_fields' => [
            'phone_number',
            'address',
            'notes',
        ],
    ],
];