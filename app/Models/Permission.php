<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Permission
 * 
 * Représente les permissions CRUD d'un groupe sur une ressource.
 * Table: permissions
 */
class Permission extends Model
{
    protected string $table = 'permissions';
    protected string $primaryKey = 'id_permission';
    protected array $fillable = [
        'groupe_id',
        'ressource_id',
        'peut_lire',
        'peut_creer',
        'peut_modifier',
        'peut_supprimer',
        'peut_exporter',
        'peut_valider',
        'conditions_json',
    ];

    /**
     * Actions possibles
     */
    public const ACTION_LIRE = 'lire';
    public const ACTION_CREER = 'creer';
    public const ACTION_MODIFIER = 'modifier';
    public const ACTION_SUPPRIMER = 'supprimer';
    public const ACTION_EXPORTER = 'exporter';
    public const ACTION_VALIDER = 'valider';

    /**
     * Vérifie si une action est autorisée
     */
    public function peutFaire(string $action): bool
    {
        $column = 'peut_' . $action;
        return (bool) ($this->$column ?? false);
    }

    /**
     * Retourne les conditions JSON décodées
     */
    public function getConditions(): ?array
    {
        if ($this->conditions_json === null) {
            return null;
        }
        return json_decode($this->conditions_json, true);
    }

    /**
     * Récupère les permissions d'un groupe pour une ressource
     */
    public static function getPermissionsGroupeRessource(int $groupeId, int $ressourceId): ?self
    {
        return self::firstWhere([
            'groupe_id' => $groupeId,
            'ressource_id' => $ressourceId,
        ]);
    }

    /**
     * Récupère toutes les permissions d'un groupe
     */
    public static function getPermissionsGroupe(int $groupeId): array
    {
        return self::where(['groupe_id' => $groupeId]);
    }
}
