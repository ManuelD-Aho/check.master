<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | États du Workflow
    |--------------------------------------------------------------------------
    */
    'states' => [
        'draft' => 'Brouillon',
        'submitted' => 'Soumis',
        'reviewed' => 'Revu',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'published' => 'Publié',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transitions autorisées
    |--------------------------------------------------------------------------
    */
    'transitions' => [
        'submit' => [
            'from' => ['draft', 'rejected'],
            'to' => 'submitted',
        ],
        'review' => [
            'from' => ['submitted'],
            'to' => 'reviewed',
        ],
        'approve' => [
            'from' => ['reviewed'],
            'to' => 'approved',
        ],
        'reject' => [
            'from' => ['submitted', 'reviewed'],
            'to' => 'rejected',
        ],
        'publish' => [
            'from' => ['approved'],
            'to' => 'published',
        ],
    ],
];
