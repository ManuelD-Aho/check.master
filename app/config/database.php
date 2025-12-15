<?php

declare(strict_types=1);

/**
 * Configuration de la base de données
 * 
 * IMPORTANT: Ne jamais commiter ce fichier avec les valeurs réelles.
 * Utiliser des variables d'environnement en production.
 */

return [
    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'checkmaster',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'path' => dirname(__DIR__, 2) . '/database/migrations',
    ],

    'redis' => [
        'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'port' => getenv('REDIS_PORT') ?: 6379,
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'database' => getenv('REDIS_DB') ?: 0,
    ],
];
