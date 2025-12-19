<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CritereEvaluation
 * 
 * Représente un critère d'évaluation pour les rapports/soutenances.
 * Table: critere_evaluation
 */
class CritereEvaluation extends Model
{
    protected string $table = 'critere_evaluation';
    protected string $primaryKey = 'id_critere';
    protected array $fillable = [
        'code_critere',
        'libelle',
        'description',
        'ponderation',
        'actif',
    ];

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un critère par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_critere' => $code]);
    }

    /**
     * Retourne tous les critères actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les critères triés par pondération
     * @return self[]
     */
    public static function triesParPonderation(): array
    {
        $sql = "SELECT * FROM critere_evaluation 
                WHERE actif = 1 
                ORDER BY ponderation DESC";

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
     * Crée un nouveau critère
     */
    public static function creer(
        string $code,
        string $libelle,
        float $ponderation,
        ?string $description = null
    ): self {
        $critere = new self([
            'code_critere' => $code,
            'libelle' => $libelle,
            'description' => $description,
            'ponderation' => $ponderation,
            'actif' => true,
        ]);
        $critere->save();
        return $critere;
    }

    /**
     * Active le critère
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive le critère
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Calcule la note pondérée
     */
    public function calculerNoteAmelioree(float $noteBase): float
    {
        return round($noteBase * ((float) $this->ponderation / 100), 2);
    }

    /**
     * Vérifie la validité du total des pondérations
     */
    public static function verifierTotalPonderations(): bool
    {
        $sql = "SELECT COALESCE(SUM(ponderation), 0) FROM critere_evaluation WHERE actif = 1";
        $stmt = self::raw($sql);
        $total = (float) $stmt->fetchColumn();

        // Le total devrait être proche de 100
        return abs($total - 100.0) < 0.01;
    }
}
