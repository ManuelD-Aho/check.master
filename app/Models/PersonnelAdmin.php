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

    /**
     * Trouve un personnel par son email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email_pers' => $email]);
    }

    /**
     * Retourne le nom complet
     */
    public function getNomComplet(): string
    {
        return trim($this->prenom_pers . ' ' . $this->nom_pers);
    }

    /**
     * Vérifie si le personnel est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    /**
     * Retourne la fonction du personnel
     */
    public function getFonction(): ?object
    {
        if ($this->fonction_id === null) {
            return null;
        }

        $sql = "SELECT * FROM fonctions WHERE id_fonction = :id";
        $stmt = self::raw($sql, ['id' => $this->fonction_id]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne tous les personnels actifs
     *
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les personnels par fonction
     *
     * @return self[]
     */
    public static function parFonction(int $fonctionId): array
    {
        return self::where(['fonction_id' => $fonctionId, 'actif' => true]);
    }
}
