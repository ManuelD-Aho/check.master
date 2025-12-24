<?php

declare(strict_types=1);

/**
 * Configuration des Permissions CheckMaster
 * 
 * Matrice des permissions par groupe utilisateur
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Groupes d'utilisateurs (13 groupes)
    |--------------------------------------------------------------------------
    */
    'groups' => [
        5 => ['code' => 'admin', 'libelle' => 'Administrateur'],
        6 => ['code' => 'secretaire', 'libelle' => 'Secrétaire'],
        7 => ['code' => 'communication', 'libelle' => 'Communication'],
        8 => ['code' => 'scolarite', 'libelle' => 'Scolarité'],
        9 => ['code' => 'resp_filiere', 'libelle' => 'Responsable Filière'],
        10 => ['code' => 'resp_niveau', 'libelle' => 'Responsable Niveau'],
        11 => ['code' => 'commission', 'libelle' => 'Commission'],
        12 => ['code' => 'enseignant', 'libelle' => 'Enseignant'],
        13 => ['code' => 'etudiant', 'libelle' => 'Étudiant'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ressources
    |--------------------------------------------------------------------------
    */
    'resources' => [
        'utilisateur' => 'Utilisateurs',
        'etudiant' => 'Étudiants',
        'enseignant' => 'Enseignants',
        'dossier' => 'Dossiers',
        'candidature' => 'Candidatures',
        'rapport' => 'Rapports',
        'commission' => 'Commission',
        'soutenance' => 'Soutenances',
        'jury' => 'Jurys',
        'paiement' => 'Paiements',
        'document' => 'Documents',
        'notification' => 'Notifications',
        'configuration' => 'Configuration',
        'audit' => 'Audit',
        'archive' => 'Archives',
        'entreprise' => 'Entreprises',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */
    'actions' => [
        'voir' => 'Consulter',
        'creer' => 'Créer',
        'modifier' => 'Modifier',
        'supprimer' => 'Supprimer',
        'valider' => 'Valider',
        'rejeter' => 'Rejeter',
        'exporter' => 'Exporter',
        'importer' => 'Importer',
        'archiver' => 'Archiver',
        'signer' => 'Signer',
        'voter' => 'Voter',
    ],

    /*
    |--------------------------------------------------------------------------
    | Matrice des permissions
    |--------------------------------------------------------------------------
    | Format: groupe_id => [ressource => [actions]]
    */
    'matrix' => [
        // Admin - Toutes les permissions
        5 => [
            'utilisateur' => ['voir', 'creer', 'modifier', 'supprimer'],
            'etudiant' => ['voir', 'creer', 'modifier', 'supprimer', 'exporter', 'importer'],
            'enseignant' => ['voir', 'creer', 'modifier', 'supprimer', 'exporter', 'importer'],
            'dossier' => ['voir', 'creer', 'modifier', 'supprimer', 'valider', 'rejeter', 'exporter'],
            'candidature' => ['voir', 'modifier', 'valider', 'rejeter'],
            'rapport' => ['voir', 'modifier', 'valider', 'rejeter'],
            'commission' => ['voir', 'creer', 'modifier', 'supprimer', 'voter'],
            'soutenance' => ['voir', 'creer', 'modifier', 'supprimer'],
            'jury' => ['voir', 'creer', 'modifier'],
            'paiement' => ['voir', 'creer', 'modifier', 'supprimer', 'valider', 'exporter'],
            'document' => ['voir', 'creer', 'modifier', 'supprimer', 'archiver', 'signer'],
            'notification' => ['voir', 'creer', 'modifier', 'supprimer'],
            'configuration' => ['voir', 'modifier'],
            'audit' => ['voir', 'exporter'],
            'archive' => ['voir', 'creer', 'exporter'],
            'entreprise' => ['voir', 'creer', 'modifier', 'supprimer'],
        ],

        // Secrétaire
        6 => [
            'utilisateur' => ['voir'],
            'etudiant' => ['voir'],
            'enseignant' => ['voir'],
            'dossier' => ['voir', 'exporter'],
            'candidature' => ['voir'],
            'rapport' => ['voir'],
            'commission' => ['voir'],
            'soutenance' => ['voir'],
            'jury' => ['voir'],
            'paiement' => ['voir', 'exporter'],
            'document' => ['voir', 'creer', 'archiver'],
            'notification' => ['voir', 'creer'],
            'configuration' => [],
            'audit' => ['voir'],
            'archive' => ['voir', 'creer'],
            'entreprise' => ['voir'],
        ],

        // Communication
        7 => [
            'utilisateur' => [],
            'etudiant' => ['voir'],
            'enseignant' => [],
            'dossier' => ['voir'],
            'candidature' => ['voir', 'valider', 'rejeter'],
            'rapport' => ['voir', 'valider', 'rejeter'],
            'commission' => [],
            'soutenance' => [],
            'jury' => [],
            'paiement' => [],
            'document' => ['voir'],
            'notification' => ['voir'],
            'configuration' => [],
            'audit' => [],
            'archive' => [],
            'entreprise' => [],
        ],

        // Scolarité
        8 => [
            'utilisateur' => ['voir'],
            'etudiant' => ['voir', 'creer', 'modifier', 'exporter', 'importer'],
            'enseignant' => ['voir'],
            'dossier' => ['voir', 'modifier', 'valider'],
            'candidature' => ['voir', 'valider', 'rejeter'],
            'rapport' => ['voir'],
            'commission' => ['voir'],
            'soutenance' => ['voir'],
            'jury' => ['voir'],
            'paiement' => ['voir', 'creer', 'modifier', 'valider', 'exporter'],
            'document' => ['voir', 'creer'],
            'notification' => ['voir', 'creer'],
            'configuration' => [],
            'audit' => ['voir'],
            'archive' => ['voir'],
            'entreprise' => ['voir', 'creer', 'modifier'],
        ],

        // Responsable Filière
        9 => [
            'utilisateur' => ['voir'],
            'etudiant' => ['voir'],
            'enseignant' => ['voir', 'creer', 'modifier'],
            'dossier' => ['voir', 'modifier'],
            'candidature' => ['voir'],
            'rapport' => ['voir', 'valider', 'rejeter'],
            'commission' => ['voir', 'creer', 'modifier'],
            'soutenance' => ['voir', 'creer', 'modifier'],
            'jury' => ['voir', 'creer', 'modifier'],
            'paiement' => ['voir'],
            'document' => ['voir', 'creer', 'signer'],
            'notification' => ['voir', 'creer'],
            'configuration' => ['voir'],
            'audit' => ['voir'],
            'archive' => ['voir'],
            'entreprise' => ['voir'],
        ],

        // Responsable Niveau
        10 => [
            'utilisateur' => ['voir'],
            'etudiant' => ['voir'],
            'enseignant' => ['voir'],
            'dossier' => ['voir'],
            'candidature' => ['voir'],
            'rapport' => ['voir'],
            'commission' => ['voir'],
            'soutenance' => ['voir'],
            'jury' => ['voir'],
            'paiement' => [],
            'document' => ['voir'],
            'notification' => ['voir'],
            'configuration' => [],
            'audit' => [],
            'archive' => [],
            'entreprise' => [],
        ],

        // Commission
        11 => [
            'utilisateur' => [],
            'etudiant' => ['voir'],
            'enseignant' => ['voir'],
            'dossier' => ['voir'],
            'candidature' => ['voir'],
            'rapport' => ['voir', 'valider', 'rejeter'],
            'commission' => ['voir', 'voter'],
            'soutenance' => ['voir'],
            'jury' => ['voir', 'creer'],
            'paiement' => [],
            'document' => ['voir'],
            'notification' => ['voir'],
            'configuration' => [],
            'audit' => [],
            'archive' => [],
            'entreprise' => [],
        ],

        // Enseignant
        12 => [
            'utilisateur' => [],
            'etudiant' => ['voir'],
            'enseignant' => ['voir'],
            'dossier' => ['voir', 'modifier'],
            'candidature' => [],
            'rapport' => ['voir', 'modifier', 'valider'],
            'commission' => [],
            'soutenance' => ['voir', 'modifier'],
            'jury' => ['voir'],
            'paiement' => [],
            'document' => ['voir', 'creer'],
            'notification' => ['voir'],
            'configuration' => [],
            'audit' => [],
            'archive' => [],
            'entreprise' => [],
        ],

        // Étudiant
        13 => [
            'utilisateur' => [],
            'etudiant' => [],
            'enseignant' => [],
            'dossier' => ['voir'],
            'candidature' => ['voir', 'creer', 'modifier'],
            'rapport' => ['voir', 'creer', 'modifier'],
            'commission' => [],
            'soutenance' => ['voir'],
            'jury' => ['voir'],
            'paiement' => ['voir'],
            'document' => ['voir', 'creer'],
            'notification' => ['voir'],
            'configuration' => [],
            'audit' => [],
            'archive' => [],
            'entreprise' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Admin (bypass permissions)
    |--------------------------------------------------------------------------
    */
    'super_admin' => [
        'enabled' => true,
        'group_id' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache des permissions
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 heure
        'prefix' => 'permissions_',
    ],
];
