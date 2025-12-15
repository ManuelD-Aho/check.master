<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Session Active
 * 
 * Représente une session utilisateur active.
 * Table: sessions_actives
 */
class SessionActive extends Model
{
    protected string $table = 'sessions_actives';
    protected string $primaryKey = 'id_session';
    protected array $fillable = [
        'utilisateur_id',
        'token_session',
        'ip_adresse',
        'user_agent',
        'derniere_activite',
        'expire_a',
    ];

    /**
     * Trouve une session par son token
     */
    public static function findByToken(string $token): ?self
    {
        return self::firstWhere(['token_session' => $token]);
    }

    /**
     * Vérifie si la session est expirée
     */
    public function estExpiree(): bool
    {
        if ($this->expire_a === null) {
            return true;
        }
        return strtotime($this->expire_a) < time();
    }

    /**
     * Vérifie si la session est valide
     */
    public function estValide(): bool
    {
        return !$this->estExpiree();
    }

    /**
     * Met à jour la dernière activité
     */
    public function majDerniereActivite(): void
    {
        $this->derniere_activite = date('Y-m-d H:i:s');
    }

    /**
     * Retourne les sessions d'un utilisateur
     */
    public static function getSessionsUtilisateur(int $userId): array
    {
        return self::where(['utilisateur_id' => $userId]);
    }

    /**
     * Supprime les sessions expirées
     */
    public static function nettoyerExpirees(): int
    {
        $sql = "DELETE FROM sessions_actives WHERE expire_a < NOW()";
        $stmt = self::raw($sql);
        return $stmt->rowCount();
    }

    /**
     * Supprime toutes les sessions d'un utilisateur
     */
    public static function supprimerToutesSessionsUtilisateur(int $userId): int
    {
        $sql = "DELETE FROM sessions_actives WHERE utilisateur_id = :user_id";
        $stmt = self::raw($sql, ['user_id' => $userId]);
        return $stmt->rowCount();
    }

    /**
     * Retourne l'utilisateur associé
     */
    public function getUtilisateur(): ?Utilisateur
    {
        $userId = $this->utilisateur_id;
        if ($userId === null) {
            return null;
        }
        return Utilisateur::find((int) $userId);
    }
}
