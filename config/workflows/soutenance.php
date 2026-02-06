<?php

declare(strict_types=1);

return [
    'name' => 'soutenance',
    'type' => 'state_machine',
    'marking_store' => [
        'type' => 'method',
        'property' => 'statutSoutenance',
    ],
    'supports' => ['App\Entity\Soutenance\Soutenance'],
    'initial_marking' => 'programmee',
    'places' => [
        'programmee',
        'en_cours',
        'terminee',
        'reportee',
        'annulee',
    ],
    'transitions' => [
        'demarrer' => [
            'from' => 'programmee',
            'to' => 'en_cours',
        ],
        'terminer' => [
            'from' => 'en_cours',
            'to' => 'terminee',
        ],
        'reporter' => [
            'from' => 'programmee',
            'to' => 'reportee',
        ],
        'reprogrammer' => [
            'from' => 'reportee',
            'to' => 'programmee',
        ],
        'annuler' => [
            'from' => ['programmee', 'reportee'],
            'to' => 'annulee',
        ],
    ],
];
