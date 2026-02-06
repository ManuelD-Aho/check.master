<?php

declare(strict_types=1);

namespace App\Entity\Academic;

enum TypeNote: string
{
    case UE = 'ue';
    case ECUE = 'ecue';
    case MOYENNE_GENERALE = 'moyenne_generale';
    case MOYENNE_M1 = 'moyenne_m1';
    case MOYENNE_S1_M2 = 'moyenne_s1_m2';
}
