<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Archive;
use Src\Support\Auth;

/**
 * Policy Archive
 * 
 * Définit les règles d'autorisation pour la gestion des archives.
 */
class ArchivePolicy
{
    /**
     * Groupes autorisés à gérer les archives
     */
    private const ADMIN_GROUPS = [5]; // Admin

    /**
     * Groupes autorisés à consulter les archives
     */
    private const VIEW_GROUPS = [5, 6, 8, 9]; // Admin, Secrétaire, Scolarité, Resp. Filière

    /**
     * Groupes autorisés à créer des archives
     */
    private const CREATE_GROUPS = [5, 6, 8]; // Admin, Secrétaire, Scolarité

    /**
     * Vérifie si l'utilisateur peut voir la liste des archives
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
     * Vérifie si l'utilisateur peut voir une archive spécifique
     */
    public function view(?Utilisateur $user, Archive $archive): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à l'entité archivée
        if ($this->hasAccessToArchivedEntity($user, $archive)) {
            return true;
        }

        return $this->hasAnyGroup($user, self::VIEW_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut créer une archive
     */
    public function create(?Utilisateur $user = null): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::CREATE_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer une archive
     * Note: Les archives ne doivent généralement pas être supprimées
     */
    public function delete(?Utilisateur $user, Archive $archive): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        // Seuls les super admins peuvent supprimer des archives
        return $this->hasAnyGroup($user, [5]) && $this->isSuperAdmin($user);
    }

    /**
     * Vérifie si l'utilisateur peut télécharger une archive
     */
    public function download(?Utilisateur $user, Archive $archive): bool
    {
        return $this->view($user, $archive);
    }

    /**
     * Vérifie si l'utilisateur peut vérifier l'intégrité d'une archive
     */
    public function verifyIntegrity(?Utilisateur $user, Archive $archive): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur peut exporter les archives
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
     * Vérifie si l'utilisateur peut restaurer une archive
     */
    public function restore(?Utilisateur $user, Archive $archive): bool
    {
        $user = $user ?? Auth::user();
        if ($user === null) {
            return false;
        }

        return $this->hasAnyGroup($user, self::ADMIN_GROUPS);
    }

    /**
     * Vérifie si l'utilisateur a accès à l'entité archivée
     */
    private function hasAccessToArchivedEntity(Utilisateur $user, Archive $archive): bool
    {
        $entiteType = $archive->getEntiteType();
        $entiteId = $archive->getEntiteId();

        // Logique selon le type d'entité archivée
        return match ($entiteType) {
            'dossier' => $this->hasAccessToDossier($user, $entiteId),
            'paiement' => $this->hasAccessToPaiement($user, $entiteId),
            'soutenance' => $this->hasAccessToSoutenance($user, $entiteId),
            default => false,
        };
    }

    /**
     * Vérifie l'accès à un dossier
     */
    private function hasAccessToDossier(Utilisateur $user, int $dossierId): bool
    {
        // L'étudiant peut voir les archives de son propre dossier
        if ($user->getGroupeId() === 13) {
            $etudiant = $user->getEtudiant();
            if ($etudiant !== null) {
                $dossier = $etudiant->getDossier();
                return $dossier !== null && $dossier->getId() === $dossierId;
            }
        }

        return false;
    }

    /**
     * Vérifie l'accès à un paiement
     */
    private function hasAccessToPaiement(Utilisateur $user, int $paiementId): bool
    {
        // L'étudiant peut voir les archives de ses propres paiements
        if ($user->getGroupeId() === 13) {
            // Logique de vérification si le paiement appartient à l'étudiant
            return false;
        }

        return false;
    }

    /**
     * Vérifie l'accès à une soutenance
     */
    private function hasAccessToSoutenance(Utilisateur $user, int $soutenanceId): bool
    {
        // Les enseignants membres du jury peuvent voir les archives
        if ($user->getGroupeId() === 12) {
            // Logique de vérification si l'enseignant est membre du jury
            return false;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur est super admin
     */
    private function isSuperAdmin(Utilisateur $user): bool
    {
        return $user->getGroupeId() === 5 && $user->isSuperAdmin();
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
