<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Rattacher
 * 
 * Table pivot pour le système de permissions (Groupe -> Traitement -> Action).
 * Table: rattacher
 */
class Rattacher extends Model
{
    protected string $table = 'rattacher';
    protected string $primaryKey = 'id_rattacher';
    protected array $fillable = [
        'id_GU',
        'id_traitement',
        'id_action',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne le groupe utilisateur
     */
    public function groupeUtilisateur(): ?GroupeUtilisateur
    {
        return $this->belongsTo(GroupeUtilisateur::class, 'id_GU', 'id_GU');
    }

    /**
     * Retourne le traitement
     */
    public function traitement(): ?Traitement
    {
        return $this->belongsTo(Traitement::class, 'id_traitement', 'id_traitement');
    }

    /**
     * Retourne l'action
     */
    public function action(): ?Action
    {
        return $this->belongsTo(Action::class, 'id_action', 'id_action');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Vérifie si un groupe a une permission spécifique
     */
    public static function aPermission(int $guId, int $traitementId, int $actionId): bool
    {
        return self::count([
            'id_GU' => $guId,
            'id_traitement' => $traitementId,
            'id_action' => $actionId,
        ]) > 0;
    }

    /**
     * Retourne les permissions d'un groupe
     * @return self[]
     */
    public static function pourGroupe(int $guId): array
    {
        return self::where(['id_GU' => $guId]);
    }

    /**
     * Retourne les permissions pour un traitement
     * @return self[]
     */
    public static function pourTraitement(int $traitementId): array
    {
        return self::where(['id_traitement' => $traitementId]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Accorde une permission
     */
    public static function accorder(int $guId, int $traitementId, int $actionId): self
    {
        // Vérifier si la permission existe déjà
        $existant = self::firstWhere([
            'id_GU' => $guId,
            'id_traitement' => $traitementId,
            'id_action' => $actionId,
        ]);

        if ($existant) {
            return $existant;
        }

        $rattacher = new self([
            'id_GU' => $guId,
            'id_traitement' => $traitementId,
            'id_action' => $actionId,
        ]);
        $rattacher->save();
        return $rattacher;
    }

    /**
     * Révoque une permission
     */
    public static function revoquer(int $guId, int $traitementId, int $actionId): bool
    {
        $existant = self::firstWhere([
            'id_GU' => $guId,
            'id_traitement' => $traitementId,
            'id_action' => $actionId,
        ]);

        if ($existant) {
            return $existant->delete();
        }

        return false;
    }

    /**
     * Clone les permissions d'un groupe vers un autre
     */
    public static function clonerPermissions(int $guSourceId, int $guCibleId): int
    {
        $permissions = self::pourGroupe($guSourceId);
        $count = 0;

        foreach ($permissions as $perm) {
            self::accorder($guCibleId, (int) $perm->id_traitement, (int) $perm->id_action);
            $count++;
        }

        return $count;
    }
}
