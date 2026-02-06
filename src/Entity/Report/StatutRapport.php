<?php

declare(strict_types=1);

namespace App\Entity\Report;

enum StatutRapport: string
{
    case BROUILLON = 'brouillon';
    case SOUMIS = 'soumis';
    case RETOURNE = 'retourne';
    case APPROUVE = 'approuve';
    case EN_COMMISSION = 'en_commission';
}
