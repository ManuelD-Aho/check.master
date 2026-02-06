<?php

declare(strict_types=1);

namespace App\Entity\Staff;

enum TypeFonction: string
{
    case Enseignant = 'enseignant';
    case Administratif = 'administratif';
}
