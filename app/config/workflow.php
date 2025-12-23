<?php

declare(strict_types=1);

/**
 * Configuration du Workflow CheckMaster
 * 
 * 14 états du workflow de supervision de mémoire
 * Voir docs/workflows.md pour la documentation complète
 */
return [
    /*
    |--------------------------------------------------------------------------
    | États du Workflow CheckMaster (14 états)
    |--------------------------------------------------------------------------
    */
    'states' => [
        'inscrit' => [
            'code' => 'inscrit',
            'libelle' => 'Inscrit',
            'description' => 'Étudiant inscrit, peut soumettre sa candidature',
            'couleur' => '#6c757d',
            'icone' => 'user-plus',
            'ordre' => 1,
            'terminal' => false,
        ],
        'candidature_soumise' => [
            'code' => 'candidature_soumise',
            'libelle' => 'Candidature soumise',
            'description' => 'Candidature en attente de vérification par la scolarité',
            'couleur' => '#17a2b8',
            'icone' => 'file-text',
            'ordre' => 2,
            'terminal' => false,
        ],
        'verification_scolarite' => [
            'code' => 'verification_scolarite',
            'libelle' => 'Vérification scolarité',
            'description' => 'Vérification du paiement et des documents par la scolarité',
            'couleur' => '#ffc107',
            'icone' => 'search',
            'ordre' => 3,
            'terminal' => false,
        ],
        'filtre_communication' => [
            'code' => 'filtre_communication',
            'libelle' => 'Filtre communication',
            'description' => 'Vérification du format par le service communication',
            'couleur' => '#fd7e14',
            'icone' => 'check-square',
            'ordre' => 4,
            'terminal' => false,
        ],
        'en_attente_commission' => [
            'code' => 'en_attente_commission',
            'libelle' => 'En attente commission',
            'description' => 'En attente d\'une session de commission',
            'couleur' => '#6610f2',
            'icone' => 'clock',
            'ordre' => 5,
            'terminal' => false,
        ],
        'en_evaluation_commission' => [
            'code' => 'en_evaluation_commission',
            'libelle' => 'En évaluation commission',
            'description' => 'En cours d\'évaluation par la commission',
            'couleur' => '#e83e8c',
            'icone' => 'users',
            'ordre' => 6,
            'terminal' => false,
        ],
        'rapport_valide' => [
            'code' => 'rapport_valide',
            'libelle' => 'Rapport validé',
            'description' => 'Rapport validé par la commission, encadreurs assignés',
            'couleur' => '#28a745',
            'icone' => 'check-circle',
            'ordre' => 7,
            'terminal' => false,
        ],
        'attente_avis_encadreur' => [
            'code' => 'attente_avis_encadreur',
            'libelle' => 'Attente avis encadreur',
            'description' => 'En attente de l\'avis favorable de l\'encadreur',
            'couleur' => '#20c997',
            'icone' => 'user-check',
            'ordre' => 8,
            'terminal' => false,
        ],
        'pret_pour_jury' => [
            'code' => 'pret_pour_jury',
            'libelle' => 'Prêt pour jury',
            'description' => 'Prêt pour la constitution du jury de soutenance',
            'couleur' => '#007bff',
            'icone' => 'award',
            'ordre' => 9,
            'terminal' => false,
        ],
        'jury_en_constitution' => [
            'code' => 'jury_en_constitution',
            'libelle' => 'Jury en constitution',
            'description' => 'Constitution du jury en cours (5 membres)',
            'couleur' => '#17a2b8',
            'icone' => 'users',
            'ordre' => 10,
            'terminal' => false,
        ],
        'soutenance_planifiee' => [
            'code' => 'soutenance_planifiee',
            'libelle' => 'Soutenance planifiée',
            'description' => 'Soutenance planifiée avec date, heure et salle',
            'couleur' => '#6f42c1',
            'icone' => 'calendar',
            'ordre' => 11,
            'terminal' => false,
        ],
        'soutenance_en_cours' => [
            'code' => 'soutenance_en_cours',
            'libelle' => 'Soutenance en cours',
            'description' => 'Soutenance en cours, saisie des notes',
            'couleur' => '#fd7e14',
            'icone' => 'play-circle',
            'ordre' => 12,
            'terminal' => false,
        ],
        'soutenance_terminee' => [
            'code' => 'soutenance_terminee',
            'libelle' => 'Soutenance terminée',
            'description' => 'Soutenance terminée, en attente de validation finale',
            'couleur' => '#28a745',
            'icone' => 'check',
            'ordre' => 13,
            'terminal' => false,
        ],
        'diplome_delivre' => [
            'code' => 'diplome_delivre',
            'libelle' => 'Diplôme délivré',
            'description' => 'Processus terminé, diplôme délivré',
            'couleur' => '#198754',
            'icone' => 'award',
            'ordre' => 14,
            'terminal' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transitions autorisées
    |--------------------------------------------------------------------------
    */
    'transitions' => [
        'soumettre_candidature' => [
            'code' => 'soumettre_candidature',
            'libelle' => 'Soumettre candidature',
            'from' => ['inscrit'],
            'to' => 'candidature_soumise',
            'roles_autorises' => [13], // Étudiant
            'notification' => 'CANDIDATURE_SOUMISE',
        ],
        'demarrer_verification' => [
            'code' => 'demarrer_verification',
            'libelle' => 'Démarrer vérification',
            'from' => ['candidature_soumise'],
            'to' => 'verification_scolarite',
            'roles_autorises' => [5, 8], // Admin, Scolarité
            'notification' => null,
        ],
        'valider_scolarite' => [
            'code' => 'valider_scolarite',
            'libelle' => 'Valider scolarité',
            'from' => ['verification_scolarite'],
            'to' => 'filtre_communication',
            'roles_autorises' => [5, 8], // Admin, Scolarité
            'notification' => 'SCOLARITE_VALIDEE',
        ],
        'valider_format' => [
            'code' => 'valider_format',
            'libelle' => 'Valider format',
            'from' => ['filtre_communication'],
            'to' => 'en_attente_commission',
            'roles_autorises' => [5, 7], // Admin, Communication
            'notification' => 'FORMAT_VALIDE',
        ],
        'affecter_commission' => [
            'code' => 'affecter_commission',
            'libelle' => 'Affecter à une commission',
            'from' => ['en_attente_commission'],
            'to' => 'en_evaluation_commission',
            'roles_autorises' => [5, 9], // Admin, Resp. Filière
            'notification' => 'AFFECTE_COMMISSION',
        ],
        'valider_commission' => [
            'code' => 'valider_commission',
            'libelle' => 'Valider par la commission',
            'from' => ['en_evaluation_commission'],
            'to' => 'rapport_valide',
            'roles_autorises' => [5, 11], // Admin, Commission
            'notification' => 'RAPPORT_VALIDE',
        ],
        'rejeter_commission' => [
            'code' => 'rejeter_commission',
            'libelle' => 'Rejeter par la commission',
            'from' => ['en_evaluation_commission'],
            'to' => 'inscrit',
            'roles_autorises' => [5, 11], // Admin, Commission
            'notification' => 'RAPPORT_REJETE',
        ],
        'assigner_encadreurs' => [
            'code' => 'assigner_encadreurs',
            'libelle' => 'Assigner les encadreurs',
            'from' => ['rapport_valide'],
            'to' => 'attente_avis_encadreur',
            'roles_autorises' => [5, 9, 11], // Admin, Resp. Filière, Commission
            'notification' => 'ENCADREURS_ASSIGNES',
        ],
        'donner_avis_favorable' => [
            'code' => 'donner_avis_favorable',
            'libelle' => 'Donner avis favorable',
            'from' => ['attente_avis_encadreur'],
            'to' => 'pret_pour_jury',
            'roles_autorises' => [5, 12], // Admin, Enseignant
            'notification' => 'AVIS_FAVORABLE',
        ],
        'constituer_jury' => [
            'code' => 'constituer_jury',
            'libelle' => 'Constituer le jury',
            'from' => ['pret_pour_jury'],
            'to' => 'jury_en_constitution',
            'roles_autorises' => [5, 9, 11], // Admin, Resp. Filière, Commission
            'notification' => null,
        ],
        'finaliser_jury' => [
            'code' => 'finaliser_jury',
            'libelle' => 'Finaliser le jury',
            'from' => ['jury_en_constitution'],
            'to' => 'soutenance_planifiee',
            'roles_autorises' => [5, 9], // Admin, Resp. Filière
            'notification' => 'JURY_CONSTITUE',
        ],
        'demarrer_soutenance' => [
            'code' => 'demarrer_soutenance',
            'libelle' => 'Démarrer la soutenance',
            'from' => ['soutenance_planifiee'],
            'to' => 'soutenance_en_cours',
            'roles_autorises' => [5, 12], // Admin, Enseignant (Président jury)
            'notification' => 'SOUTENANCE_DEMARREE',
        ],
        'terminer_soutenance' => [
            'code' => 'terminer_soutenance',
            'libelle' => 'Terminer la soutenance',
            'from' => ['soutenance_en_cours'],
            'to' => 'soutenance_terminee',
            'roles_autorises' => [5, 12], // Admin, Enseignant (Président jury)
            'notification' => 'SOUTENANCE_TERMINEE',
        ],
        'delivrer_diplome' => [
            'code' => 'delivrer_diplome',
            'libelle' => 'Délivrer le diplôme',
            'from' => ['soutenance_terminee'],
            'to' => 'diplome_delivre',
            'roles_autorises' => [5, 6], // Admin, Secrétaire
            'notification' => 'DIPLOME_DELIVRE',
        ],
        'demander_corrections' => [
            'code' => 'demander_corrections',
            'libelle' => 'Demander des corrections',
            'from' => ['verification_scolarite', 'filtre_communication'],
            'to' => 'candidature_soumise',
            'roles_autorises' => [5, 7, 8], // Admin, Communication, Scolarité
            'notification' => 'CORRECTIONS_DEMANDEES',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | État Gate (Blocage rédaction rapport)
    |--------------------------------------------------------------------------
    | L'onglet "Rédaction du rapport" est bloqué tant que le dossier
    | n'a pas atteint l'état 'rapport_valide'
    */
    'gate' => [
        'redaction_rapport' => [
            'etats_requis' => ['rapport_valide', 'attente_avis_encadreur', 'pret_pour_jury', 'jury_en_constitution', 'soutenance_planifiee', 'soutenance_en_cours', 'soutenance_terminee', 'diplome_delivre'],
            'message_erreur' => 'La rédaction du rapport n\'est pas encore disponible. Votre candidature doit d\'abord être validée par la commission.',
            'routes_bloquees' => ['/etudiant/rapport/*'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Groupes d'utilisateurs et leurs permissions workflow
    |--------------------------------------------------------------------------
    */
    'groupes' => [
        5 => ['code' => 'admin', 'libelle' => 'Administrateur', 'all_transitions' => true],
        6 => ['code' => 'secretaire', 'libelle' => 'Secrétaire', 'transitions' => ['delivrer_diplome']],
        7 => ['code' => 'communication', 'libelle' => 'Communication', 'transitions' => ['valider_format', 'demander_corrections']],
        8 => ['code' => 'scolarite', 'libelle' => 'Scolarité', 'transitions' => ['demarrer_verification', 'valider_scolarite', 'demander_corrections']],
        9 => ['code' => 'resp_filiere', 'libelle' => 'Resp. Filière', 'transitions' => ['affecter_commission', 'assigner_encadreurs', 'constituer_jury', 'finaliser_jury']],
        10 => ['code' => 'resp_niveau', 'libelle' => 'Resp. Niveau', 'transitions' => []],
        11 => ['code' => 'commission', 'libelle' => 'Commission', 'transitions' => ['valider_commission', 'rejeter_commission', 'assigner_encadreurs', 'constituer_jury']],
        12 => ['code' => 'enseignant', 'libelle' => 'Enseignant', 'transitions' => ['donner_avis_favorable', 'demarrer_soutenance', 'terminer_soutenance']],
        13 => ['code' => 'etudiant', 'libelle' => 'Étudiant', 'transitions' => ['soumettre_candidature']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Escalade workflow
    |--------------------------------------------------------------------------
    */
    'escalade' => [
        'enabled' => true,
        'niveaux' => [
            1 => 'resp_niveau',
            2 => 'resp_filiere',
            3 => 'doyen',
        ],
        'delai_escalade_jours' => 5,
    ],
];
