<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowTransition
 * 
 * Représente une transition possible entre deux états du workflow.
 * Table: workflow_transitions
 */
class WorkflowTransition extends Model
{
    protected string $table = 'workflow_transitions';
    protected string $primaryKey = 'id_transition';
    protected array $fillable = [
        'etat_source_id',
        'etat_cible_id',
        'code_transition',
        'nom_transition',
        'roles_autorises',
        'conditions_json',
        'notifier',
    ];

    /**
     * Trouve une transition par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_transition' => $code]);
    }

    /**
     * Retourne l'état source
     */
    public function getEtatSource(): ?WorkflowEtat
    {
        if ($this->etat_source_id === null) {
            return null;
        }
        return WorkflowEtat::find((int) $this->etat_source_id);
    }

    /**
     * Retourne l'état cible
     */
    public function getEtatCible(): ?WorkflowEtat
    {
        if ($this->etat_cible_id === null) {
            return null;
        }
        return WorkflowEtat::find((int) $this->etat_cible_id);
    }

    /**
     * Retourne les rôles autorisés
     */
    public function getRolesAutorises(): array
    {
        if (empty($this->roles_autorises)) {
            return [];
        }
        return json_decode($this->roles_autorises, true) ?? [];
    }

    /**
     * Vérifie si un rôle est autorisé pour cette transition
     */
    public function estAutoriseePourRole(string $role): bool
    {
        $roles = $this->getRolesAutorises();
        return in_array($role, $roles, true) || in_array('*', $roles, true);
    }

    /**
     * Retourne les conditions de la transition
     */
    public function getConditions(): array
    {
        if (empty($this->conditions_json)) {
            return [];
        }
        return json_decode($this->conditions_json, true) ?? [];
    }

    /**
     * Vérifie si les notifications sont activées
     */
    public function doitNotifier(): bool
    {
        return (bool) $this->notifier;
    }

    /**
     * Trouve la transition entre deux états
     */
    public static function trouverTransition(int $sourceId, int $cibleId): ?self
    {
        return self::firstWhere([
            'etat_source_id' => $sourceId,
            'etat_cible_id' => $cibleId,
        ]);
    }

    /**
     * Retourne toutes les transitions depuis un état
     *
     * @return self[]
     */
    public static function depuisEtat(int $etatId): array
    {
        return self::where(['etat_source_id' => $etatId]);
    }

    /**
     * Retourne toutes les transitions vers un état
     *
     * @return self[]
     */
    public static function versEtat(int $etatId): array
    {
        return self::where(['etat_cible_id' => $etatId]);
    }
}
