<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EmailBounce
 * 
 * Trace des emails rejetés ou non délivrés.
 * Table: email_bounces
 */
class EmailBounce extends Model
{
    protected string $table = 'email_bounces';
    protected string $primaryKey = 'id_bounce';
    protected array $fillable = [
        'email',
        'type_bounce',
        'raison',
        'compteur',
        'bloque',
    ];

    /**
     * Enregistre un bounce
     */
    public static function log(string $email, string $raison, string $type = 'Hard'): void
    {
        $existing = self::firstWhere(['email' => $email]);
        if ($existing) {
            $existing->compteur++;
            $existing->raison = $raison;
            $existing->type_bounce = $type;
            if ($type === 'Hard' || $existing->compteur >= 3) {
                $existing->bloque = true;
            }
            $existing->save();
        } else {
            $new = new self([
                'email' => $email,
                'type_bounce' => $type,
                'raison' => $raison,
                'compteur' => 1,
                'bloque' => ($type === 'Hard'),
            ]);
            $new->save();
        }
    }
}
