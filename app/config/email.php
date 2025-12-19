<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Driver par défaut
    |--------------------------------------------------------------------------
    | 'smtp', 'sendmail', 'log', 'array'
    */
    'default' => 'smtp',

    /*
    |--------------------------------------------------------------------------
    | Configuration SMTP
    |--------------------------------------------------------------------------
    */
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => 'smtp.mailtrap.io',
            'port' => 2525,
            'encryption' => 'tls',
            'username' => null,
            'password' => null,
            'timeout' => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel' => 'mail',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Adresse d'expédition globale
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => 'no-reply@checkmaster.ci',
        'name' => 'CheckMaster UFHB',
    ],
];
