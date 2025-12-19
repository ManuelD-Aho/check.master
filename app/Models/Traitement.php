<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Traitement
 * 
 * Représente un traitement/module du système pour la gestion des permissions.
 * Table: traitement
 */
class Traitement extends Model
{
    protected string $table = 'traitement';
    protected string $primaryKey = 'id_traitement';
    protected array $fillable = [
        'lib_traitement',
        'description',
        'ordre_traitement',
        'actif',
    ];

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un traitement par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_traitement' => $libelle]);
    }

    /**
     * Retourne tous les traitements actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les traitements triés par ordre
     * @return self[]
     */
    public static function triesParOrdre(): array
    {
        $sql = "SELECT * FROM traitement WHERE actif = 1 ORDER BY ordre_traitement ASC";
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
     * Retourne les actions associées via rattacher
     * @return Action[]
     */
    public function actions(): array
    {
        $sql = "SELECT a.* FROM action a
                INNER JOIN rattacher r ON r.id_action = a.id_action
                WHERE r.id_traitement = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Action($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau traitement
     */
    public static function creer(
        string $libelle,
        ?string $description = null,
        ?int $ordre = null
    ): self {
        $traitement = new self([
            'lib_traitement' => $libelle,
            'description' => $description,
            'ordre_traitement' => $ordre ?? self::prochainOrdre(),
            'actif' => true,
        ]);
        $traitement->save();
        return $traitement;
    }

    /**
     * Retourne le prochain ordre disponible
     */
    public static function prochainOrdre(): int
    {
        $sql = "SELECT COALESCE(MAX(ordre_traitement), 0) + 1 FROM traitement";
        $stmt = self::raw($sql);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Active le traitement
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive le traitement
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }
}
