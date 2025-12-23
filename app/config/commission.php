<?php

declare(strict_types=1);

/**
 * Configuration Commission CheckMaster
 * 
 * Configuration des votes, tours et escalade
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des sessions
    |--------------------------------------------------------------------------
    */
    'session' => [
        'duree_defaut_heures' => 3,
        'quorum_minimum' => 3,
        'membres_maximum' => 10,
        'rapports_max_par_session' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des votes
    |--------------------------------------------------------------------------
    */
    'vote' => [
        'tours_maximum' => 3,
        'delai_par_tour_heures' => 48,
        'decisions_possibles' => [
            'valider' => [
                'code' => 'valider',
                'libelle' => 'Valider',
                'description' => 'Le rapport est validé et peut passer à l\'étape suivante',
                'couleur' => '#28a745',
            ],
            'a_revoir' => [
                'code' => 'a_revoir',
                'libelle' => 'À revoir',
                'description' => 'Le rapport nécessite des corrections mineures',
                'couleur' => '#ffc107',
            ],
            'rejeter' => [
                'code' => 'rejeter',
                'libelle' => 'Rejeter',
                'description' => 'Le rapport est rejeté et retourne à l\'étudiant',
                'couleur' => '#dc3545',
            ],
        ],
        'unanimite_requise' => false,
        'majorite_simple' => true,
        'vote_secret' => true,
        'commentaire_obligatoire_rejet' => true,
        'commentaire_min_length' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Règles de majorité
    |--------------------------------------------------------------------------
    */
    'majorite' => [
        'type' => 'simple', // simple, absolue, qualifiee
        'seuil_simple' => 50,
        'seuil_absolue' => 50,
        'seuil_qualifiee' => 66.67,
        'voix_preponderante_president' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Escalade au Doyen
    |--------------------------------------------------------------------------
    */
    'escalade' => [
        'enabled' => true,
        'declenchement' => [
            'apres_tours' => 3,
            'divergence_min' => true, // Au moins 1 vote différent après 3 tours
        ],
        'delai_decision_doyen_heures' => 72,
        'notification_automatique' => true,
        'justification_obligatoire' => true,
        'justification_min_length' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestion des conflits d'intérêt
    |--------------------------------------------------------------------------
    */
    'conflit_interet' => [
        'detection_automatique' => true,
        'regles' => [
            'encadreur_ne_peut_voter' => true,
            'meme_departement_autorise' => true,
            'lien_familial_interdit' => true,
        ],
        'declaration_obligatoire' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Procès-verbal
    |--------------------------------------------------------------------------
    */
    'pv' => [
        'generation_automatique' => true,
        'signature_president_obligatoire' => true,
        'signature_membres_optionnelle' => true,
        'archivage_automatique' => true,
        'delai_signature_jours' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rappels et notifications
    |--------------------------------------------------------------------------
    */
    'rappels' => [
        'avant_session_heures' => [48, 24, 2],
        'avant_fin_vote_heures' => [12, 2],
        'rappel_signature_pv_jours' => [3, 1],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mentions attribuables
    |--------------------------------------------------------------------------
    */
    'mentions' => [
        'passable' => [
            'code' => 'passable',
            'libelle' => 'Passable',
            'note_min' => 10,
            'note_max' => 11.99,
        ],
        'assez_bien' => [
            'code' => 'assez_bien',
            'libelle' => 'Assez Bien',
            'note_min' => 12,
            'note_max' => 13.99,
        ],
        'bien' => [
            'code' => 'bien',
            'libelle' => 'Bien',
            'note_min' => 14,
            'note_max' => 15.99,
        ],
        'tres_bien' => [
            'code' => 'tres_bien',
            'libelle' => 'Très Bien',
            'note_min' => 16,
            'note_max' => 17.99,
        ],
        'excellent' => [
            'code' => 'excellent',
            'libelle' => 'Excellent',
            'note_min' => 18,
            'note_max' => 20,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Délégation de vote
    |--------------------------------------------------------------------------
    */
    'delegation' => [
        'autorisee' => false,
        'max_delegations_par_membre' => 1,
        'notification_delegataire' => true,
    ],
];
