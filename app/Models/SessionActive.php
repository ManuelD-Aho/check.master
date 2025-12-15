<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle SessionActive
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
     * Durée de session par défaut (en secondes)
     */
    public const SESSION_DURATION = 3600; // 1 heure

    /**
     * Trouve une session par son token
     */
    public static function findByToken(string $token): ?self
    {
        return self::firstWhere(['token_session' => $token]);
    }

    /**
     * Vérifie si la session est valide (non expirée)
     */
    public function estValide(): bool
    {
        if ($this->expire_a === null) {
            return false;
        }
        return strtotime($this->expire_a) > time();
    }

    /**
     * Met à jour la dernière activité
     */
    public function majDerniereActivite(): void
    {
        $this->derniere_activite = date('Y-m-d H:i:s');
    }

    /**
     * Prolonge la session
     */
    public function prolonger(int $seconds = self::SESSION_DURATION): void
    {
        $this->expire_a = date('Y-m-d H:i:s', time() + $seconds);
    }

    /**
     * Retourne l'utilisateur associé à cette session
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if ($this->utilisateur_id === null) {
            return null;
        }
        return Utilisateur::find((int) $this->utilisateur_id);
    }

    /**
     * Génère un nouveau token de session sécurisé
     */
    public static function genererToken(): string
    {
        return bin2hex(random_bytes(64));
    }

    /**
     * Crée une nouvelle session pour un utilisateur
     */
    public static function creer(
        int $utilisateurId,
        string $ipAdresse,
        string $userAgent,
        int $duree = self::SESSION_DURATION
    ): self {
        $session = new self([
            'utilisateur_id' => $utilisateurId,
            'token_session' => self::genererToken(),
            'ip_adresse' => $ipAdresse,
            'user_agent' => substr($userAgent, 0, 500),
            'derniere_activite' => date('Y-m-d H:i:s'),
            'expire_a' => date('Y-m-d H:i:s', time() + $duree),
        ]);
        $session->save();
        return $session;
    }

    /**
     * Invalide toutes les sessions d'un utilisateur
     */
    public static function invaliderTout(int $utilisateurId): int
    {
        $sessions = self::where(['utilisateur_id' => $utilisateurId]);
        $count = 0;
        foreach ($sessions as $session) {
            $session->delete();
            $count++;
        }
        return $count;
    }

    /**
     * Supprime les sessions expirées
     */
    public static function nettoyerExpirees(): int
    {
        $sql = "DELETE FROM sessions_actives WHERE expire_a < :now";
        $stmt = self::raw($sql, ['now' => date('Y-m-d H:i:s')]);
        return $stmt->rowCount();
    }

    /**
     * Compte les sessions actives d'un utilisateur
     */
    public static function compterActives(int $utilisateurId): int
    {
        $sessions = self::where(['utilisateur_id' => $utilisateurId]);
        return count(array_filter($sessions, fn($s) => $s->estValide()));
    }
}
