<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Permission
 * 
 * Représente les permissions granulaires par groupe et ressource.
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

    // ===== RELATIONS =====

    /**
     * Retourne le groupe
     */
    public function groupe(): ?Groupe
    {
        return $this->belongsTo(Groupe::class, 'groupe_id', 'id_groupe');
    }

    /**
     * Retourne la ressource
     */
    public function ressource(): ?Ressource
    {
        return $this->belongsTo(Ressource::class, 'ressource_id', 'id_ressource');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve les permissions d'un groupe
     * @return self[]
     */
    public static function pourGroupe(int $groupeId): array
    {
        return self::where(['groupe_id' => $groupeId]);
    }

    /**
     * Trouve les permissions pour une ressource
     * @return self[]
     */
    public static function pourRessource(int $ressourceId): array
    {
        return self::where(['ressource_id' => $ressourceId]);
    }

    /**
     * Trouve la permission spécifique groupe/ressource
     */
    public static function trouver(int $groupeId, int $ressourceId): ?self
    {
        return self::firstWhere([
            'groupe_id' => $groupeId,
            'ressource_id' => $ressourceId,
        ]);
    }

    /**
     * Trouve la permission par groupe et code ressource
     */
    public static function trouverParCode(int $groupeId, string $codeRessource): ?self
    {
        $sql = "SELECT p.* FROM permissions p
                INNER JOIN ressources r ON r.id_ressource = p.ressource_id
                WHERE p.groupe_id = :gid AND r.code_ressource = :code
                LIMIT 1";

        $stmt = self::raw($sql, ['gid' => $groupeId, 'code' => $codeRessource]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie une action spécifique
     */
    public function peutAction(string $action): bool
    {
        $colonne = 'peut_' . $action;
        return (bool) ($this->$colonne ?? false);
    }

    /**
     * Vérifie si peut lire
     */
    public function peutLire(): bool
    {
        return (bool) $this->peut_lire;
    }

    /**
     * Vérifie si peut créer
     */
    public function peutCreer(): bool
    {
        return (bool) $this->peut_creer;
    }

    /**
     * Vérifie si peut modifier
     */
    public function peutModifier(): bool
    {
        return (bool) $this->peut_modifier;
    }

    /**
     * Vérifie si peut supprimer
     */
    public function peutSupprimer(): bool
    {
        return (bool) $this->peut_supprimer;
    }

    /**
     * Vérifie si peut exporter
     */
    public function peutExporter(): bool
    {
        return (bool) $this->peut_exporter;
    }

    /**
     * Vérifie si peut valider
     */
    public function peutValider(): bool
    {
        return (bool) $this->peut_valider;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les conditions JSON décodées
     */
    public function getConditions(): array
    {
        if (empty($this->conditions_json)) {
            return [];
        }
        return json_decode($this->conditions_json, true) ?? [];
    }

    /**
     * Définit les conditions JSON
     */
    public function setConditions(array $conditions): void
    {
        $this->conditions_json = json_encode($conditions);
    }

    /**
     * Retourne toutes les permissions sous forme de tableau
     */
    public function toPermissionsArray(): array
    {
        return [
            'lire' => $this->peutLire(),
            'creer' => $this->peutCreer(),
            'modifier' => $this->peutModifier(),
            'supprimer' => $this->peutSupprimer(),
            'exporter' => $this->peutExporter(),
            'valider' => $this->peutValider(),
        ];
    }

    /**
     * Crée ou met à jour une permission
     */
    public static function definir(
        int $groupeId,
        int $ressourceId,
        array $permissions,
        ?array $conditions = null
    ): self {
        $perm = self::trouver($groupeId, $ressourceId);

        if ($perm === null) {
            $perm = new self();
            $perm->groupe_id = $groupeId;
            $perm->ressource_id = $ressourceId;
        }

        $perm->peut_lire = $permissions['lire'] ?? false;
        $perm->peut_creer = $permissions['creer'] ?? false;
        $perm->peut_modifier = $permissions['modifier'] ?? false;
        $perm->peut_supprimer = $permissions['supprimer'] ?? false;
        $perm->peut_exporter = $permissions['exporter'] ?? false;
        $perm->peut_valider = $permissions['valider'] ?? false;

        if ($conditions !== null) {
            $perm->setConditions($conditions);
        }

        $perm->save();
        return $perm;
    }

    /**
     * Alias pour trouver() utilisé par ServicePermissions
     */
    public static function getPermissionsGroupeRessource(int $groupeId, int $ressourceId): ?self
    {
        return self::trouver($groupeId, $ressourceId);
    }

    /**
     * Vérifie si une action est permise (utilisé par ServicePermissions)
     */
    public function peutFaire(string $action): bool
    {
        return $this->peutAction($action);
    }

    /**
     * Accorde toutes les permissions
     */
    public function accorderTout(): void
    {
        $this->peut_lire = true;
        $this->peut_creer = true;
        $this->peut_modifier = true;
        $this->peut_supprimer = true;
        $this->peut_exporter = true;
        $this->peut_valider = true;
        $this->save();
    }

    /**
     * Révoque toutes les permissions
     */
    public function revoquerTout(): void
    {
        $this->peut_lire = false;
        $this->peut_creer = false;
        $this->peut_modifier = false;
        $this->peut_supprimer = false;
        $this->peut_exporter = false;
        $this->peut_valider = false;
        $this->save();
    }
}
