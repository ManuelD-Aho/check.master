<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RoleJury
 * 
 * Rôle possible dans un jury (Président, etc.).
 * Table: roles_jury
 */
class RoleJury extends Model
{
    protected string $table = 'roles_jury';
    protected string $primaryKey = 'id_role_jury';
    protected array $fillable = [
        'code_role',
        'lib_role',
    ];

    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_role' => $code]);
    }
}
