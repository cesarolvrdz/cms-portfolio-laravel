<?php

return [
    'default' => 'array',

    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => '/tmp/storage/framework/cache/data',
        ],
    ],

    'prefix' => env('CACHE_PREFIX', 'cms_cache'),
];
