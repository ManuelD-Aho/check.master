<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle PersonnelAdmin
 * 
 * Représente un membre du personnel administratif.
 * Table: personnel_admin
 */
class PersonnelAdmin extends Model
{
    protected string $table = 'personnel_admin';
    protected string $primaryKey = 'id_pers_admin';
    protected array $fillable = [
        'nom_pers',
        'prenom_pers',
        'email_pers',
        'telephone_pers',
        'fonction_id',
        'actif',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne la fonction
     */
    public function fonction(): ?Fonction
    {
        if ($this->fonction_id === null) {
            return null;
        }
        return $this->belongsTo(Fonction::class, 'fonction_id', 'id_fonction');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email_pers' => $email]);
    }

    /**
     * Retourne tous les membres actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les membres par fonction
     * @return self[]
     */
    public static function parFonction(int $fonctionId): array
    {
        return self::where(['fonction_id' => $fonctionId, 'actif' => true]);
    }

    /**
     * Recherche
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM personnel_admin 
                WHERE actif = 1 AND (
                    nom_pers LIKE :terme OR 
                    prenom_pers LIKE :terme OR 
                    email_pers LIKE :terme
                )
                ORDER BY nom_pers, prenom_pers
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('terme', "%{$terme}%", \PDO::PARAM_STR);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le membre est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES HELPER =====

    /**
     * Retourne le nom complet
     */
    public function getNomComplet(): string
    {
        return trim($this->prenom_pers . ' ' . $this->nom_pers);
    }

    /**
     * Retourne le nom formel
     */
    public function getNomFormel(): string
    {
        return strtoupper($this->nom_pers ?? '') . ' ' . $this->prenom_pers;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Active le membre
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive le membre
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }
}
