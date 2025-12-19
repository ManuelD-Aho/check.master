<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EscaladeNiveau
 * 
 * Définit les niveaux d'escalade et leurs délais de réponse.
 * Table: escalade_niveaux
 */
class EscaladeNiveau extends Model
{
    protected string $table = 'escalade_niveaux';
    protected string $primaryKey = 'id_niveau';
    protected array $fillable = [
        'niveau',
        'nom_niveau',
        'delai_reponse_jours',
    ];

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un niveau par son numéro
     */
    public static function findByNiveau(int $niveau): ?self
    {
        return self::firstWhere(['niveau' => $niveau]);
    }

    /**
     * Retourne tous les niveaux triés
     * @return self[]
     */
    public static function triesParNiveau(): array
    {
        $sql = "SELECT * FROM escalade_niveaux ORDER BY niveau ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau niveau d'escalade
     */
    public static function creer(
        int $niveau,
        string $nom,
        int $delaiJours
    ): self {
        $niveauObj = new self([
            'niveau' => $niveau,
            'nom_niveau' => $nom,
            'delai_reponse_jours' => $delaiJours,
        ]);
        $niveauObj->save();
        return $niveauObj;
    }

    /**
     * Retourne le niveau suivant
     */
    public function niveauSuivant(): ?self
    {
        $sql = "SELECT * FROM escalade_niveaux 
                WHERE niveau > :niveau 
                ORDER BY niveau ASC 
                LIMIT 1";

        $stmt = self::raw($sql, ['niveau' => $this->niveau]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Vérifie s'il existe un niveau supérieur
     */
    public function aNiveauSuperieur(): bool
    {
        return $this->niveauSuivant() !== null;
    }

    /**
     * Retourne le nombre maximum de niveaux
     */
    public static function nombreMaxNiveaux(): int
    {
        $sql = "SELECT MAX(niveau) FROM escalade_niveaux";
        $stmt = self::raw($sql);
        return (int) $stmt->fetchColumn();
    }
}
