<?php

declare(strict_types=1);

namespace App\Entity\Report;

enum TypeCommentaire: string
{
    case VERIFICATION = 'verification';
    case COMMISSION = 'commission';
    case RETOUR = 'retour';
}
