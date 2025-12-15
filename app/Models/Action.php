<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Action
 * 
 * Représente une action possible dans le système (ex: CRUD, Workflow).
 * Table: actions
 */
class Action extends Model
{
    protected string $table = 'actions';
    protected string $primaryKey = 'id_action';
    protected array $fillable = [
        'code_action',
        'lib_action',
        'description',
    ];

    /**
     * Trouve une action par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_action' => $code]);
    }
}
