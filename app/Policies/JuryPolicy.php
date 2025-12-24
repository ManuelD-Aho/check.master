<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\JuryMembre;
use App\Models\Soutenance;
use Src\Support\Auth;

/**
 * Policy Jury
 * 
 * Définit les règles d'autorisation pour la gestion des jurys.
 */
class JuryPolicy
{
    /**
     * Groupes autorisés à constituer les jurys
     */
    private const CONSTITUTION_GROUPS = [5, 9, 11]; // Admin, Resp. Filière, Commission

    /**
     * Groupes autorisés à consulter les jurys
     */
    private const VIEW_GROUPS = [5, 6, 8, 9, 10, 11, 12]; // Admin, Secrétaire, Scolarité, Resp., Commission, Enseignant

    /**
     * Vérifie si l'utilisateur peut voir la liste des jurys
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
     * Vérifie si l'utilisateur peut voir un jury spécifique
     */
    public function view(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir son jury
        if ($this->isOwnJury($user, $soutenance)) {
            return true;
        }

        // Les membres du jury peuvent voir
        if ($this->isJuryMember($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut constituer un jury
     */
    public function constitute(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier un jury
     */
    public function update(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut plus modifier si soutenance en cours ou terminée
        if ($soutenance->estEnCours() || $soutenance->estTerminee()) {
            return $this->hasAnyGroup($user, [5]); // Seul admin
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut ajouter un membre au jury
     */
    public function addMember(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Vérifier le nombre max de membres (5 membres)
        if ($soutenance->getNombreMembresJury() >= 5) {
            return false;
        }

        // Ne peut plus ajouter si soutenance en cours ou terminée
        if ($soutenance->estEnCours() || $soutenance->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut retirer un membre du jury
     */
    public function removeMember(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut plus retirer si soutenance en cours ou terminée
        if ($soutenance->estEnCours() || $soutenance->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut désigner le président du jury
     */
    public function designatePresident(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut plus désigner si soutenance en cours ou terminée
        if ($soutenance->estEnCours() || $soutenance->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut envoyer les invitations au jury
     */
    public function sendInvitations(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Jury doit être complet
        if (!$soutenance->juryEstComplet()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut répondre à une invitation
     */
    public function respondToInvitation(?Utilisateur $user, JuryMembre $invitation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seul le membre invité peut répondre
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        return $invitation->getEnseignantId() === $enseignantId;
    }

    /**
     * Vérifie si l'utilisateur peut voir les statistiques des jurys
     */
    public function viewStats(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9]); // Admin, Resp. Filière
    }

    /**
     * Vérifie si l'utilisateur peut valider le jury
     */
    public function validate(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Jury doit être complet avec toutes les acceptations
        if (!$soutenance->juryEstComplet() || !$soutenance->tousMembreOntAccepte()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CONSTITUTION_GROUPS);
    }

    /**
     * Vérifie si c'est le jury de l'utilisateur (étudiant)
     */
    private function isOwnJury(Utilisateur $user, Soutenance $soutenance): bool
    {
        if ($user->getGroupeId() !== 13) { // Groupe étudiant
            return false;
        }

        $etudiantId = $user->getEtudiantId();
        if ($etudiantId === null) {
            return false;
        }

        return $soutenance->getEtudiantId() === $etudiantId;
    }

    /**
     * Vérifie si l'utilisateur est membre du jury
     */
    private function isJuryMember(Utilisateur $user, Soutenance $soutenance): bool
    {
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        return $soutenance->aMembreJury($enseignantId);
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
