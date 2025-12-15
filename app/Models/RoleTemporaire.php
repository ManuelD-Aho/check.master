<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RoleTemporaire
 * 
 * Représente un rôle temporaire attribué à un utilisateur (ex: Président Jury).
 * Table: roles_temporaires
 */
class RoleTemporaire extends Model
{
    protected string $table = 'roles_temporaires';
    protected string $primaryKey = 'id_role_temp';
    protected array $fillable = [
        'utilisateur_id',
        'role_code',
        'contexte_type',
        'contexte_id',
        'permissions_json',
        'actif',
        'valide_de',
        'valide_jusqu_a',
        'cree_par',
    ];

    /**
     * Codes de rôles temporaires
     */
    public const ROLE_PRESIDENT_JURY = 'president_jury';
    public const ROLE_MEMBRE_JURY = 'membre_jury';
    public const ROLE_RAPPORTEUR = 'rapporteur';
    public const ROLE_SUPERVISEUR = 'superviseur';

    /**
     * Vérifie si le rôle est actuellement actif
     */
    public function estActif(): bool
    {
        if (!$this->actif) {
            return false;
        }

        $now = time();
        $valideDe = strtotime($this->valide_de);
        $valideJusqua = strtotime($this->valide_jusqu_a);

        return $now >= $valideDe && $now <= $valideJusqua;
    }

    /**
     * Retourne les permissions sous forme de tableau
     */
    public function getPermissions(): array
    {
        if (empty($this->permissions_json)) {
            return [];
        }

        return json_decode($this->permissions_json, true) ?? [];
    }

    /**
     * Vérifie si le rôle a une permission spécifique
     */
    public function aPermission(string $permission): bool
    {
        $permissions = $this->getPermissions();
        return in_array($permission, $permissions, true);
    }

    /**
     * Retourne l'utilisateur associé
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if ($this->utilisateur_id === null) {
            return null;
        }
        return Utilisateur::find((int) $this->utilisateur_id);
    }

    /**
     * Désactive le rôle
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Crée un nouveau rôle temporaire
     */
    public static function creer(
        int $utilisateurId,
        string $roleCode,
        array $permissions,
        ?string $contexteType = null,
        ?int $contexteId = null,
        ?int $dureeJours = 30,
        ?int $creePar = null
    ): self {
        // Désactiver les rôles précédents du même type/contexte
        self::desactiverPrecedents($utilisateurId, $roleCode, $contexteType, $contexteId);

        $now = time();
        $role = new self([
            'utilisateur_id' => $utilisateurId,
            'role_code' => $roleCode,
            'contexte_type' => $contexteType,
            'contexte_id' => $contexteId,
            'permissions_json' => json_encode($permissions),
            'actif' => true,
            'valide_de' => date('Y-m-d H:i:s', $now),
            'valide_jusqu_a' => date('Y-m-d H:i:s', $now + ($dureeJours * 86400)),
            'cree_par' => $creePar,
        ]);
        $role->save();
        return $role;
    }

    /**
     * Désactive les rôles précédents similaires
     */
    private static function desactiverPrecedents(
        int $utilisateurId,
        string $roleCode,
        ?string $contexteType,
        ?int $contexteId
    ): void {
        $conditions = [
            'utilisateur_id' => $utilisateurId,
            'role_code' => $roleCode,
            'actif' => true,
        ];

        if ($contexteType !== null) {
            $conditions['contexte_type'] = $contexteType;
        }

        if ($contexteId !== null) {
            $conditions['contexte_id'] = $contexteId;
        }

        $roles = self::where($conditions);
        foreach ($roles as $role) {
            $role->desactiver();
        }
    }

    /**
     * Retourne les rôles actifs d'un utilisateur
     *
     * @return self[]
     */
    public static function rolesActifs(int $utilisateurId): array
    {
        $roles = self::where([
            'utilisateur_id' => $utilisateurId,
            'actif' => true,
        ]);

        return array_filter($roles, fn($r) => $r->estActif());
    }

    /**
     * Vérifie si un utilisateur a un rôle spécifique actif
     */
    public static function utilisateurARole(int $utilisateurId, string $roleCode): bool
    {
        $roles = self::where([
            'utilisateur_id' => $utilisateurId,
            'role_code' => $roleCode,
            'actif' => true,
        ]);

        foreach ($roles as $role) {
            if ($role->estActif()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prolonge la validité du rôle
     */
    public function prolonger(int $jours): void
    {
        $currentExpire = strtotime($this->valide_jusqu_a);
        $this->valide_jusqu_a = date('Y-m-d H:i:s', $currentExpire + ($jours * 86400));
        $this->save();
    }
}
