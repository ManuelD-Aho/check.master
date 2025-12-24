<?php

declare(strict_types=1);

/**
 * Configuration SLA CheckMaster
 * 
 * Délais SLA par étape du workflow
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Activation du suivi SLA
    |--------------------------------------------------------------------------
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Délais par étape du workflow (en jours ouvrés)
    |--------------------------------------------------------------------------
    */
    'delais' => [
        'candidature_soumise' => [
            'etape' => 'candidature_soumise',
            'libelle' => 'Traitement candidature soumise',
            'delai_jours' => 5,
            'responsable' => 'scolarite',
            'groupe_responsable' => 8,
        ],
        'verification_scolarite' => [
            'etape' => 'verification_scolarite',
            'libelle' => 'Vérification par la scolarité',
            'delai_jours' => 3,
            'responsable' => 'scolarite',
            'groupe_responsable' => 8,
        ],
        'filtre_communication' => [
            'etape' => 'filtre_communication',
            'libelle' => 'Vérification format par communication',
            'delai_jours' => 3,
            'responsable' => 'communication',
            'groupe_responsable' => 7,
        ],
        'en_attente_commission' => [
            'etape' => 'en_attente_commission',
            'libelle' => 'Attente session commission',
            'delai_jours' => 14,
            'responsable' => 'resp_filiere',
            'groupe_responsable' => 9,
        ],
        'en_evaluation_commission' => [
            'etape' => 'en_evaluation_commission',
            'libelle' => 'Évaluation par la commission',
            'delai_jours' => 7,
            'responsable' => 'commission',
            'groupe_responsable' => 11,
        ],
        'rapport_valide' => [
            'etape' => 'rapport_valide',
            'libelle' => 'Assignation des encadreurs',
            'delai_jours' => 5,
            'responsable' => 'resp_filiere',
            'groupe_responsable' => 9,
        ],
        'attente_avis_encadreur' => [
            'etape' => 'attente_avis_encadreur',
            'libelle' => 'Attente avis encadreur',
            'delai_jours' => 30,
            'responsable' => 'enseignant',
            'groupe_responsable' => 12,
        ],
        'pret_pour_jury' => [
            'etape' => 'pret_pour_jury',
            'libelle' => 'Constitution du jury',
            'delai_jours' => 14,
            'responsable' => 'resp_filiere',
            'groupe_responsable' => 9,
        ],
        'jury_en_constitution' => [
            'etape' => 'jury_en_constitution',
            'libelle' => 'Finalisation du jury',
            'delai_jours' => 7,
            'responsable' => 'resp_filiere',
            'groupe_responsable' => 9,
        ],
        'soutenance_planifiee' => [
            'etape' => 'soutenance_planifiee',
            'libelle' => 'Attente date soutenance',
            'delai_jours' => 30,
            'responsable' => 'scolarite',
            'groupe_responsable' => 8,
        ],
        'soutenance_terminee' => [
            'etape' => 'soutenance_terminee',
            'libelle' => 'Délivrance diplôme',
            'delai_jours' => 30,
            'responsable' => 'secretaire',
            'groupe_responsable' => 6,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Seuils d'alerte (pourcentage du délai écoulé)
    |--------------------------------------------------------------------------
    */
    'seuils' => [
        'rappel' => [
            'pourcentage' => 50,
            'notification' => 'RAPPEL_SLA_50',
            'destinataires' => ['responsable'],
        ],
        'alerte' => [
            'pourcentage' => 80,
            'notification' => 'RAPPEL_SLA_80',
            'destinataires' => ['responsable', 'superviseur'],
        ],
        'escalade' => [
            'pourcentage' => 100,
            'notification' => 'RAPPEL_SLA_100',
            'destinataires' => ['responsable', 'superviseur', 'direction'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Escalade automatique
    |--------------------------------------------------------------------------
    */
    'escalade' => [
        'enabled' => true,
        'niveaux' => [
            1 => [
                'apres_jours_depassement' => 0,
                'niveau' => 'resp_niveau',
                'groupe' => 10,
            ],
            2 => [
                'apres_jours_depassement' => 3,
                'niveau' => 'resp_filiere',
                'groupe' => 9,
            ],
            3 => [
                'apres_jours_depassement' => 7,
                'niveau' => 'direction',
                'groupe' => 5,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Jours ouvrés
    |--------------------------------------------------------------------------
    */
    'jours_ouvres' => [
        'lundi' => true,
        'mardi' => true,
        'mercredi' => true,
        'jeudi' => true,
        'vendredi' => true,
        'samedi' => false,
        'dimanche' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Jours fériés (format MM-DD)
    |--------------------------------------------------------------------------
    */
    'jours_feries' => [
        '01-01', // Jour de l'An
        '05-01', // Fête du Travail
        '08-07', // Fête de l'Indépendance
        '08-15', // Assomption
        '11-01', // Toussaint
        '11-15', // Journée de la Paix
        '12-25', // Noël
        // Les fêtes mobiles (Pâques, Ascension, etc.) sont calculées dynamiquement
    ],

    /*
    |--------------------------------------------------------------------------
    | Rapports SLA
    |--------------------------------------------------------------------------
    */
    'rapports' => [
        'frequence_generation' => 'daily',
        'destinataires' => [5, 9], // Admin, Resp. Filière
        'format' => 'pdf',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pénalités (désactivé par défaut)
    |--------------------------------------------------------------------------
    */
    'penalites' => [
        'enabled' => false,
        'montant_par_jour' => 0,
        'plafond' => 0,
    ],
];
