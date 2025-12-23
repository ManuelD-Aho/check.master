<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Reclamation;
use Src\Support\Auth;

/**
 * Policy Réclamation
 * 
 * Définit les règles d'autorisation pour la gestion des réclamations.
 */
class ReclamationPolicy
{
    /**
     * Groupes autorisés à gérer les réclamations
     */
    private const ADMIN_GROUPS = [5, 6, 8, 9]; // Admin, Secrétaire, Scolarité, Resp. Filière

    /**
     * Groupes autorisés à consulter les réclamations
     */
    private const VIEW_GROUPS = [5, 6, 8, 9, 10]; // Admin, Secrétaire, Scolarité, Resp. Filière, Resp. Niveau

    /**
     * Groupes autorisés à traiter les réclamations
     */
    private const PROCESS_GROUPS = [5, 8, 9]; // Admin, Scolarité, Resp. Filière

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Groupe enseignant
     */
    private const TEACHER_GROUP = 12;

    /**
     * Vérifie si l'utilisateur peut voir la liste des réclamations
     */
    public function viewAny(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les étudiants peuvent voir leurs propres réclamations via une autre méthode
        if ($user->getGroupeId() === self::STUDENT_GROUP) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir une réclamation spécifique
     */
    public function view(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'auteur peut voir sa propre réclamation
        if ($this->isOwner($user, $reclamation)) {
            return true;
        }

        // La personne concernée peut voir la réclamation
        if ($this->isConcerned($user, $reclamation)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer une réclamation
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les étudiants et enseignants peuvent créer des réclamations
        if (in_array($user->getGroupeId(), [self::STUDENT_GROUP, self::TEACHER_GROUP], true)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier une réclamation
     */
    public function update(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'auteur peut modifier sa réclamation tant qu'elle n'est pas traitée
        if ($this->isOwner($user, $reclamation) && $this->canOwnerEdit($reclamation)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une réclamation
     */
    public function delete(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'auteur peut supprimer sa réclamation tant qu'elle n'est pas traitée
        if ($this->isOwner($user, $reclamation) && $this->canOwnerDelete($reclamation)) {
            return true;
        }

        // Seuls les admins peuvent supprimer des réclamations traitées
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut traiter une réclamation
     */
    public function process(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::PROCESS_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut répondre à une réclamation
     */
    public function respond(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // La personne concernée peut répondre
        if ($this->isConcerned($user, $reclamation)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::PROCESS_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut clôturer une réclamation
     */
    public function close(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::PROCESS_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut rouvrir une réclamation
     */
    public function reopen(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'auteur peut rouvrir sa réclamation clôturée
        if ($this->isOwner($user, $reclamation) && $this->isClosed($reclamation)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut escalader une réclamation
     */
    public function escalate(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9]); // Admin, Resp. Filière
    }

    /**
     * Vérifie si l'utilisateur peut ajouter un commentaire
     */
    public function comment(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'auteur et la personne concernée peuvent commenter
        if ($this->isOwner($user, $reclamation) || $this->isConcerned($user, $reclamation)) {
            return !$this->isClosed($reclamation);
        }

        return $this->hasAnyGroup($user, self::PROCESS_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut ajouter des pièces jointes
     */
    public function attach(?Utilisateur $user, Reclamation $reclamation): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'auteur peut ajouter des pièces jointes tant que la réclamation est ouverte
        if ($this->isOwner($user, $reclamation) && !$this->isClosed($reclamation)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::PROCESS_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur est l'auteur de la réclamation
     */
    private function isOwner(Utilisateur $user, Reclamation $reclamation): bool
    {
        return $reclamation->getAuteurId() === $user->getId();
    }

    /**
     * Vérifie si l'utilisateur est concerné par la réclamation
     */
    private function isConcerned(Utilisateur $user, Reclamation $reclamation): bool
    {
        $concerneId = $reclamation->getConcerneId();
        return $concerneId !== null && $concerneId === $user->getId();
    }

    /**
     * Vérifie si la réclamation est clôturée
     */
    private function isClosed(Reclamation $reclamation): bool
    {
        $statut = $reclamation->getStatut();
        return in_array($statut, ['cloturee', 'rejetee', 'resolue'], true);
    }

    /**
     * Vérifie si l'auteur peut modifier sa réclamation
     */
    private function canOwnerEdit(Reclamation $reclamation): bool
    {
        $statut = $reclamation->getStatut();
        return in_array($statut, ['ouverte', 'en_attente'], true);
    }

    /**
     * Vérifie si l'auteur peut supprimer sa réclamation
     */
    private function canOwnerDelete(Reclamation $reclamation): bool
    {
        $statut = $reclamation->getStatut();
        return $statut === 'ouverte';
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
