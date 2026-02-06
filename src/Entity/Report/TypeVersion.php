<?php

declare(strict_types=1);

namespace App\Entity\Report;

enum TypeVersion: string
{
    case AUTO_SAVE = 'auto_save';
    case SOUMISSION = 'soumission';
    case MODIFICATION = 'modification';
}
