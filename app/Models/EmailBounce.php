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
     * Types de bounce
     */
    public const TYPE_HARD = 'Hard';
    public const TYPE_SOFT = 'Soft';
    public const TYPE_SPAM = 'Spam';

    /**
     * Seuil de blocage automatique pour soft bounces
     */
    public const SEUIL_BLOCAGE = 3;

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un email bounce
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email' => $email]);
    }

    /**
     * Retourne tous les emails bloqués
     * @return self[]
     */
    public static function bloques(): array
    {
        return self::where(['bloque' => true]);
    }

    /**
     * Vérifie si un email est bloqué
     */
    public static function estBloque(string $email): bool
    {
        $bounce = self::findByEmail($email);
        return $bounce !== null && (bool) $bounce->bloque;
    }

    // ===== MÉTHODES MÉTIER =====

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
            if ($type === self::TYPE_HARD || $existing->compteur >= self::SEUIL_BLOCAGE) {
                $existing->bloque = true;
            }
            $existing->save();
        } else {
            $new = new self([
                'email' => $email,
                'type_bounce' => $type,
                'raison' => $raison,
                'compteur' => 1,
                'bloque' => ($type === self::TYPE_HARD),
            ]);
            $new->save();
        }
    }

    /**
     * Débloque un email
     */
    public function debloquer(): void
    {
        $this->bloque = false;
        $this->compteur = 0;
        $this->save();
    }

    /**
     * Retourne les statistiques des bounces
     */
    public static function statistiques(): array
    {
        $sql = "SELECT type_bounce, COUNT(*) as total, 
                       SUM(CASE WHEN bloque = 1 THEN 1 ELSE 0 END) as bloques
                FROM email_bounces
                GROUP BY type_bounce";
        
        $stmt = self::raw($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Nettoie les anciens bounces non bloqués
     */
    public static function nettoyer(int $joursRetention = 90): int
    {
        $sql = "DELETE FROM email_bounces 
                WHERE bloque = 0 
                AND created_at < DATE_SUB(NOW(), INTERVAL :jours DAY)";
        
        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('jours', $joursRetention, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
