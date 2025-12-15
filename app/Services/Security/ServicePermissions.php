<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\Permission;
use App\Models\Ressource;
use App\Models\RoleTemporaire;
use App\Models\Utilisateur;
use App\Orm\Model;
use PDO;

/**
 * Service de Permissions
 * 
 * Vérifie les permissions d'un utilisateur sur les ressources.
 * Gère le cache des permissions et les rôles temporaires.
 * 
 * @see PRD RF-006 à RF-008
 * @see Constitution I - Database-Driven Architecture
 */
class ServicePermissions
{
    /**
     * Durée du cache en secondes (5 minutes)
     */
    private const CACHE_DUREE_SECONDES = 300;

    /**
     * Vérifie si un utilisateur a une permission sur une ressource
     */
    public static function verifier(int $userId, string $ressourceCode, string $action): bool
    {
        // 1. Vérifier le cache
        $cached = self::getFromCache($userId, $ressourceCode);
        if ($cached !== null) {
            return self::checkAction($cached, $action);
        }

        // 2. Récupérer les permissions depuis la DB
        $permissions = self::calculerPermissions($userId, $ressourceCode);

        // 3. Mettre en cache
        self::setCache($userId, $ressourceCode, $permissions);

        // 4. Vérifier l'action
        return self::checkAction($permissions, $action);
    }

    /**
     * Vérifie si une action est autorisée dans les permissions
     */
    private static function checkAction(array $permissions, string $action): bool
    {
        $key = 'peut_' . $action;
        return (bool) ($permissions[$key] ?? false);
    }

    /**
     * Calcule les permissions effectives d'un utilisateur pour une ressource
     */
    private static function calculerPermissions(int $userId, string $ressourceCode): array
    {
        $permissions = [
            'peut_lire' => false,
            'peut_creer' => false,
            'peut_modifier' => false,
            'peut_supprimer' => false,
            'peut_exporter' => false,
            'peut_valider' => false,
        ];

        // Trouver la ressource
        $ressource = Ressource::findByCode($ressourceCode);
        if ($ressource === null) {
            return $permissions;
        }

        $ressourceId = $ressource->getId();

        // Récupérer l'utilisateur et son groupe principal
        $user = Utilisateur::find($userId);
        if ($user === null) {
            return $permissions;
        }

        $groupeId = $user->getGroupeId();
        if ($groupeId !== null) {
            // Récupérer les permissions du groupe
            $perm = Permission::getPermissionsGroupeRessource($groupeId, $ressourceId);
            if ($perm !== null) {
                $permissions = self::fusionnerPermissions($permissions, $perm);
            }
        }

        // Ajouter les permissions des rôles temporaires (additifs)
        $rolesTemp = RoleTemporaire::getRolesActifsUtilisateur($userId);
        foreach ($rolesTemp as $role) {
            $rolePerms = $role->getPermissions();
            if (isset($rolePerms[$ressourceCode])) {
                $permissions = self::fusionnerPermissionsArray($permissions, $rolePerms[$ressourceCode]);
            }
        }

        return $permissions;
    }

    /**
     * Fusionne les permissions (OR logique - les rôles sont additifs)
     */
    private static function fusionnerPermissions(array $base, Permission $perm): array
    {
        return [
            'peut_lire' => $base['peut_lire'] || $perm->peutFaire('lire'),
            'peut_creer' => $base['peut_creer'] || $perm->peutFaire('creer'),
            'peut_modifier' => $base['peut_modifier'] || $perm->peutFaire('modifier'),
            'peut_supprimer' => $base['peut_supprimer'] || $perm->peutFaire('supprimer'),
            'peut_exporter' => $base['peut_exporter'] || $perm->peutFaire('exporter'),
            'peut_valider' => $base['peut_valider'] || $perm->peutFaire('valider'),
        ];
    }

    /**
     * Fusionne les permissions depuis un tableau
     */
    private static function fusionnerPermissionsArray(array $base, array $perms): array
    {
        return [
            'peut_lire' => $base['peut_lire'] || ($perms['lire'] ?? false),
            'peut_creer' => $base['peut_creer'] || ($perms['creer'] ?? false),
            'peut_modifier' => $base['peut_modifier'] || ($perms['modifier'] ?? false),
            'peut_supprimer' => $base['peut_supprimer'] || ($perms['supprimer'] ?? false),
            'peut_exporter' => $base['peut_exporter'] || ($perms['exporter'] ?? false),
            'peut_valider' => $base['peut_valider'] || ($perms['valider'] ?? false),
        ];
    }

    /**
     * Récupère les permissions depuis le cache
     */
    private static function getFromCache(int $userId, string $ressourceCode): ?array
    {
        $sql = "SELECT permissions_json FROM permissions_cache 
                WHERE utilisateur_id = :user_id 
                AND ressource_code = :ressource 
                AND expire_le > NOW()";

        try {
            $stmt = Model::raw($sql, [
                'user_id' => $userId,
                'ressource' => $ressourceCode,
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return null;
            }

            return json_decode($row['permissions_json'], true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Met en cache les permissions
     */
    private static function setCache(int $userId, string $ressourceCode, array $permissions): void
    {
        $expireLe = date('Y-m-d H:i:s', time() + self::CACHE_DUREE_SECONDES);
        $json = json_encode($permissions);

        $sql = "INSERT INTO permissions_cache (utilisateur_id, ressource_code, permissions_json, expire_le) 
                VALUES (:user_id, :ressource, :json, :expire)
                ON DUPLICATE KEY UPDATE permissions_json = :json2, expire_le = :expire2, genere_le = NOW()";

        try {
            Model::raw($sql, [
                'user_id' => $userId,
                'ressource' => $ressourceCode,
                'json' => $json,
                'expire' => $expireLe,
                'json2' => $json,
                'expire2' => $expireLe,
            ]);
        } catch (\Exception $e) {
            // Ignorer les erreurs de cache
        }
    }

    /**
     * Invalide le cache pour un utilisateur
     */
    public static function invaliderCache(int $userId): void
    {
        $sql = "DELETE FROM permissions_cache WHERE utilisateur_id = :user_id";
        try {
            Model::raw($sql, ['user_id' => $userId]);
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }
    }

    /**
     * Invalide tout le cache des permissions
     */
    public static function invaliderToutCache(): void
    {
        $sql = "DELETE FROM permissions_cache";
        try {
            Model::raw($sql);
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }
    }

    /**
     * Retourne toutes les permissions effectives d'un utilisateur
     */
    public static function getToutesPermissions(int $userId): array
    {
        $ressources = Ressource::all();
        $permissions = [];

        foreach ($ressources as $ressource) {
            $code = $ressource->code_ressource;
            $permissions[$code] = self::calculerPermissions($userId, $code);
        }

        return $permissions;
    }

    /**
     * Vérifie si l'utilisateur est administrateur (groupe 5)
     */
    public static function estAdministrateur(int $userId): bool
    {
        $user = Utilisateur::find($userId);
        if ($user === null) {
            return false;
        }

        return $user->getGroupeId() === 5;
    }

    /**
     * Vérifie plusieurs permissions à la fois
     */
    public static function verifierMultiple(int $userId, array $checks): bool
    {
        foreach ($checks as $check) {
            [$ressource, $action] = $check;
            if (!self::verifier($userId, $ressource, $action)) {
                return false;
            }
        }
        return true;
    }
}
