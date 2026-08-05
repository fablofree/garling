<?php

return [
    'driver'   => getenv('DB_DRIVER')   ?: 'pgsql',
    'host'     => getenv('DB_HOST')     ?: '127.0.0.1',
    'port'     => getenv('DB_PORT')     ?: '5432',
    'database' => getenv('DB_DATABASE') ?: 'garage_lingiah',
    'username' => getenv('DB_USERNAME') ?: 'postgres',
    'password' => getenv('DB_PASSWORD') ?: 'postgres',
    'charset'  => 'utf8',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
