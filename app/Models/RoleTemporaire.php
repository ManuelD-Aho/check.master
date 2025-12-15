<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Rôle Temporaire
 * 
 * Représente un rôle temporaire attribué à un utilisateur.
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
     * Codes de rôles temporaires connus
     */
    public const ROLE_PRESIDENT_JURY = 'president_jury';

    /**
     * Vérifie si le rôle est actuellement valide
     */
    public function estValide(): bool
    {
        if (!$this->actif) {
            return false;
        }

        $now = time();
        $debut = strtotime($this->valide_de);
        $fin = strtotime($this->valide_jusqu_a);

        return $now >= $debut && $now <= $fin;
    }

    /**
     * Retourne les permissions JSON décodées
     */
    public function getPermissions(): array
    {
        if ($this->permissions_json === null) {
            return [];
        }
        return json_decode($this->permissions_json, true) ?? [];
    }

    /**
     * Récupère les rôles temporaires actifs d'un utilisateur
     */
    public static function getRolesActifsUtilisateur(int $userId): array
    {
        $sql = "SELECT * FROM roles_temporaires 
                WHERE utilisateur_id = :user_id 
                AND actif = 1 
                AND valide_de <= NOW() 
                AND valide_jusqu_a >= NOW()";
        $stmt = self::raw($sql, ['user_id' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            return new self($row);
        }, $rows);
    }

    /**
     * Révoque tous les rôles temporaires expirés
     */
    public static function revoquerExpires(): int
    {
        $sql = "UPDATE roles_temporaires SET actif = 0 
                WHERE actif = 1 AND valide_jusqu_a < NOW()";
        $stmt = self::raw($sql);
        return $stmt->rowCount();
    }
}
