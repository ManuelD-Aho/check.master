<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle PasswordReset
 * 
 * Demande de réinitialisation de mot de passe.
 * Table: password_resets
 */
class PasswordReset extends Model
{
    protected string $table = 'password_resets';
    protected string $primaryKey = 'email'; // Souvent email est la clé
    protected array $fillable = [
        'email',
        'token',
        'created_at',
    ];

    // Note: Laravel utilise souvent une table sans ID auto-incrémenté ici
}
