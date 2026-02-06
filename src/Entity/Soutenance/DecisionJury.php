<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

enum DecisionJury: string
{
    case ADMIS = 'admis';
    case AJOURNE = 'ajourne';
    case REFUSE = 'refuse';
}
