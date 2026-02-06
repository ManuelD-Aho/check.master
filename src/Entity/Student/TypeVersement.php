<?php

declare(strict_types=1);

namespace App\Entity\Student;

enum TypeVersement: string
{
    case Inscription = 'inscription';
    case Scolarite = 'scolarite';
}
