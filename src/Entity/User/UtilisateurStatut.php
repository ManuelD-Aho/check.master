<?php
declare(strict_types=1);

namespace App\Entity\User;

enum UtilisateurStatut: string
{
    case Actif = 'actif';
    case Inactif = 'inactif';
    case Bloque = 'bloque';
    case EnAttente = 'en_attente';
}
