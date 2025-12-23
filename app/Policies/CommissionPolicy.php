<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\SessionCommission;
use App\Models\Vote;
use Src\Support\Auth;

/**
 * Policy Commission
 * 
 * Définit les règles d'autorisation pour la gestion des commissions et votes.
 */
class CommissionPolicy
{
    /**
     * Groupes autorisés à gérer les sessions de commission
     */
    private const ADMIN_GROUPS = [5, 9]; // Admin, Resp. Filière

    /**
     * Groupe commission
     */
    private const COMMISSION_GROUP = 11;

    /**
     * Groupes autorisés à consulter les commissions
     */
    private const VIEW_GROUPS = [5, 9, 10, 11]; // Admin, Resp. Filière, Resp. Niveau, Commission

    /**
     * Vérifie si l'utilisateur peut voir la liste des sessions de commission
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
     * Vérifie si l'utilisateur peut voir une session spécifique
     */
    public function view(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les membres de la commission peuvent voir leurs sessions
        if ($this->isCommissionMember($user) && $session->aMembreCommission($user->getId())) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer une session de commission
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
     * Vérifie si l'utilisateur peut modifier une session de commission
     */
    public function update(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut plus modifier si session terminée
        if ($session->estTerminee()) {
            return $this->hasAnyGroup($user, [5]); // Seul admin
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une session de commission
     */
    public function delete(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Ne peut pas supprimer si des votes ont été enregistrés
        if ($session->aDesVotes()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5]); // Seul admin
    }

    /**
     * Vérifie si l'utilisateur peut voter
     */
    public function vote(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être membre de la commission
        if (!$this->isCommissionMember($user)) {
            return false;
        }

        // Doit être membre de cette session
        if (!$session->aMembreCommission($user->getId())) {
            return false;
        }

        // La session doit être en cours
        if (!$session->estEnCours()) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur peut voir les votes
     */
    public function viewVotes(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les membres peuvent voir après avoir voté
        if ($this->isCommissionMember($user) && $session->aVote($user->getId())) {
            return true;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut démarrer un nouveau tour de vote
     */
    public function startVotingRound(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être admin ou resp. filière
        if (!$this->hasAnyGroup($user, self::ADMIN_GROUPS)) {
            return false;
        }

        // Vérifier le nombre max de tours (3)
        if ($session->getTourActuel() >= 3) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur peut clôturer une session
     */
    public function closeSession(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit être admin ou resp. filière
        if (!$this->hasAnyGroup($user, self::ADMIN_GROUPS)) {
            return false;
        }

        // La session doit être en cours
        return $session->estEnCours();
    }

    /**
     * Vérifie si l'utilisateur peut escalader au doyen
     */
    public function escalateToDean(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Doit avoir épuisé les 3 tours
        if ($session->getTourActuel() < 3) {
            return false;
        }

        // Doit avoir une divergence
        if ($session->aUnanimite()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut prendre la décision arbitrale (doyen)
     */
    public function makeArbitralDecision(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // La session doit être en escalade
        if (!$session->estEnEscalade()) {
            return false;
        }

        // Doit être admin (représentant du doyen dans le système)
        return $this->hasAnyGroup($user, [5]);
    }

    /**
     * Vérifie si l'utilisateur peut voir le PV de la commission
     */
    public function viewPV(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // La session doit être terminée
        if (!$session->estTerminee()) {
            return false;
        }

        // Les membres peuvent voir le PV de leurs sessions
        if ($this->isCommissionMember($user) && $session->aMembreCommission($user->getId())) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut générer le PV
     */
    public function generatePV(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // La session doit être terminée
        if (!$session->estTerminee()) {
            return false;
        }

        return $this->hasAnyGroup($user, [5, 6]); // Admin, Secrétaire
    }

    /**
     * Vérifie si l'utilisateur peut ajouter un rapport à la session
     */
    public function addRapport(?Utilisateur $user, SessionCommission $session): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // La session doit être en cours
        if (!$session->estEnCours()) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir les statistiques des commissions
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
     * Vérifie si l'utilisateur est membre de la commission
     */
    private function isCommissionMember(Utilisateur $user): bool
    {
        return $user->getGroupeId() === self::COMMISSION_GROUP;
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
