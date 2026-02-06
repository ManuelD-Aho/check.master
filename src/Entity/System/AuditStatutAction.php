<?php
declare(strict_types=1);

namespace App\Entity\System;

enum AuditStatutAction: string
{
    case Succes = 'succes';
    case Echec = 'echec';
    case Tentative = 'tentative';
}
