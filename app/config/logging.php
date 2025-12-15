<?php

declare(strict_types=1);

/**
 * Configuration du logging
 * 
 * Paramètres de journalisation pour CheckMaster.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Canal par défaut
    |--------------------------------------------------------------------------
    */
    'default' => env('LOG_CHANNEL', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Niveau de log par défaut
    |--------------------------------------------------------------------------
    | Options: debug, info, notice, warning, error, critical, alert, emergency
    */
    'level' => env('LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Répertoire des logs
    |--------------------------------------------------------------------------
    */
    'path' => 'storage/logs',

    /*
    |--------------------------------------------------------------------------
    | Rétention des logs (jours)
    |--------------------------------------------------------------------------
    */
    'retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Canaux disponibles
    |--------------------------------------------------------------------------
    */
    'channels' => [
        'app' => [
            'driver' => 'daily',
            'path' => 'storage/logs/app.log',
            'level' => 'debug',
            'days' => 14,
        ],

        'error' => [
            'driver' => 'daily',
            'path' => 'storage/logs/error.log',
            'level' => 'error',
            'days' => 30,
        ],

        'auth' => [
            'driver' => 'daily',
            'path' => 'storage/logs/auth.log',
            'level' => 'info',
            'days' => 90,
        ],

        'audit' => [
            'driver' => 'daily',
            'path' => 'storage/logs/audit.log',
            'level' => 'info',
            'days' => 365, // 1 an de rétention pour audit
        ],

        'sql' => [
            'driver' => 'daily',
            'path' => 'storage/logs/sql.log',
            'level' => 'debug',
            'days' => 7,
        ],

        'performance' => [
            'driver' => 'daily',
            'path' => 'storage/logs/performance.log',
            'level' => 'info',
            'days' => 14,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions à logger dans la table pister
    |--------------------------------------------------------------------------
    */
    'audit_actions' => [
        'connexion',
        'deconnexion',
        'creation',
        'modification',
        'suppression',
        'export',
        'validation',
        'transition_workflow',
    ],
];
