<?php

declare(strict_types=1);

namespace App\Entity\Student;

enum StatutEcheance: string
{
    case EnAttente = 'en_attente';
    case Payee = 'payee';
    case EnRetard = 'en_retard';
    case Partielle = 'partielle';
}
