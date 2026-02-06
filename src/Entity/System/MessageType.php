<?php
declare(strict_types=1);

namespace App\Entity\System;

enum MessageType: string
{
    case Info = 'info';
    case Erreur = 'erreur';
    case Succes = 'succes';
    case Warning = 'warning';
}
