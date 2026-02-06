<?php
declare(strict_types=1);

namespace App\Entity\System;

enum AppSettingType: string
{
    case String = 'string';
    case Number = 'number';
    case Boolean = 'boolean';
    case Json = 'json';
    case Encrypted = 'encrypted';
}
