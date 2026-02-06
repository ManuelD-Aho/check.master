<?php

declare(strict_types=1);

namespace App\Entity\Stage;

enum StatutCandidature: string
{
    case Brouillon = 'brouillon';
    case Soumise = 'soumise';
    case Validee = 'validee';
    case Rejetee = 'rejetee';
}
