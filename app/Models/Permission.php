<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Permission
 * 
 * Représente les permissions d'un groupe sur une ressource.
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
     * Actions disponibles
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
    public function peutEffectuer(string $action): bool
    {
        return match ($action) {
            self::ACTION_LIRE => (bool) $this->peut_lire,
            self::ACTION_CREER => (bool) $this->peut_creer,
            self::ACTION_MODIFIER => (bool) $this->peut_modifier,
            self::ACTION_SUPPRIMER => (bool) $this->peut_supprimer,
            self::ACTION_EXPORTER => (bool) $this->peut_exporter,
            self::ACTION_VALIDER => (bool) $this->peut_valider,
            default => false,
        };
    }

    /**
     * Retourne les conditions sous forme de tableau
     */
    public function getConditions(): array
    {
        if (empty($this->conditions_json)) {
            return [];
        }
        return json_decode($this->conditions_json, true) ?? [];
    }

    /**
     * Retourne le groupe associé
     */
    public function getGroupe(): ?Groupe
    {
        if ($this->groupe_id === null) {
            return null;
        }
        return Groupe::find((int) $this->groupe_id);
    }

    /**
     * Retourne la ressource associée
     */
    public function getRessource(): ?Ressource
    {
        if ($this->ressource_id === null) {
            return null;
        }
        return Ressource::find((int) $this->ressource_id);
    }

    /**
     * Vérifie si un groupe a la permission sur une ressource
     */
    public static function verifier(int $groupeId, string $codeRessource, string $action): bool
    {
        $sql = "SELECT p.* FROM permissions p
                INNER JOIN ressources r ON r.id_ressource = p.ressource_id
                WHERE p.groupe_id = :groupe_id AND r.code_ressource = :code";

        $stmt = self::raw($sql, [
            'groupe_id' => $groupeId,
            'code' => $codeRessource,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false; // DENY by default
        }

        $permission = new self($row);
        $permission->exists = true;

        return $permission->peutEffectuer($action);
    }

    /**
     * Accorde une permission
     */
    public static function accorder(
        int $groupeId,
        int $ressourceId,
        array $actions
    ): self {
        // Chercher une permission existante
        $existing = self::firstWhere([
            'groupe_id' => $groupeId,
            'ressource_id' => $ressourceId,
        ]);

        if ($existing === null) {
            $existing = new self([
                'groupe_id' => $groupeId,
                'ressource_id' => $ressourceId,
            ]);
        }

        // Mettre à jour les actions
        foreach ($actions as $action => $autorise) {
            $colonne = 'peut_' . $action;
            if (property_exists($existing, $colonne) || in_array($colonne, $existing->fillable)) {
                $existing->$colonne = $autorise ? 1 : 0;
            }
        }

        $existing->save();
        return $existing;
    }

    /**
     * Révoque toutes les permissions d'un groupe sur une ressource
     */
    public static function revoquer(int $groupeId, int $ressourceId): bool
    {
        $permission = self::firstWhere([
            'groupe_id' => $groupeId,
            'ressource_id' => $ressourceId,
        ]);

        if ($permission === null) {
            return true;
        }

        return $permission->delete();
    }

    /**
     * Retourne toutes les permissions d'un groupe
     *
     * @return self[]
     */
    public static function pourGroupe(int $groupeId): array
    {
        return self::where(['groupe_id' => $groupeId]);
    }

    /**
     * Retourne les permissions détaillées d'un groupe (avec ressources)
     */
    public static function detailleesGroupe(int $groupeId): array
    {
        $sql = "SELECT p.*, r.code_ressource, r.nom_ressource, r.module
                FROM permissions p
                INNER JOIN ressources r ON r.id_ressource = p.ressource_id
                WHERE p.groupe_id = :groupe_id";

        $stmt = self::raw($sql, ['groupe_id' => $groupeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
