<?php

declare(strict_types=1);

namespace App\Entity\Commission;

enum DecisionEvaluation: string
{
    case Oui = 'oui';
    case Non = 'non';
}
