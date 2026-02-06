<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

enum TypePv: string
{
    case STANDARD = 'standard';
    case SIMPLIFIE = 'simplifie';
}
