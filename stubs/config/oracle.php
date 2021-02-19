<?php

return [
    'oraclecdmpvt' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS_3', ''),
        'host'           => env('DB_HOST_3', ''),
        'port'           => env('DB_PORT_3', '1521'),
        'database'       => env('DB_DATABASE_3', ''),
        'username'       => env('DB_USERNAME_3', ''),
        'password'       => env('DB_PASSWORD_3', ''),
        'charset'        => env('DB_CHARSET_3', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX_3', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX_3', ''),
        'server_version' => env('DB_SERVER_VERSION_3', '11g'),
    ],
    'oraclecdm' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS_4', ''),
        'host'           => env('DB_HOST_4', ''),
        'port'           => env('DB_PORT_4', '1521'),
        'database'       => env('DB_DATABASE_4', ''),
        'username'       => env('DB_USERNAME_4', ''),
        'password'       => env('DB_PASSWORD_4', ''),
        'charset'        => env('DB_CHARSET_4', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX_4', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX_4', ''),
        'server_version' => env('DB_SERVER_VERSION_4', '11g'),
    ],
];
