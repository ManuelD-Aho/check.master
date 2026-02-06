<?php

declare(strict_types=1);

namespace App\Entity\Report;

enum ActionValidation: string
{
    case APPROUVE = 'approuve';
    case RETOURNE = 'retourne';
}
