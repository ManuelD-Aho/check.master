<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle SessionActive
 * 
 * Gère les sessions utilisateur actives.
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
     * Durée de vie d'une session en secondes (8 heures)
     */
    public const DUREE_VIE_SESSION = 28800;

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur de la session
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve une session par son token
     */
    public static function findByToken(string $token): ?self
    {
        return self::firstWhere(['token_session' => $token]);
    }

    /**
     * Retourne les sessions actives d'un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId): array
    {
        return self::where(['utilisateur_id' => $utilisateurId]);
    }

    /**
     * Retourne les sessions expirées
     * @return self[]
     */
    public static function expirees(): array
    {
        $sql = "SELECT * FROM sessions_actives WHERE expire_a < NOW()";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES D'ÉTAT =====

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

    // ===== MÉTHODES MÉTIER =====

    /**
     * Génère un nouveau token de session
     */
    public static function genererToken(): string
    {
        return bin2hex(random_bytes(64));
    }

    /**
     * Crée une nouvelle session
     */
    public static function creer(
        int $utilisateurId,
        string $ipAdresse,
        string $userAgent
    ): self {
        $session = new self([
            'utilisateur_id' => $utilisateurId,
            'token_session' => self::genererToken(),
            'ip_adresse' => $ipAdresse,
            'user_agent' => $userAgent,
            'derniere_activite' => date('Y-m-d H:i:s'),
            'expire_a' => date('Y-m-d H:i:s', time() + self::DUREE_VIE_SESSION),
        ]);
        $session->save();
        return $session;
    }

    /**
     * Rafraîchit la session
     */
    public function rafraichir(): void
    {
        $this->derniere_activite = date('Y-m-d H:i:s');
        $this->expire_a = date('Y-m-d H:i:s', time() + self::DUREE_VIE_SESSION);
        $this->save();
    }

    /**
     * Régénère le token de session (après action sensible)
     */
    public function regenererToken(): string
    {
        $this->token_session = self::genererToken();
        $this->rafraichir();
        return $this->token_session;
    }

    /**
     * Invalide la session
     */
    public function invalider(): void
    {
        $this->delete();
    }

    /**
     * Supprime les sessions expirées
     */
    public static function nettoyerExpirees(): int
    {
        $sql = "DELETE FROM sessions_actives WHERE expire_a < NOW()";
        $stmt = self::raw($sql, []);
        return $stmt->rowCount();
    }

    /**
     * Supprime toutes les sessions d'un utilisateur
     */
    public static function supprimerPourUtilisateur(int $utilisateurId): int
    {
        $sql = "DELETE FROM sessions_actives WHERE utilisateur_id = :id";
        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        return $stmt->rowCount();
    }

    /**
     * Compte les sessions actives d'un utilisateur
     */
    public static function compterPourUtilisateur(int $utilisateurId): int
    {
        return self::count(['utilisateur_id' => $utilisateurId]);
    }

    /**
     * Retourne les informations d'agent utilisateur parsées
     */
    public function getInfosNavigation(): array
    {
        $userAgent = $this->user_agent ?? '';

        // Détection basique du navigateur
        $browser = 'Inconnu';
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }

        // Détection basique du système d'exploitation
        $os = 'Inconnu';
        if (strpos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $os = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            $os = 'iOS';
        }

        return [
            'navigateur' => $browser,
            'systeme' => $os,
            'ip' => $this->ip_adresse,
        ];
    }
}
