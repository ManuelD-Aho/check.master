<?php

declare(strict_types=1);

namespace App\Entity\Commission;

enum RoleEncadrement: string
{
    case DirecteurMemoire = 'directeur_memoire';
    case EncadreurPedagogique = 'encadreur_pedagogique';
}
