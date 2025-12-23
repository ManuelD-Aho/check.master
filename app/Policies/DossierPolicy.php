<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\DossierEtudiant;
use Src\Support\Auth;

/**
 * Policy Dossier
 * 
 * Définit les règles d'autorisation pour la gestion des dossiers étudiants.
 */
class DossierPolicy
{
    /**
     * Groupes autorisés à gérer les dossiers
     */
    private const ADMIN_GROUPS = [5, 8, 9]; // Admin, Scolarité, Resp. Filière

    /**
     * Groupes autorisés à consulter les dossiers
     */
    private const VIEW_GROUPS = [5, 6, 7, 8, 9, 10, 11, 12]; // Admin, Secrétaire, Communication, Scolarité, Resp., Commission, Enseignant

    /**
     * Groupes autorisés à valider les dossiers
     */
    private const VALIDATE_GROUPS = [5, 8, 9, 11]; // Admin, Scolarité, Resp. Filière, Commission

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Vérifie si l'utilisateur peut voir la liste des dossiers
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
     * Vérifie si l'utilisateur peut voir un dossier spécifique
     */
    public function view(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir son propre dossier
        if ($this->isOwnDossier($user, $dossier)) {
            return true;
        }

        // Les encadreurs peuvent voir le dossier de leur étudiant
        if ($this->isEncadreur($user, $dossier)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer un dossier
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les étudiants peuvent créer leur propre dossier
        if ($user->getGroupeId() === self::STUDENT_GROUP) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier un dossier
     */
    public function update(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut modifier son dossier si le statut le permet
        if ($this->isOwnDossier($user, $dossier) && $this->canStudentEdit($dossier)) {
            return true;
        }

        // Les encadreurs peuvent modifier certaines parties
        if ($this->isEncadreur($user, $dossier)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un dossier
     */
    public function delete(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seuls les admins peuvent supprimer des dossiers
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut valider un dossier
     */
    public function validate(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::VALIDATE_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut rejeter un dossier
     */
    public function reject(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::VALIDATE_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut assigner des encadreurs
     */
    public function assignEncadreurs(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9, 11]); // Admin, Resp. Filière, Commission
    }

    /**
     * Vérifie si l'utilisateur peut exporter les dossiers
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
     * Vérifie si l'utilisateur peut effectuer une transition workflow
     */
    public function transition(?Utilisateur $user, DossierEtudiant $dossier, string $transitionCode): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Charger les transitions autorisées depuis la config workflow
        $workflowConfig = require __DIR__ . '/../config/workflow.php';
        $transition = $workflowConfig['transitions'][$transitionCode] ?? null;

        if ($transition === null) {
            return false;
        }

        $rolesAutorises = $transition['roles_autorises'] ?? [];
        return in_array($user->getGroupeId(), $rolesAutorises, true);
    }

    /**
     * Vérifie si l'utilisateur peut donner un avis favorable
     */
    public function donnerAvisFavorable(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seuls les encadreurs assignés peuvent donner un avis
        return $this->isEncadreur($user, $dossier);
    }

    /**
     * Vérifie si l'utilisateur peut consulter l'historique du dossier
     */
    public function viewHistory(?Utilisateur $user, DossierEtudiant $dossier): bool
    {
        return $this->view($user, $dossier);
    }

    /**
     * Vérifie si c'est le dossier de l'étudiant lui-même
     */
    private function isOwnDossier(Utilisateur $user, DossierEtudiant $dossier): bool
    {
        if ($user->getGroupeId() !== self::STUDENT_GROUP) {
            return false;
        }

        $etudiant = $user->getEtudiant();
        if ($etudiant === null) {
            return false;
        }

        return $dossier->getEtudiantId() === $etudiant->getId();
    }

    /**
     * Vérifie si l'utilisateur est encadreur du dossier
     */
    private function isEncadreur(Utilisateur $user, DossierEtudiant $dossier): bool
    {
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        return $dossier->getDirecteurId() === $enseignantId 
            || $dossier->getEncadreurId() === $enseignantId;
    }

    /**
     * Vérifie si l'étudiant peut éditer son dossier selon l'état workflow
     */
    private function canStudentEdit(DossierEtudiant $dossier): bool
    {
        // États où l'étudiant peut modifier son dossier
        $etatsEditables = ['inscrit', 'candidature_soumise'];
        $etatActuel = $dossier->getEtatActuel();

        return in_array($etatActuel, $etatsEditables, true);
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
