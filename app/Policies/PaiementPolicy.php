<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Paiement;
use App\Models\Etudiant;
use Src\Support\Auth;

/**
 * Policy Paiement
 * 
 * Définit les règles d'autorisation pour la gestion des paiements.
 */
class PaiementPolicy
{
    /**
     * Groupes autorisés à gérer les paiements
     */
    private const ADMIN_GROUPS = [5, 8]; // Admin, Scolarité

    /**
     * Groupes autorisés à consulter les paiements
     */
    private const VIEW_GROUPS = [5, 6, 8]; // Admin, Secrétaire, Scolarité

    /**
     * Groupe étudiant
     */
    private const STUDENT_GROUP = 13;

    /**
     * Vérifie si l'utilisateur peut voir la liste des paiements
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
     * Vérifie si l'utilisateur peut voir un paiement spécifique
     */
    public function view(?Utilisateur $user, Paiement $paiement): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'étudiant peut voir ses propres paiements
        if ($this->isOwnPayment($user, $paiement)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut enregistrer un paiement
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
     * Vérifie si l'utilisateur peut modifier un paiement
     */
    public function update(?Utilisateur $user, Paiement $paiement): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas modifier un paiement validé
        if ($paiement->estValide()) {
            return $this->hasAnyGroup($user, [5]); // Seul admin
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un paiement
     */
    public function delete(?Utilisateur $user, Paiement $paiement): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas supprimer un paiement validé
        if ($paiement->estValide()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5]); // Seul admin
    }

    /**
     * Vérifie si l'utilisateur peut valider un paiement
     */
    public function validate(?Utilisateur $user, Paiement $paiement): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas valider un paiement déjà validé
        if ($paiement->estValide()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut annuler un paiement
     */
    public function cancel(?Utilisateur $user, Paiement $paiement): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut annuler qu'un paiement non validé
        if ($paiement->estValide()) {
            return $this->hasAnyGroup($user, [5]); // Seul admin avec justification
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut générer un reçu
     */
    public function generateReceipt(?Utilisateur $user, Paiement $paiement): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Le paiement doit être validé
        if (!$paiement->estValide()) {
            return false;
        }

        // L'étudiant peut générer son reçu
        if ($this->isOwnPayment($user, $paiement)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut appliquer une exonération
     */
    public function applyExoneration(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 8]); // Admin, Scolarité
    }

    /**
     * Vérifie si l'utilisateur peut appliquer une pénalité
     */
    public function applyPenalty(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 8]); // Admin, Scolarité
    }

    /**
     * Vérifie si l'utilisateur peut voir le rapport financier
     */
    public function viewFinancialReport(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 8]); // Admin, Scolarité
    }

    /**
     * Vérifie si l'utilisateur peut exporter les paiements
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
     * Vérifie si l'utilisateur peut envoyer des relances de paiement
     */
    public function sendReminder(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir les statistiques de paiement
     */
    public function viewStats(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si c'est le paiement de l'utilisateur (étudiant)
     */
    private function isOwnPayment(Utilisateur $user, Paiement $paiement): bool
    {
        if ($user->getGroupeId() !== self::STUDENT_GROUP) {
            return false;
        }

        $etudiantId = $user->getEtudiantId();
        if ($etudiantId === null) {
            return false;
        }

        return $paiement->getEtudiantId() === $etudiantId;
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
