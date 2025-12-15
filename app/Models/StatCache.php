<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle StatCache
 * 
 * Cache des statistiques lourdes (dashboard).
 * Table: stat_cache
 */
class StatCache extends Model
{
    protected string $table = 'stat_cache';
    protected string $primaryKey = 'cle_stat';
    protected array $fillable = [
        'cle_stat',
        'valeur_json',
        'genere_le',
        'expire_le',
    ];
}
