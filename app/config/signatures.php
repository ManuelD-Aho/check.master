<?php

declare(strict_types=1);

/**
 * Configuration Signatures Électroniques CheckMaster
 * 
 * Configuration des signatures électroniques pour les documents officiels
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Activation des signatures électroniques
    |--------------------------------------------------------------------------
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Type de signature
    |--------------------------------------------------------------------------
    */
    'type' => 'image', // image, otp, certificat

    /*
    |--------------------------------------------------------------------------
    | Configuration signature par image
    |--------------------------------------------------------------------------
    */
    'image' => [
        'path' => 'storage/signatures/',
        'format' => 'png',
        'max_width' => 300,
        'max_height' => 150,
        'max_size_kb' => 500,
        'background' => 'transparent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration OTP
    |--------------------------------------------------------------------------
    */
    'otp' => [
        'enabled' => true,
        'digits' => 6,
        'validite_minutes' => 10,
        'max_tentatives' => 3,
        'canal' => 'email', // email, sms
    ],

    /*
    |--------------------------------------------------------------------------
    | Documents nécessitant une signature
    |--------------------------------------------------------------------------
    */
    'documents_signables' => [
        'pv_commission' => [
            'signataires' => ['president_commission'],
            'otp_requis' => true,
        ],
        'pv_soutenance' => [
            'signataires' => ['president_jury', 'membres_jury'],
            'otp_requis' => true,
        ],
        'attestation_reussite' => [
            'signataires' => ['directeur_ufr', 'chef_departement'],
            'otp_requis' => true,
        ],
        'attestation_diplome' => [
            'signataires' => ['directeur_ufr', 'chef_departement', 'recteur'],
            'otp_requis' => true,
        ],
        'lettre_affectation' => [
            'signataires' => ['resp_filiere'],
            'otp_requis' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Signataires autorisés
    |--------------------------------------------------------------------------
    */
    'signataires' => [
        'directeur_ufr' => [
            'fonction' => 'Directeur UFR',
            'groupes' => [5],
        ],
        'chef_departement' => [
            'fonction' => 'Chef de Département',
            'groupes' => [5, 9],
        ],
        'resp_filiere' => [
            'fonction' => 'Responsable de Filière',
            'groupes' => [9],
        ],
        'president_commission' => [
            'fonction' => 'Président de Commission',
            'groupes' => [11],
        ],
        'president_jury' => [
            'fonction' => 'Président du Jury',
            'groupes' => [12],
        ],
        'membres_jury' => [
            'fonction' => 'Membre du Jury',
            'groupes' => [12],
        ],
        'recteur' => [
            'fonction' => 'Recteur',
            'groupes' => [5],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Délégation de signature
    |--------------------------------------------------------------------------
    */
    'delegation' => [
        'autorisee' => true,
        'duree_max_jours' => 30,
        'notification_obligatoire' => true,
        'motif_obligatoire' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Horodatage
    |--------------------------------------------------------------------------
    */
    'horodatage' => [
        'enabled' => true,
        'format' => 'Y-m-d H:i:s',
        'timezone' => 'Africa/Abidjan',
        'inclure_dans_document' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Vérification
    |--------------------------------------------------------------------------
    */
    'verification' => [
        'qr_code' => true,
        'url_verification' => '/verifier-signature/{hash}',
        'hash_algorithm' => 'sha256',
    ],

    /*
    |--------------------------------------------------------------------------
    | Archivage
    |--------------------------------------------------------------------------
    */
    'archivage' => [
        'enabled' => true,
        'path' => 'storage/signatures_archive/',
        'retention_years' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'log_tentatives' => true,
        'log_verifications' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Workflow de signature
    |--------------------------------------------------------------------------
    */
    'workflow' => [
        'sequentiel' => true, // Les signatures doivent être dans l'ordre
        'rappel_heures' => [48, 24, 6],
        'expiration_jours' => 7,
    ],
];
