<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

enum StatutSoutenance: string
{
    case PROGRAMMEE = 'programmee';
    case EN_COURS = 'en_cours';
    case TERMINEE = 'terminee';
    case REPORTEE = 'reportee';
    case ANNULEE = 'annulee';
}
