<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Action
 * 
 * Représente une action possible dans le système pour les permissions.
 * Table: action
 */
class Action extends Model
{
    protected string $table = 'action';
    protected string $primaryKey = 'id_action';
    protected array $fillable = [
        'lib_action',
        'description',
    ];

    /**
     * Actions CRUD prédéfinies
     */
    public const ACTION_LIRE = 'Lire';
    public const ACTION_CREER = 'Creer';
    public const ACTION_MODIFIER = 'Modifier';
    public const ACTION_SUPPRIMER = 'Supprimer';
    public const ACTION_VALIDER = 'Valider';
    public const ACTION_EXPORTER = 'Exporter';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve une action par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_action' => $libelle]);
    }

    /**
     * Retourne toutes les actions
     * @return self[]
     */
    public static function toutes(): array
    {
        $sql = "SELECT * FROM action ORDER BY lib_action ASC";
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
     * Crée une nouvelle action
     */
    public static function creer(string $libelle, ?string $description = null): self
    {
        $action = new self([
            'lib_action' => $libelle,
            'description' => $description,
        ]);
        $action->save();
        return $action;
    }

    /**
     * Vérifie si c'est une action CRUD standard
     */
    public function estActionCRUD(): bool
    {
        return in_array($this->lib_action, [
            self::ACTION_LIRE,
            self::ACTION_CREER,
            self::ACTION_MODIFIER,
            self::ACTION_SUPPRIMER,
        ], true);
    }
}
