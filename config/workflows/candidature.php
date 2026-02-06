<?php

declare(strict_types=1);

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

return [
    'name' => 'candidature',
    'type' => 'state_machine',
    'marking_store' => [
        'type' => 'method',
        'property' => 'statutCandidature',
    ],
    'supports' => ['App\Entity\Stage\Candidature'],
    'initial_marking' => 'brouillon',
    'places' => [
        'brouillon',
        'soumise',
        'validee',
        'rejetee',
    ],
    'transitions' => [
        'soumettre' => [
            'from' => 'brouillon',
            'to' => 'soumise',
        ],
        'valider' => [
            'from' => 'soumise',
            'to' => 'validee',
        ],
        'rejeter' => [
            'from' => 'soumise',
            'to' => 'rejetee',
        ],
        'modifier' => [
            'from' => 'rejetee',
            'to' => 'brouillon',
        ],
    ],
];
