<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Migration
 * 
 * Suivi des migrations de base de données.
 * Table: migrations
 */
class Migration extends Model
{
    protected string $table = 'migrations';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'migration',
        'batch',
    ];
}
