<?php

declare(strict_types=1);

/**
 * Exemple de configuration de la base de données
 * 
 * Copiez ce fichier vers database.php et modifiez les valeurs.
 * Ne jamais commiter database.php avec les vraies valeurs !
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
            'password' => 'your_password_here',
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
];
