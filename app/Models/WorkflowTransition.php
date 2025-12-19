<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowTransition
 * 
 * Représente une transition autorisée entre deux états du workflow.
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

    // ===== RELATIONS =====

    /**
     * Retourne l'état source
     */
    public function etatSource(): ?WorkflowEtat
    {
        return $this->belongsTo(WorkflowEtat::class, 'etat_source_id', 'id_etat');
    }

    /**
     * Retourne l'état cible
     */
    public function etatCible(): ?WorkflowEtat
    {
        return $this->belongsTo(WorkflowEtat::class, 'etat_cible_id', 'id_etat');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_transition' => $code]);
    }

    /**
     * Trouve une transition entre deux états
     */
    public static function trouverTransition(int $etatSourceId, int $etatCibleId): ?self
    {
        return self::firstWhere([
            'etat_source_id' => $etatSourceId,
            'etat_cible_id' => $etatCibleId,
        ]);
    }

    /**
     * Retourne les transitions depuis un état
     * @return self[]
     */
    public static function depuisEtat(int $etatId): array
    {
        return self::where(['etat_source_id' => $etatId]);
    }

    /**
     * Retourne les transitions vers un état
     * @return self[]
     */
    public static function versEtat(int $etatId): array
    {
        return self::where(['etat_cible_id' => $etatId]);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si la transition doit déclencher des notifications
     */
    public function doitNotifier(): bool
    {
        return (bool) $this->notifier;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les rôles autorisés décodés
     */
    public function getRolesAutorises(): array
    {
        if (empty($this->roles_autorises)) {
            return [];
        }
        return json_decode($this->roles_autorises, true) ?? [];
    }

    /**
     * Définit les rôles autorisés
     */
    public function setRolesAutorises(array $roles): void
    {
        $this->roles_autorises = json_encode($roles);
    }

    /**
     * Retourne les conditions décodées
     */
    public function getConditions(): array
    {
        if (empty($this->conditions_json)) {
            return [];
        }
        return json_decode($this->conditions_json, true) ?? [];
    }

    /**
     * Définit les conditions
     */
    public function setConditions(array $conditions): void
    {
        $this->conditions_json = json_encode($conditions);
    }

    /**
     * Vérifie si un rôle est autorisé pour cette transition
     */
    public function roleAutorise(string $role): bool
    {
        $roles = $this->getRolesAutorises();
        if (empty($roles)) {
            return true; // Pas de restriction
        }
        return in_array($role, $roles, true);
    }

    /**
     * Retourne le libellé complet de la transition
     */
    public function getLibelleComplet(): string
    {
        $source = $this->etatSource();
        $cible = $this->etatCible();

        $sourceNom = $source ? $source->nom_etat : '?';
        $cibleNom = $cible ? $cible->nom_etat : '?';

        return "{$sourceNom} → {$cibleNom}";
    }
}
