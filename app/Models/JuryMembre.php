<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle JuryMembre
 * 
 * Représente un membre du jury pour une soutenance.
 * Table: jury_membres
 */
class JuryMembre extends Model
{
    protected string $table = 'jury_membres';
    protected string $primaryKey = 'id_jury_membre';
    protected array $fillable = [
        'dossier_id',
        'enseignant_id',
        'role_jury',
        'statut_acceptation',
        'date_invitation',
        'date_reponse',
    ];

    /**
     * Rôles dans le jury
     */
    public const ROLE_PRESIDENT = 'President';
    public const ROLE_RAPPORTEUR = 'Rapporteur';
    public const ROLE_EXAMINATEUR = 'Examinateur';
    public const ROLE_ENCADREUR = 'Encadreur';

    /**
     * Statuts d'acceptation
     */
    public const STATUT_INVITE = 'Invite';
    public const STATUT_ACCEPTE = 'Accepte';
    public const STATUT_REFUSE = 'Refuse';

    /**
     * Retourne le dossier
     */
    public function getDossier(): ?DossierEtudiant
    {
        if ($this->dossier_id === null) {
            return null;
        }
        return DossierEtudiant::find((int) $this->dossier_id);
    }

    /**
     * Retourne l'enseignant
     */
    public function getEnseignant(): ?Enseignant
    {
        if ($this->enseignant_id === null) {
            return null;
        }
        return Enseignant::find((int) $this->enseignant_id);
    }

    /**
     * Accepte l'invitation
     */
    public function accepter(): void
    {
        $this->statut_acceptation = self::STATUT_ACCEPTE;
        $this->date_reponse = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Refuse l'invitation
     */
    public function refuser(): void
    {
        $this->statut_acceptation = self::STATUT_REFUSE;
        $this->date_reponse = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Invite un enseignant au jury
     */
    public static function inviter(
        int $dossierId,
        int $enseignantId,
        string $role
    ): self {
        $membre = new self([
            'dossier_id' => $dossierId,
            'enseignant_id' => $enseignantId,
            'role_jury' => $role,
            'statut_acceptation' => self::STATUT_INVITE,
            'date_invitation' => date('Y-m-d H:i:s'),
        ]);
        $membre->save();
        return $membre;
    }

    /**
     * Retourne les membres du jury d'un dossier
     *
     * @return self[]
     */
    public static function pourDossier(int $dossierId): array
    {
        return self::where(['dossier_id' => $dossierId]);
    }

    /**
     * Vérifie si le jury est complet (tous ont accepté)
     */
    public static function juryComplet(int $dossierId): bool
    {
        $membres = self::pourDossier($dossierId);

        if (count($membres) < 3) { // Minimum 3 membres
            return false;
        }

        foreach ($membres as $membre) {
            if ($membre->statut_acceptation !== self::STATUT_ACCEPTE) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne le président du jury
     */
    public static function president(int $dossierId): ?self
    {
        return self::firstWhere([
            'dossier_id' => $dossierId,
            'role_jury' => self::ROLE_PRESIDENT,
        ]);
    }

    /**
     * Retourne les invitations en attente d'un enseignant
     *
     * @return self[]
     */
    public static function invitationsEnAttente(int $enseignantId): array
    {
        return self::where([
            'enseignant_id' => $enseignantId,
            'statut_acceptation' => self::STATUT_INVITE,
        ]);
    }
}
