<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Etudiant;
use Src\Support\Auth;

/**
 * Policy Etudiant
 * 
 * Définit les règles d'autorisation pour la gestion des étudiants.
 */
class EtudiantPolicy
{
    /**
     * Groupes autorisés à gérer les étudiants
     */
    private const ADMIN_GROUPS = [5, 8, 9, 10]; // Admin, Scolarité, Resp. Filière, Resp. Niveau

    /**
     * Groupes autorisés à consulter les étudiants
     */
    private const VIEW_GROUPS = [5, 6, 7, 8, 9, 10, 11, 12]; // Admin, Secrétaire, Communication, Scolarité, Resp., Commission, Enseignant

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Vérifie si l'utilisateur peut voir la liste des étudiants
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
     * Vérifie si l'utilisateur peut voir un étudiant spécifique
     */
    public function view(?Utilisateur $user, Etudiant $etudiant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir son propre profil
        if ($this->isOwnProfile($user, $etudiant)) {
            return true;
        }

        // Les encadreurs peuvent voir leurs étudiants
        if ($this->isEncadreur($user, $etudiant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer un étudiant
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
     * Vérifie si l'utilisateur peut modifier un étudiant
     */
    public function update(?Utilisateur $user, Etudiant $etudiant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut modifier certaines informations de son profil
        if ($this->isOwnProfile($user, $etudiant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un étudiant
     */
    public function delete(?Utilisateur $user, Etudiant $etudiant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seuls les admins peuvent supprimer
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut soumettre une candidature pour l'étudiant
     */
    public function submitCandidature(?Utilisateur $user, Etudiant $etudiant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seul l'étudiant lui-même peut soumettre sa candidature
        return $this->isOwnProfile($user, $etudiant);
    }

    /**
     * Vérifie si l'utilisateur peut voir le dossier de l'étudiant
     */
    public function viewDossier(?Utilisateur $user, Etudiant $etudiant): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir son propre dossier
        if ($this->isOwnProfile($user, $etudiant)) {
            return true;
        }

        // Les encadreurs peuvent voir le dossier
        if ($this->isEncadreur($user, $etudiant)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut télécharger les documents de l'étudiant
     */
    public function downloadDocuments(?Utilisateur $user, Etudiant $etudiant): bool
    {
        return $this->viewDossier($user, $etudiant);
    }

    /**
     * Vérifie si l'utilisateur peut exporter les données de l'étudiant
     */
    public function export(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut importer des étudiants
     */
    public function import(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 8]); // Admin, Scolarité
    }

    /**
     * Vérifie si c'est le profil de l'étudiant lui-même
     */
    private function isOwnProfile(Utilisateur $user, Etudiant $etudiant): bool
    {
        if ($user->getGroupeId() !== self::STUDENT_GROUP) {
            return false;
        }

        // Vérifier si l'email correspond
        return $user->getEmail() === $etudiant->getEmail();
    }

    /**
     * Vérifie si l'utilisateur est encadreur de l'étudiant
     */
    private function isEncadreur(Utilisateur $user, Etudiant $etudiant): bool
    {
        // Vérifier si l'utilisateur est directeur de mémoire ou encadreur pédagogique
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        $dossier = $etudiant->getDossier();
        if ($dossier === null) {
            return false;
        }

        return $dossier->getDirecteurId() === $enseignantId 
            || $dossier->getEncadreurId() === $enseignantId;
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
