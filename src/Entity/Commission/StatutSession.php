<?php

declare(strict_types=1);

namespace App\Entity\Commission;

enum StatutSession: string
{
    case Ouverte = 'ouverte';
    case Fermee = 'fermee';
    case Archivee = 'archivee';
}
