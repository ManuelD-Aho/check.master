<?php

declare(strict_types=1);

/**
 * Configuration des Notifications CheckMaster
 * 
 * 71 templates email, canaux et priorités
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Canaux de notification
    |--------------------------------------------------------------------------
    */
    'channels' => [
        'email' => [
            'enabled' => true,
            'driver' => 'smtp',
            'priority' => 1,
        ],
        'sms' => [
            'enabled' => false,
            'driver' => 'twilio',
            'priority' => 2,
            'only_critical' => true,
        ],
        'database' => [
            'enabled' => true,
            'driver' => 'database',
            'priority' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Priorités
    |--------------------------------------------------------------------------
    */
    'priorities' => [
        'low' => [
            'code' => 'low',
            'libelle' => 'Basse',
            'delay_minutes' => 60,
            'channels' => ['database'],
        ],
        'normal' => [
            'code' => 'normal',
            'libelle' => 'Normale',
            'delay_minutes' => 0,
            'channels' => ['database', 'email'],
        ],
        'high' => [
            'code' => 'high',
            'libelle' => 'Haute',
            'delay_minutes' => 0,
            'channels' => ['database', 'email'],
        ],
        'urgent' => [
            'code' => 'urgent',
            'libelle' => 'Urgente',
            'delay_minutes' => 0,
            'channels' => ['database', 'email', 'sms'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Catégories de templates
    |--------------------------------------------------------------------------
    */
    'categories' => [
        'candidature' => 'Candidature',
        'workflow' => 'Workflow',
        'commission' => 'Commission',
        'soutenance' => 'Soutenance',
        'paiement' => 'Paiement',
        'rappel' => 'Rappel',
        'alerte' => 'Alerte',
        'systeme' => 'Système',
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates de notification (71 templates)
    |--------------------------------------------------------------------------
    | Note: Les templates complets sont stockés en base de données
    | Ceci est la configuration de référence
    */
    'templates' => [
        // === CANDIDATURE (10) ===
        'CANDIDATURE_SOUMISE' => [
            'code' => 'CANDIDATURE_SOUMISE',
            'categorie' => 'candidature',
            'sujet' => 'Candidature soumise avec succès',
            'priorite' => 'normal',
            'destinataires' => ['etudiant', 'scolarite'],
            'variables' => ['nom_etudiant', 'theme', 'date_soumission'],
        ],
        'CANDIDATURE_VALIDEE_SCOLARITE' => [
            'code' => 'CANDIDATURE_VALIDEE_SCOLARITE',
            'categorie' => 'candidature',
            'sujet' => 'Candidature validée par la scolarité',
            'priorite' => 'normal',
            'destinataires' => ['etudiant', 'communication'],
            'variables' => ['nom_etudiant', 'theme'],
        ],
        'CANDIDATURE_VALIDEE_COMMUNICATION' => [
            'code' => 'CANDIDATURE_VALIDEE_COMMUNICATION',
            'categorie' => 'candidature',
            'sujet' => 'Format validé - Transmission à la commission',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'theme'],
        ],
        'CANDIDATURE_REJETEE' => [
            'code' => 'CANDIDATURE_REJETEE',
            'categorie' => 'candidature',
            'sujet' => 'Candidature rejetée',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'motif_rejet', 'theme'],
        ],
        'CORRECTIONS_DEMANDEES' => [
            'code' => 'CORRECTIONS_DEMANDEES',
            'categorie' => 'candidature',
            'sujet' => 'Corrections demandées sur votre candidature',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'corrections', 'delai'],
        ],

        // === WORKFLOW (15) ===
        'SCOLARITE_VALIDEE' => [
            'code' => 'SCOLARITE_VALIDEE',
            'categorie' => 'workflow',
            'sujet' => 'Validation scolarité effectuée',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant'],
        ],
        'FORMAT_VALIDE' => [
            'code' => 'FORMAT_VALIDE',
            'categorie' => 'workflow',
            'sujet' => 'Format du rapport validé',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant'],
        ],
        'AFFECTE_COMMISSION' => [
            'code' => 'AFFECTE_COMMISSION',
            'categorie' => 'workflow',
            'sujet' => 'Rapport affecté à une session de commission',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'date_commission'],
        ],
        'RAPPORT_VALIDE' => [
            'code' => 'RAPPORT_VALIDE',
            'categorie' => 'workflow',
            'sujet' => 'Rapport validé par la commission',
            'priorite' => 'high',
            'destinataires' => ['etudiant', 'encadreurs'],
            'variables' => ['nom_etudiant', 'theme', 'mention'],
        ],
        'RAPPORT_REJETE' => [
            'code' => 'RAPPORT_REJETE',
            'categorie' => 'workflow',
            'sujet' => 'Rapport rejeté par la commission',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'motif_rejet'],
        ],
        'ENCADREURS_ASSIGNES' => [
            'code' => 'ENCADREURS_ASSIGNES',
            'categorie' => 'workflow',
            'sujet' => 'Encadreurs assignés à votre mémoire',
            'priorite' => 'high',
            'destinataires' => ['etudiant', 'directeur', 'encadreur'],
            'variables' => ['nom_etudiant', 'theme', 'directeur_nom', 'encadreur_nom'],
        ],
        'AVIS_FAVORABLE' => [
            'code' => 'AVIS_FAVORABLE',
            'categorie' => 'workflow',
            'sujet' => 'Avis favorable pour la soutenance',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'encadreur_nom'],
        ],
        'JURY_CONSTITUE' => [
            'code' => 'JURY_CONSTITUE',
            'categorie' => 'workflow',
            'sujet' => 'Jury de soutenance constitué',
            'priorite' => 'high',
            'destinataires' => ['etudiant', 'jury_membres', 'maitre_stage'],
            'variables' => ['nom_etudiant', 'date_soutenance', 'heure', 'salle', 'jury'],
        ],

        // === COMMISSION (12) ===
        'SESSION_COMMISSION_PLANIFIEE' => [
            'code' => 'SESSION_COMMISSION_PLANIFIEE',
            'categorie' => 'commission',
            'sujet' => 'Session de commission planifiée',
            'priorite' => 'high',
            'destinataires' => ['membres_commission'],
            'variables' => ['date_session', 'heure', 'lieu', 'nombre_rapports'],
        ],
        'VOTE_TOUR_1' => [
            'code' => 'VOTE_TOUR_1',
            'categorie' => 'commission',
            'sujet' => 'Tour 1 de vote ouvert',
            'priorite' => 'high',
            'destinataires' => ['membres_commission'],
            'variables' => ['date_limite', 'rapports'],
        ],
        'VOTE_TOUR_2' => [
            'code' => 'VOTE_TOUR_2',
            'categorie' => 'commission',
            'sujet' => 'Tour 2 de vote ouvert',
            'priorite' => 'high',
            'destinataires' => ['membres_commission'],
            'variables' => ['date_limite', 'rapports', 'votes_precedents'],
        ],
        'VOTE_TOUR_3' => [
            'code' => 'VOTE_TOUR_3',
            'categorie' => 'commission',
            'sujet' => 'Tour 3 (FINAL) de vote ouvert',
            'priorite' => 'urgent',
            'destinataires' => ['membres_commission'],
            'variables' => ['date_limite', 'rapports', 'votes_precedents'],
        ],
        'ESCALADE_DOYEN' => [
            'code' => 'ESCALADE_DOYEN',
            'categorie' => 'commission',
            'sujet' => 'Escalade au Doyen - Médiation requise',
            'priorite' => 'urgent',
            'destinataires' => ['doyen', 'membres_commission'],
            'variables' => ['rapports_concernes', 'historique_votes', 'delai_decision'],
        ],
        'DECISION_ARBITRALE' => [
            'code' => 'DECISION_ARBITRALE',
            'categorie' => 'commission',
            'sujet' => 'Décision arbitrale du Doyen',
            'priorite' => 'high',
            'destinataires' => ['membres_commission', 'etudiant'],
            'variables' => ['decision', 'justification'],
        ],

        // === SOUTENANCE (15) ===
        'SOUTENANCE_PLANIFIEE' => [
            'code' => 'SOUTENANCE_PLANIFIEE',
            'categorie' => 'soutenance',
            'sujet' => 'Soutenance planifiée',
            'priorite' => 'high',
            'destinataires' => ['etudiant', 'jury_membres'],
            'variables' => ['nom_etudiant', 'theme', 'date', 'heure', 'salle'],
        ],
        'CONVOCATION_JURY' => [
            'code' => 'CONVOCATION_JURY',
            'categorie' => 'soutenance',
            'sujet' => 'Convocation comme membre du jury',
            'priorite' => 'high',
            'destinataires' => ['jury_membre'],
            'variables' => ['nom_membre', 'role', 'etudiant', 'theme', 'date', 'heure', 'salle'],
        ],
        'OTP_PRESIDENT_JURY' => [
            'code' => 'OTP_PRESIDENT_JURY',
            'categorie' => 'soutenance',
            'sujet' => 'Code OTP pour validation des notes',
            'priorite' => 'urgent',
            'destinataires' => ['president_jury'],
            'variables' => ['code_otp', 'validite', 'etudiant'],
        ],
        'SOUTENANCE_DEMARREE' => [
            'code' => 'SOUTENANCE_DEMARREE',
            'categorie' => 'soutenance',
            'sujet' => 'Soutenance démarrée',
            'priorite' => 'normal',
            'destinataires' => ['scolarite'],
            'variables' => ['etudiant', 'heure_debut'],
        ],
        'SOUTENANCE_TERMINEE' => [
            'code' => 'SOUTENANCE_TERMINEE',
            'categorie' => 'soutenance',
            'sujet' => 'Résultats de votre soutenance',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'note', 'mention', 'felicitations'],
        ],
        'DIPLOME_DELIVRE' => [
            'code' => 'DIPLOME_DELIVRE',
            'categorie' => 'soutenance',
            'sujet' => 'Votre diplôme est disponible',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'lieu_retrait', 'horaires'],
        ],

        // === PAIEMENT (8) ===
        'PAIEMENT_ENREGISTRE' => [
            'code' => 'PAIEMENT_ENREGISTRE',
            'categorie' => 'paiement',
            'sujet' => 'Paiement enregistré',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'montant', 'reference', 'date'],
        ],
        'PAIEMENT_VALIDE' => [
            'code' => 'PAIEMENT_VALIDE',
            'categorie' => 'paiement',
            'sujet' => 'Paiement validé - Reçu disponible',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'montant', 'numero_recu', 'lien_telechargement'],
        ],
        'EXONERATION_ACCORDEE' => [
            'code' => 'EXONERATION_ACCORDEE',
            'categorie' => 'paiement',
            'sujet' => 'Exonération accordée',
            'priorite' => 'normal',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'type_exoneration', 'montant_exonere'],
        ],
        'PENALITE_APPLIQUEE' => [
            'code' => 'PENALITE_APPLIQUEE',
            'categorie' => 'paiement',
            'sujet' => 'Pénalité de retard appliquée',
            'priorite' => 'high',
            'destinataires' => ['etudiant'],
            'variables' => ['nom_etudiant', 'montant_penalite', 'motif', 'date_limite'],
        ],

        // === RAPPEL (6) ===
        'RAPPEL_SLA_50' => [
            'code' => 'RAPPEL_SLA_50',
            'categorie' => 'rappel',
            'sujet' => 'Rappel : 50% du délai écoulé',
            'priorite' => 'normal',
            'destinataires' => ['responsable'],
            'variables' => ['etape', 'dossier', 'delai_restant'],
        ],
        'RAPPEL_SLA_80' => [
            'code' => 'RAPPEL_SLA_80',
            'categorie' => 'rappel',
            'sujet' => 'ALERTE : 80% du délai écoulé',
            'priorite' => 'high',
            'destinataires' => ['responsable', 'superviseur'],
            'variables' => ['etape', 'dossier', 'delai_restant'],
        ],
        'RAPPEL_SLA_100' => [
            'code' => 'RAPPEL_SLA_100',
            'categorie' => 'rappel',
            'sujet' => 'URGENT : Délai dépassé - Escalade automatique',
            'priorite' => 'urgent',
            'destinataires' => ['responsable', 'superviseur', 'direction'],
            'variables' => ['etape', 'dossier', 'jours_depassement'],
        ],

        // === ALERTE (3) ===
        'ALERTE_SECURITE' => [
            'code' => 'ALERTE_SECURITE',
            'categorie' => 'alerte',
            'sujet' => 'Alerte de sécurité',
            'priorite' => 'urgent',
            'destinataires' => ['admin'],
            'variables' => ['type_alerte', 'details', 'ip', 'utilisateur'],
        ],
        'ALERTE_SYSTEME' => [
            'code' => 'ALERTE_SYSTEME',
            'categorie' => 'alerte',
            'sujet' => 'Alerte système',
            'priorite' => 'urgent',
            'destinataires' => ['admin'],
            'variables' => ['type_alerte', 'details'],
        ],

        // === SYSTEME (2) ===
        'BIENVENUE' => [
            'code' => 'BIENVENUE',
            'categorie' => 'systeme',
            'sujet' => 'Bienvenue sur CheckMaster',
            'priorite' => 'normal',
            'destinataires' => ['utilisateur'],
            'variables' => ['nom', 'login', 'mot_de_passe_temporaire', 'lien_connexion'],
        ],
        'RESET_PASSWORD' => [
            'code' => 'RESET_PASSWORD',
            'categorie' => 'systeme',
            'sujet' => 'Réinitialisation de mot de passe',
            'priorite' => 'high',
            'destinataires' => ['utilisateur'],
            'variables' => ['nom', 'lien_reset', 'validite'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration envoi
    |--------------------------------------------------------------------------
    */
    'sending' => [
        'batch_size' => 50,
        'delay_between_batches_ms' => 1000,
        'retry_attempts' => 3,
        // Délai de retry par priorité (en minutes)
        'retry_delay_by_priority' => [
            'urgent' => 1,
            'high' => 3,
            'normal' => 5,
            'low' => 15,
        ],
        'retry_delay_minutes' => 5, // Valeur par défaut si priorité non définie
    ],

    /*
    |--------------------------------------------------------------------------
    | Répertoire des templates email
    |--------------------------------------------------------------------------
    */
    'templates_path' => 'ressources/templates/emails/',

    /*
    |--------------------------------------------------------------------------
    | Historique
    |--------------------------------------------------------------------------
    */
    'history' => [
        'enabled' => true,
        'retention_days' => 365,
    ],
];
