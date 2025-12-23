<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use Src\Support\Auth;

/**
 * Policy Configuration
 * 
 * Définit les règles d'autorisation pour la gestion de la configuration système.
 */
class ConfigurationPolicy
{
    /**
     * Groupes autorisés à gérer la configuration
     */
    private const ADMIN_GROUPS = [5]; // Admin uniquement

    /**
     * Groupes autorisés à consulter certaines configurations
     */
    private const VIEW_GROUPS = [5, 9]; // Admin, Resp. Filière

    /**
     * Vérifie si l'utilisateur peut voir la configuration
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
     * Vérifie si l'utilisateur peut voir une configuration spécifique
     */
    public function view(?Utilisateur $user, string $configKey): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Certaines configurations sont publiques
        if ($this->isPublicConfig($configKey)) {
            return true;
        }

        // Les configurations sensibles requièrent des droits admin
        if ($this->isSensitiveConfig($configKey)) {
            return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut modifier la configuration
     */
    public function update(?Utilisateur $user, string $configKey): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les configurations en lecture seule ne peuvent pas être modifiées
        if ($this->isReadOnlyConfig($configKey)) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer une nouvelle configuration
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
     * Vérifie si l'utilisateur peut supprimer une configuration
     */
    public function delete(?Utilisateur $user, string $configKey): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Les configurations système ne peuvent pas être supprimées
        if ($this->isSystemConfig($configKey)) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les templates de notification
     */
    public function manageNotificationTemplates(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les workflows
     */
    public function manageWorkflows(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les paramètres de sécurité
     */
    public function manageSecuritySettings(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les paramètres email
     */
    public function manageEmailSettings(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut mettre le système en maintenance
     */
    public function setMaintenanceMode(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut voir les logs système
     */
    public function viewSystemLogs(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les paramètres SLA
     */
    public function manageSlaSettings(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut gérer les paramètres PDF
     */
    public function managePdfSettings(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut réinitialiser le cache
     */
    public function clearCache(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si une configuration est publique
     */
    private function isPublicConfig(string $key): bool
    {
        $publicConfigs = [
            'app.name',
            'app.timezone',
            'app.locale',
            'app.year_academique',
        ];

        return in_array($key, $publicConfigs, true);
    }

    /**
     * Vérifie si une configuration est sensible
     */
    private function isSensitiveConfig(string $key): bool
    {
        $sensitivePatterns = [
            'security.',
            'email.smtp_',
            'email.password',
            'api.key',
            'signature.',
        ];

        foreach ($sensitivePatterns as $pattern) {
            if (str_starts_with($key, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si une configuration est en lecture seule
     */
    private function isReadOnlyConfig(string $key): bool
    {
        $readOnlyConfigs = [
            'app.version',
            'app.installed_at',
            'system.migration_version',
        ];

        return in_array($key, $readOnlyConfigs, true);
    }

    /**
     * Vérifie si une configuration est système (non supprimable)
     */
    private function isSystemConfig(string $key): bool
    {
        $systemPatterns = [
            'app.',
            'system.',
            'workflow.',
            'security.',
        ];

        foreach ($systemPatterns as $pattern) {
            if (str_starts_with($key, $pattern)) {
                return true;
            }
        }

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
