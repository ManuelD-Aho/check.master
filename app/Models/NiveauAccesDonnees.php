<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NiveauAccesDonnees
 * 
 * Représente un niveau de confidentialité/accès aux données.
 * Table: niveau_acces_donnees
 */
class NiveauAccesDonnees extends Model
{
    protected string $table = 'niveau_acces_donnees';
    protected string $primaryKey = 'id_niv_acces_donnee';
    protected array $fillable = [
        'lib_niveau_acces',
        'description',
    ];

    /**
     * Niveaux prédéfinis
     */
    public const NIVEAU_PUBLIC = 'Public';
    public const NIVEAU_CONFIDENTIEL = 'Confidentiel';
    public const NIVEAU_RESTREINT = 'Restreint';
    public const NIVEAU_SECRET = 'Secret';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un niveau par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_niveau_acces' => $libelle]);
    }

    /**
     * Retourne tous les niveaux
     * @return self[]
     */
    public static function tous(): array
    {
        $sql = "SELECT * FROM niveau_acces_donnees ORDER BY lib_niveau_acces ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== RELATIONS =====

    /**
     * Retourne les utilisateurs ayant ce niveau d'accès
     * @return Utilisateur[]
     */
    public function utilisateurs(): array
    {
        return $this->hasMany(Utilisateur::class, 'id_niv_acces_donnee', 'id_niv_acces_donnee');
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau niveau
     */
    public static function creer(string $libelle, ?string $description = null): self
    {
        $niveau = new self([
            'lib_niveau_acces' => $libelle,
            'description' => $description,
        ]);
        $niveau->save();
        return $niveau;
    }
}
