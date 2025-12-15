<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Rattacher
 * 
 * Noyau du système de Menu et Structure.
 * Lie des éléments de menu à des parents ou des rôles.
 * Table: rattacher
 */
class Rattacher extends Model
{
    protected string $table = 'rattacher';
    protected array $fillable = [
        'menu_id',
        'parent_id',
        'groupe_id',
        'ordre',
    ];
    // Clé composite
}
