<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

enum StatutJury: string
{
    case EN_COMPOSITION = 'en_composition';
    case COMPLET = 'complet';
    case VALIDE = 'valide';
}
