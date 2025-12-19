<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Brouillon
 * 
 * Gère les brouillons de saisie pour les formulaires longs.
 * Permet de sauvegarder et restaurer les données partielles.
 * Table: brouillons
 */
class Brouillon extends Model
{
    protected string $table = 'brouillons';
    protected string $primaryKey = 'id_brouillon';
    protected array $fillable = [
        'utilisateur_id',
        'type_formulaire',
        'donnees_json',
        'cree_le',
        'mis_a_jour_le',
        'expire_le',
    ];

    /**
     * Types de formulaires
     */
    public const TYPE_CANDIDATURE = 'candidature';
    public const TYPE_RAPPORT = 'rapport';
    public const TYPE_COMPTE_RENDU = 'compte_rendu';
    public const TYPE_SOUTENANCE = 'soutenance';
    public const TYPE_NOTES = 'notes';

    /**
     * Durée d'expiration par défaut (7 jours en secondes)
     */
    public const EXPIRATION_DEFAUT = 604800;

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur propriétaire
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne tous les brouillons d'un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT * FROM brouillons 
                WHERE utilisateur_id = :id 
                AND (expire_le IS NULL OR expire_le > NOW())
                ORDER BY mis_a_jour_le DESC";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Trouve un brouillon spécifique
     */
    public static function trouver(int $utilisateurId, string $type): ?self
    {
        $sql = "SELECT * FROM brouillons 
                WHERE utilisateur_id = :id 
                AND type_formulaire = :type
                AND (expire_le IS NULL OR expire_le > NOW())
                LIMIT 1";

        $stmt = self::raw($sql, ['id' => $utilisateurId, 'type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Sauvegarde un brouillon
     */
    public static function sauvegarder(
        int $userId,
        string $type,
        array $data,
        ?int $expirationSecondes = null
    ): self {
        $expirationSecondes = $expirationSecondes ?? self::EXPIRATION_DEFAUT;

        $existing = self::trouver($userId, $type);

        if ($existing !== null) {
            $existing->donnees_json = json_encode($data);
            $existing->mis_a_jour_le = date('Y-m-d H:i:s');
            $existing->expire_le = date('Y-m-d H:i:s', time() + $expirationSecondes);
            $existing->save();
            return $existing;
        }

        $brouillon = new self([
            'utilisateur_id' => $userId,
            'type_formulaire' => $type,
            'donnees_json' => json_encode($data),
            'cree_le' => date('Y-m-d H:i:s'),
            'mis_a_jour_le' => date('Y-m-d H:i:s'),
            'expire_le' => date('Y-m-d H:i:s', time() + $expirationSecondes),
        ]);
        $brouillon->save();
        return $brouillon;
    }

    /**
     * Récupère un brouillon (données décodées)
     */
    public static function recuperer(int $userId, string $type): ?array
    {
        $brouillon = self::trouver($userId, $type);

        if ($brouillon === null) {
            return null;
        }

        return $brouillon->getDonnees();
    }

    /**
     * Supprime un brouillon
     */
    public static function supprimer(int $userId, string $type): bool
    {
        $brouillon = self::trouver($userId, $type);

        if ($brouillon !== null) {
            return $brouillon->delete();
        }

        return false;
    }

    /**
     * Supprime tous les brouillons d'un utilisateur
     */
    public static function supprimerTous(int $userId): int
    {
        $sql = "DELETE FROM brouillons WHERE utilisateur_id = :id";
        $stmt = self::raw($sql, ['id' => $userId]);
        return $stmt->rowCount();
    }

    /**
     * Supprime les brouillons expirés
     */
    public static function nettoyerExpires(): int
    {
        $sql = "DELETE FROM brouillons WHERE expire_le IS NOT NULL AND expire_le < NOW()";
        $stmt = self::raw($sql);
        return $stmt->rowCount();
    }

    /**
     * Retourne les données décodées
     */
    public function getDonnees(): array
    {
        if (empty($this->donnees_json)) {
            return [];
        }
        return json_decode($this->donnees_json, true) ?? [];
    }

    /**
     * Met à jour les données
     */
    public function setDonnees(array $data): void
    {
        $this->donnees_json = json_encode($data);
        $this->mis_a_jour_le = date('Y-m-d H:i:s');
    }

    /**
     * Vérifie si le brouillon est expiré
     */
    public function estExpire(): bool
    {
        if ($this->expire_le === null) {
            return false;
        }

        return strtotime($this->expire_le) < time();
    }

    /**
     * Prolonge la durée de vie du brouillon
     */
    public function prolonger(int $secondes = null): void
    {
        $secondes = $secondes ?? self::EXPIRATION_DEFAUT;
        $this->expire_le = date('Y-m-d H:i:s', time() + $secondes);
        $this->save();
    }

    /**
     * Vérifie si un brouillon existe
     */
    public static function existe(int $userId, string $type): bool
    {
        return self::trouver($userId, $type) !== null;
    }

    /**
     * Compte les brouillons d'un utilisateur
     */
    public static function compter(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM brouillons 
                WHERE utilisateur_id = :id 
                AND (expire_le IS NULL OR expire_le > NOW())";
        $stmt = self::raw($sql, ['id' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
