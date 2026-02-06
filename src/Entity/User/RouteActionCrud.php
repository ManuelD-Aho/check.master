<?php
declare(strict_types=1);

namespace App\Entity\User;

enum RouteActionCrud: string
{
    case Voir = 'voir';
    case Creer = 'creer';
    case Modifier = 'modifier';
    case Supprimer = 'supprimer';
}
