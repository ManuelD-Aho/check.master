<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Document;
use Src\Support\Auth;

/**
 * Policy Document
 * 
 * Définit les règles d'autorisation pour la gestion des documents.
 */
class DocumentPolicy
{
    /**
     * Groupes autorisés à gérer les documents
     */
    private const ADMIN_GROUPS = [5, 6]; // Admin, Secrétaire

    /**
     * Groupes autorisés à consulter les documents
     */
    private const VIEW_GROUPS = [5, 6, 7, 8, 9, 10, 11, 12]; // Tous sauf étudiants

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Vérifie si l'utilisateur peut voir la liste des documents
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
     * Vérifie si l'utilisateur peut voir un document spécifique
     */
    public function view(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir ses propres documents
        if ($this->isOwnDocument($user, $document)) {
            return true;
        }

        // Les encadreurs peuvent voir les documents de leurs étudiants
        if ($this->isEncadreurOfDocument($user, $document)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut uploader un document
     */
    public function upload(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Tous les utilisateurs peuvent uploader
        return true;
    }

    /**
     * Vérifie si l'utilisateur peut modifier un document
     */
    public function update(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Le propriétaire peut modifier si le document n'est pas verrouillé
        if ($this->isOwnDocument($user, $document) && !$document->estVerrouille()) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un document
     */
    public function delete(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas supprimer un document verrouillé
        if ($document->estVerrouille()) {
            return $this->hasAnyGroup($user, [5]); // Seul admin
        }

        // Le propriétaire peut supprimer
        if ($this->isOwnDocument($user, $document)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut télécharger un document
     */
    public function download(?Utilisateur $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    /**
     * Vérifie si l'utilisateur peut verrouiller un document
     */
    public function lock(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Déjà verrouillé
        if ($document->estVerrouille()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut déverrouiller un document
     */
    public function unlock(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Pas verrouillé
        if (!$document->estVerrouille()) {
            return false;
        }

        // Seuls les admins peuvent déverrouiller
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut archiver un document
     */
    public function archive(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut vérifier l'intégrité d'un document
     */
    public function verifyIntegrity(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut générer un document PDF
     */
    public function generate(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 6, 8]); // Admin, Secrétaire, Scolarité
    }

    /**
     * Vérifie si l'utilisateur peut signer un document
     */
    public function sign(?Utilisateur $user, Document $document): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Le document doit être signable
        if (!$document->estSignable()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 9, 11]); // Admin, Resp. Filière, Commission
    }

    /**
     * Vérifie si l'utilisateur peut voir l'historique d'un document
     */
    public function viewHistory(?Utilisateur $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    /**
     * Vérifie si c'est le document de l'utilisateur
     */
    private function isOwnDocument(Utilisateur $user, Document $document): bool
    {
        // Vérifier si l'utilisateur est le propriétaire
        return $document->getProprietaireId() === $user->getId();
    }

    /**
     * Vérifie si l'utilisateur est encadreur du propriétaire du document
     */
    private function isEncadreurOfDocument(Utilisateur $user, Document $document): bool
    {
        $enseignantId = $user->getEnseignantId();
        if ($enseignantId === null) {
            return false;
        }

        // Vérifier si le document appartient à un étudiant encadré par cet enseignant
        $etudiantId = $document->getEtudiantId();
        if ($etudiantId === null) {
            return false;
        }

        // Logique de vérification de l'encadrement (simplifiée)
        // Dans une implémentation complète, vérifier via les dossiers étudiants
        return false;
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
