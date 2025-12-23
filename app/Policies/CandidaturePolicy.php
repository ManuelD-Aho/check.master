<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Candidature;
use App\Models\DossierEtudiant;
use Src\Support\Auth;

/**
 * Policy Candidature
 * 
 * Définit les règles d'autorisation pour la gestion des candidatures.
 */
class CandidaturePolicy
{
    /**
     * Groupes autorisés à gérer les candidatures
     */
    private const ADMIN_GROUPS = [5, 8]; // Admin, Scolarité

    /**
     * Groupes autorisés à valider les candidatures
     */
    private const VALIDATION_GROUPS = [5, 7, 8]; // Admin, Communication, Scolarité

    /**
     * Groupes autorisés à consulter les candidatures
     */
    private const VIEW_GROUPS = [5, 6, 7, 8, 9, 10, 11]; // Admin, Secrétaire, Communication, Scolarité, Resp., Commission

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Vérifie si l'utilisateur peut voir la liste des candidatures
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
     * Vérifie si l'utilisateur peut voir une candidature spécifique
     */
    public function view(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir sa propre candidature
        if ($this->isOwnCandidature($user, $candidature)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut soumettre une candidature
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seuls les étudiants peuvent soumettre une candidature
        return $user->getGroupeId() === self::STUDENT_GROUP;
    }

    /**
     * Vérifie si l'utilisateur peut modifier une candidature
     */
    public function update(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut modifier sa candidature si elle est encore en brouillon
        if ($this->isOwnCandidature($user, $candidature) && $candidature->estBrouillon()) {
            return true;
        }

        // Les admins peuvent toujours modifier
        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une candidature
     */
    public function delete(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut supprimer sa candidature si elle est encore en brouillon
        if ($this->isOwnCandidature($user, $candidature) && $candidature->estBrouillon()) {
            return true;
        }

        // Seuls les admins peuvent supprimer après soumission
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut soumettre la candidature (passer de brouillon à soumis)
     */
    public function submit(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être en brouillon
        if (!$candidature->estBrouillon()) {
            return false;
        }

        // Seul l'étudiant peut soumettre sa candidature
        return $this->isOwnCandidature($user, $candidature);
    }

    /**
     * Vérifie si l'utilisateur peut valider une candidature (scolarité)
     */
    public function validateScolarite(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être soumise
        if (!$candidature->estSoumise()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 8]); // Admin, Scolarité
    }

    /**
     * Vérifie si l'utilisateur peut valider le format (communication)
     */
    public function validateFormat(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être en vérification scolarité complétée
        if (!$candidature->estValideScolarite()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 7]); // Admin, Communication
    }

    /**
     * Vérifie si l'utilisateur peut rejeter une candidature
     */
    public function reject(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas rejeter si déjà validée complètement
        if ($candidature->estValideeCompletement()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::VALIDATION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut demander des corrections
     */
    public function requestCorrections(?Utilisateur $user, Candidature $candidature): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::VALIDATION_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir l'historique de la candidature
     */
    public function viewHistory(?Utilisateur $user, Candidature $candidature): bool
    {
        return $this->view($user, $candidature);
    }

    /**
     * Vérifie si l'utilisateur peut télécharger les documents de la candidature
     */
    public function downloadDocuments(?Utilisateur $user, Candidature $candidature): bool
    {
        return $this->view($user, $candidature);
    }

    /**
     * Vérifie si l'utilisateur peut exporter les candidatures
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
     * Vérifie si c'est la candidature de l'utilisateur (étudiant)
     */
    private function isOwnCandidature(Utilisateur $user, Candidature $candidature): bool
    {
        if ($user->getGroupeId() !== self::STUDENT_GROUP) {
            return false;
        }

        $etudiantId = $user->getEtudiantId();
        if ($etudiantId === null) {
            return false;
        }

        return $candidature->getEtudiantId() === $etudiantId;
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
