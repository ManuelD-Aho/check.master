<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Soutenance;
use App\Models\DossierEtudiant;
use Src\Support\Auth;

/**
 * Policy Soutenance
 * 
 * Définit les règles d'autorisation pour la gestion des soutenances.
 */
class SoutenancePolicy
{
    /**
     * Groupes autorisés à planifier les soutenances
     */
    private const PLANIFICATION_GROUPS = [5, 9, 11]; // Admin, Resp. Filière, Commission

    /**
     * Groupes autorisés à consulter les soutenances
     */
    private const VIEW_GROUPS = [5, 6, 7, 8, 9, 10, 11, 12, 13]; // Tous les groupes

    /**
     * Vérifie si l'utilisateur peut voir la liste des soutenances
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
     * Vérifie si l'utilisateur peut voir une soutenance spécifique
     */
    public function view(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir sa propre soutenance
        if ($this->isOwnSoutenance($user, $soutenance)) {
            return true;
        }

        // Les membres du jury peuvent voir
        if ($this->isJuryMember($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut planifier une soutenance
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::PLANIFICATION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier une soutenance
     */
    public function update(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut plus modifier si soutenance terminée
        if ($soutenance->estTerminee()) {
            return $this->hasAnyGroup($user, [5]); // Seul admin
        }

        return $this->hasAnyGroup($user, self::PLANIFICATION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut annuler une soutenance
     */
    public function cancel(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas annuler si terminée
        if ($soutenance->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::PLANIFICATION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut reporter une soutenance
     */
    public function postpone(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas reporter si terminée
        if ($soutenance->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::PLANIFICATION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut saisir les notes
     */
    public function enterNotes(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être en cours
        if (!$soutenance->estEnCours()) {
            return false;
        }

        // Seul le président du jury peut saisir les notes
        if ($this->isPresidentJury($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, [5]); // Admin en secours
    }

    /**
     * Vérifie si l'utilisateur peut valider les notes
     */
    public function validateNotes(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seul le président du jury peut valider
        if ($this->isPresidentJury($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, [5]); // Admin en secours
    }

    /**
     * Vérifie si l'utilisateur peut voir le PV de soutenance
     */
    public function viewPV(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir son PV après la soutenance
        if ($this->isOwnSoutenance($user, $soutenance) && $soutenance->estTerminee()) {
            return true;
        }

        // Les membres du jury peuvent voir
        if ($this->isJuryMember($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, [5, 6, 8, 9]); // Admin, Secrétaire, Scolarité, Resp. Filière
    }

    /**
     * Vérifie si l'utilisateur peut générer le PV de soutenance
     */
    public function generatePV(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // La soutenance doit être terminée
        if (!$soutenance->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 6]); // Admin, Secrétaire
    }

    /**
     * Vérifie si l'utilisateur peut démarrer la soutenance
     */
    public function start(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être planifiée
        if (!$soutenance->estPlanifiee()) {
            return false;
        }

        // Seul le président du jury peut démarrer
        if ($this->isPresidentJury($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, [5]); // Admin en secours
    }

    /**
     * Vérifie si l'utilisateur peut terminer la soutenance
     */
    public function finish(?Utilisateur $user, Soutenance $soutenance): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être en cours
        if (!$soutenance->estEnCours()) {
            return false;
        }

        // Seul le président du jury peut terminer
        if ($this->isPresidentJury($user, $soutenance)) {
            return true;
        }

        return $this->hasAnyGroup($user, [5]); // Admin en secours
    }

    /**
     * Vérifie si c'est la soutenance de l'utilisateur (étudiant)
     */
    private function isOwnSoutenance(Utilisateur $user, Soutenance $soutenance): bool
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
     * Vérifie si l'utilisateur est président du jury
     */
    private function isPresidentJury(Utilisateur $user, Soutenance $soutenance): bool
    {
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        return $soutenance->getPresidentJuryId() === $enseignantId;
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
