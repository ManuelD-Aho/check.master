<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NiveauApprobation
 * 
 * Représente un niveau dans un processus d'approbation hiérarchique.
 * Table: niveau_approbation
 */
class NiveauApprobation extends Model
{
    protected string $table = 'niveau_approbation';
    protected string $primaryKey = 'id_niveau_approbation';
    protected array $fillable = [
        'lib_niveau',
        'ordre_niveau',
    ];

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un niveau par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_niveau' => $libelle]);
    }

    /**
     * Retourne les niveaux triés par ordre
     * @return self[]
     */
    public static function triesParOrdre(): array
    {
        $sql = "SELECT * FROM niveau_approbation ORDER BY ordre_niveau ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Trouve un niveau par son ordre
     */
    public static function findByOrdre(int $ordre): ?self
    {
        return self::firstWhere(['ordre_niveau' => $ordre]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau niveau
     */
    public static function creer(string $libelle, int $ordre): self
    {
        $niveau = new self([
            'lib_niveau' => $libelle,
            'ordre_niveau' => $ordre,
        ]);
        $niveau->save();
        return $niveau;
    }

    /**
     * Retourne le niveau suivant
     */
    public function niveauSuivant(): ?self
    {
        $sql = "SELECT * FROM niveau_approbation 
                WHERE ordre_niveau > :ordre 
                ORDER BY ordre_niveau ASC 
                LIMIT 1";

        $stmt = self::raw($sql, ['ordre' => $this->ordre_niveau]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne le niveau précédent
     */
    public function niveauPrecedent(): ?self
    {
        $sql = "SELECT * FROM niveau_approbation 
                WHERE ordre_niveau < :ordre 
                ORDER BY ordre_niveau DESC 
                LIMIT 1";

        $stmt = self::raw($sql, ['ordre' => $this->ordre_niveau]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Vérifie si c'est le premier niveau
     */
    public function estPremier(): bool
    {
        return $this->niveauPrecedent() === null;
    }

    /**
     * Vérifie si c'est le dernier niveau
     */
    public function estDernier(): bool
    {
        return $this->niveauSuivant() === null;
    }
}
