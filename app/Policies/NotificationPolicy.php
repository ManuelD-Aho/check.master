<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Notification;
use Src\Support\Auth;

/**
 * Policy Notification
 * 
 * Définit les règles d'autorisation pour la gestion des notifications.
 */
class NotificationPolicy
{
    /**
     * Groupes autorisés à gérer les notifications
     */
    private const ADMIN_GROUPS = [5]; // Admin uniquement

    /**
     * Vérifie si l'utilisateur peut voir ses notifications
     */
    public function viewOwn(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user !== null;
    }

    /**
     * Vérifie si l'utilisateur peut voir une notification spécifique
     */
    public function view(?Utilisateur $user, Notification $notification): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // L'utilisateur peut voir ses propres notifications
        if ($notification->getDestinataire() === $user->getId()) {
            return true;
        }

        // Les admins peuvent voir toutes les notifications
        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut marquer une notification comme lue
     */
    public function markAsRead(?Utilisateur $user, Notification $notification): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seul le destinataire peut marquer comme lu
        return $notification->getDestinataire() === $user->getId();
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une notification
     */
    public function delete(?Utilisateur $user, Notification $notification): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Le destinataire peut supprimer ses notifications
        if ($notification->getDestinataire() === $user->getId()) {
            return true;
        }

        // Les admins peuvent supprimer toutes les notifications
        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut envoyer une notification
     */
    public function send(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les utilisateurs autorisés peuvent envoyer des notifications
        return $this->hasAnyGroup($user, [5, 6, 7, 8, 9, 10, 11]); // Personnel administratif
    }

    /**
     * Vérifie si l'utilisateur peut envoyer des notifications en masse
     */
    public function sendBulk(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les templates de notification
     */
    public function manageTemplates(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir les statistiques de notification
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
     * Vérifie si l'utilisateur peut configurer ses préférences de notification
     */
    public function managePreferences(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        return $user !== null; // Tous les utilisateurs connectés
    }

    /**
     * Vérifie si l'utilisateur peut voir l'historique des notifications
     */
    public function viewHistory(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut relancer une notification
     */
    public function resend(?Utilisateur $user, Notification $notification): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
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
