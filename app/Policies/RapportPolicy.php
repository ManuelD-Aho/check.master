<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Rapport;
use Src\Support\Auth;

/**
 * Policy Rapport
 * 
 * Définit les règles d'autorisation pour la gestion des rapports de mémoire.
 */
class RapportPolicy
{
    /**
     * Groupes autorisés à gérer les rapports
     */
    private const ADMIN_GROUPS = [5, 7, 9, 11]; // Admin, Communication, Resp. Filière, Commission

    /**
     * Groupes autorisés à consulter les rapports
     */
    private const VIEW_GROUPS = [5, 6, 7, 8, 9, 10, 11, 12]; // Admin, Secrétaire, Communication, Scolarité, Resp., Commission, Enseignant

    /**
     * Groupes autorisés à valider les rapports
     */
    private const VALIDATE_GROUPS = [5, 7, 11, 12]; // Admin, Communication, Commission, Enseignant (encadreur)

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Vérifie si l'utilisateur peut voir la liste des rapports
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
     * Vérifie si l'utilisateur peut voir un rapport spécifique
     */
    public function view(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir son propre rapport
        if ($this->isOwnRapport($user, $rapport)) {
            return true;
        }

        // Les encadreurs peuvent voir le rapport de leur étudiant
        if ($this->isEncadreur($user, $rapport)) {
            return true;
        }

        // Les membres du jury peuvent voir le rapport
        if ($this->isJuryMember($user, $rapport)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer un rapport
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les étudiants peuvent soumettre leur rapport
        if ($user->getGroupeId() === self::STUDENT_GROUP) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier un rapport
     */
    public function update(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut modifier son rapport si le statut le permet
        if ($this->isOwnRapport($user, $rapport) && $this->canStudentEdit($rapport)) {
            return true;
        }

        // Les encadreurs peuvent annoter/commenter le rapport
        if ($this->isEncadreur($user, $rapport)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un rapport
     */
    public function delete(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut supprimer son rapport tant qu'il n'est pas soumis
        if ($this->isOwnRapport($user, $rapport) && $this->canStudentDelete($rapport)) {
            return true;
        }

        // Seuls les admins peuvent supprimer des rapports soumis
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut valider le format du rapport
     */
    public function validateFormat(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Communication valide le format
        return $this->hasAnyGroup($user, [5, 7]);
    }

    /**
     * Vérifie si l'utilisateur peut valider le contenu du rapport (commission)
     */
    public function validateContent(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 11]); // Admin, Commission
    }

    /**
     * Vérifie si l'utilisateur peut rejeter un rapport
     */
    public function reject(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::VALIDATE_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut télécharger un rapport
     */
    public function download(?Utilisateur $user, Rapport $rapport): bool
    {
        return $this->view($user, $rapport);
    }

    /**
     * Vérifie si l'utilisateur peut ajouter des annotations
     */
    public function annotate(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les encadreurs peuvent annoter
        if ($this->isEncadreur($user, $rapport)) {
            return true;
        }

        // Les membres de la commission peuvent annoter
        if ($this->hasAnyGroup($user, [5, 7, 11])) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur peut soumettre le rapport pour évaluation
     */
    public function submit(?Utilisateur $user, Rapport $rapport): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->isOwnRapport($user, $rapport);
    }

    /**
     * Vérifie si l'utilisateur peut voir les commentaires/annotations
     */
    public function viewAnnotations(?Utilisateur $user, Rapport $rapport): bool
    {
        return $this->view($user, $rapport);
    }

    /**
     * Vérifie si c'est le rapport de l'étudiant lui-même
     */
    private function isOwnRapport(Utilisateur $user, Rapport $rapport): bool
    {
        if ($user->getGroupeId() !== self::STUDENT_GROUP) {
            return false;
        }

        $etudiant = $user->getEtudiant();
        if ($etudiant === null) {
            return false;
        }

        $dossier = $rapport->getDossier();
        if ($dossier === null) {
            return false;
        }

        return $dossier->getEtudiantId() === $etudiant->getId();
    }

    /**
     * Vérifie si l'utilisateur est encadreur du rapport
     */
    private function isEncadreur(Utilisateur $user, Rapport $rapport): bool
    {
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        $dossier = $rapport->getDossier();
        if ($dossier === null) {
            return false;
        }

        return $dossier->getDirecteurId() === $enseignantId 
            || $dossier->getEncadreurId() === $enseignantId;
    }

    /**
     * Vérifie si l'utilisateur est membre du jury
     */
    private function isJuryMember(Utilisateur $user, Rapport $rapport): bool
    {
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        $dossier = $rapport->getDossier();
        if ($dossier === null) {
            return false;
        }

        // Vérifier si l'enseignant est membre du jury pour ce dossier
        $jury = $dossier->getJury();
        if ($jury === null) {
            return false;
        }

        return $jury->hasMembre($enseignantId);
    }

    /**
     * Vérifie si l'étudiant peut éditer son rapport selon l'état
     */
    private function canStudentEdit(Rapport $rapport): bool
    {
        $statut = $rapport->getStatut();
        // États où l'étudiant peut modifier son rapport
        $etatsEditables = ['brouillon', 'a_corriger'];
        return in_array($statut, $etatsEditables, true);
    }

    /**
     * Vérifie si l'étudiant peut supprimer son rapport
     */
    private function canStudentDelete(Rapport $rapport): bool
    {
        $statut = $rapport->getStatut();
        return $statut === 'brouillon';
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
