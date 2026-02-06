<?php

declare(strict_types=1);

namespace App\Entity\Student;

enum StatutInscription: string
{
    case EnAttente = 'en_attente';
    case Partiel = 'partiel';
    case Solde = 'solde';
    case Annulee = 'annulee';
    case Suspendue = 'suspendue';
}
