<?php

declare(strict_types=1);

namespace App\Entity\Commission;

enum StatutPv: string
{
    case Brouillon = 'brouillon';
    case Finalise = 'finalise';
    case Envoye = 'envoye';
}
