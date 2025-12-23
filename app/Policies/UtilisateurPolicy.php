<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use Src\Support\Auth;

/**
 * Policy Utilisateur
 * 
 * Définit les règles d'autorisation pour la gestion des utilisateurs.
 */
class UtilisateurPolicy
{
    /**
     * Groupes autorisés à gérer les utilisateurs
     */
    private const ADMIN_GROUPS = [5]; // Administrateur

    /**
     * Groupes autorisés à consulter les utilisateurs
     */
    private const VIEW_GROUPS = [5, 6, 8]; // Admin, Secrétaire, Scolarité

    /**
     * Vérifie si l'utilisateur peut voir la liste des utilisateurs
     */
    public function viewAny(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir un utilisateur spécifique
     */
    public function view(?Utilisateur $user, Utilisateur $target): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'utilisateur peut voir son propre profil
        if ($user->getId() === $target->getId()) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer un nouvel utilisateur
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier un utilisateur
     */
    public function update(?Utilisateur $user, Utilisateur $target): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'utilisateur peut modifier son propre profil (certains champs)
        if ($user->getId() === $target->getId()) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer/désactiver un utilisateur
     */
    public function delete(?Utilisateur $user, Utilisateur $target): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Un utilisateur ne peut pas se supprimer lui-même
        if ($user->getId() === $target->getId()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut réinitialiser le mot de passe
     */
    public function resetPassword(?Utilisateur $user, Utilisateur $target): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'utilisateur peut réinitialiser son propre mot de passe
        if ($user->getId() === $target->getId()) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier les permissions
     */
    public function managePermissions(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir les logs d'audit
     */
    public function viewAuditLogs(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur appartient à l'un des groupes
     *
     * @param array<int> $groupIds
     */
    private function hasAnyGroup(Utilisateur $user, array $groupIds): bool
    {
        $userGroupId = $user->getGroupeId();
        return in_array($userGroupId, $groupIds, true);
    }
}
