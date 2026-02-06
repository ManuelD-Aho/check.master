<?php

declare(strict_types=1);

namespace App\Entity\Stage;

enum ActionHistorique: string
{
    case Creation = 'creation';
    case Soumission = 'soumission';
    case Validation = 'validation';
    case Rejet = 'rejet';
    case Modification = 'modification';
}
