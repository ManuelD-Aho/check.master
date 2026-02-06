<?php

declare(strict_types=1);

return [
    'name' => 'rapport',
    'type' => 'state_machine',
    'marking_store' => [
        'type' => 'method',
        'property' => 'statutRapport',
    ],
    'supports' => ['App\Entity\Report\Rapport'],
    'initial_marking' => 'brouillon',
    'places' => [
        'brouillon',
        'soumis',
        'retourne',
        'approuve',
        'en_commission',
    ],
    'transitions' => [
        'soumettre' => [
            'from' => 'brouillon',
            'to' => 'soumis',
        ],
        'soumettre_apres_retour' => [
            'from' => 'retourne',
            'to' => 'soumis',
        ],
        'approuver' => [
            'from' => 'soumis',
            'to' => 'approuve',
        ],
        'retourner' => [
            'from' => 'soumis',
            'to' => 'retourne',
        ],
        'envoyer_commission' => [
            'from' => 'approuve',
            'to' => 'en_commission',
        ],
    ],
];
