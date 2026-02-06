<?php

declare(strict_types=1);

namespace App\Entity\Staff;

enum TypeEnseignant: string
{
    case Permanent = 'permanent';
    case Vacataire = 'vacataire';
}
