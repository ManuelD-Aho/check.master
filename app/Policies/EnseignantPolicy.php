<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Enseignant;
use Src\Support\Auth;

/**
 * Policy Enseignant
 * 
 * Définit les règles d'autorisation pour la gestion des enseignants.
 */
class EnseignantPolicy
{
    /**
     * Groupes autorisés à gérer les enseignants
     */
    private const ADMIN_GROUPS = [5, 9]; // Admin, Resp. Filière

    /**
     * Groupes autorisés à consulter les enseignants
     */
    private const VIEW_GROUPS = [5, 6, 8, 9, 10, 11, 12, 13]; // Tous sauf Communication

    /**
     * Groupe enseignant
     */
    private const TEACHER_GROUP = 12;

    /**
     * Vérifie si l'utilisateur peut voir la liste des enseignants
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
     * Vérifie si l'utilisateur peut voir un enseignant spécifique
     */
    public function view(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'enseignant peut voir son propre profil
        if ($this->isOwnProfile($user, $enseignant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer un enseignant
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
     * Vérifie si l'utilisateur peut modifier un enseignant
     */
    public function update(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'enseignant peut modifier son propre profil
        if ($this->isOwnProfile($user, $enseignant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un enseignant
     */
    public function delete(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seuls les admins peuvent supprimer
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut assigner l'enseignant comme directeur
     */
    public function assignAsDirecteur(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Vérifier que l'enseignant est habilité
        if (!$enseignant->estHabilite()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9, 11]); // Admin, Resp. Filière, Commission
    }

    /**
     * Vérifie si l'utilisateur peut assigner l'enseignant comme encadreur
     */
    public function assignAsEncadreur(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9, 11]); // Admin, Resp. Filière, Commission
    }

    /**
     * Vérifie si l'utilisateur peut assigner l'enseignant comme membre du jury
     */
    public function assignAsJury(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Vérifier que l'enseignant peut être juré
        if (!$enseignant->peutEtreJure()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9, 11]); // Admin, Resp. Filière, Commission
    }

    /**
     * Vérifie si l'utilisateur peut gérer les disponibilités de l'enseignant
     */
    public function manageDisponibilites(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'enseignant peut gérer ses propres disponibilités
        if ($this->isOwnProfile($user, $enseignant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir les statistiques de l'enseignant
     */
    public function viewStats(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'enseignant peut voir ses propres statistiques
        if ($this->isOwnProfile($user, $enseignant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier le quota d'encadrement
     */
    public function updateQuota(?Utilisateur $user, Enseignant $enseignant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si c'est le profil de l'enseignant lui-même
     */
    private function isOwnProfile(Utilisateur $user, Enseignant $enseignant): bool
    {
        if ($user->getGroupeId() !== self::TEACHER_GROUP) {
            return false;
        }

        return $user->getEnseignantId() === $enseignant->getId();
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
