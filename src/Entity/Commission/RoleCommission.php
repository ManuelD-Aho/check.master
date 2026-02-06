<?php

declare(strict_types=1);

namespace App\Entity\Commission;

enum RoleCommission: string
{
    case President = 'president';
    case Membre = 'membre';
}
