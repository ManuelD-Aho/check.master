<?php

declare(strict_types=1);

namespace App\Services\Soutenance;

use App\Models\JuryMembre;
use App\Models\DossierEtudiant;
use App\Models\Enseignant;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use App\Services\Workflow\ServiceWorkflow;
use Src\Exceptions\JuryException;
use Src\Exceptions\NotFoundException;

/**
 * Service Jury
 * 
 * Gestion de la constitution des jurys de soutenance.
 * 5 membres requis, workflow d'invitation/acceptation.
 */
class ServiceJury
{
    private const NOMBRE_MEMBRES_REQUIS = 5;
    private const NOMBRE_MEMBRES_MINIMUM = 3;

    /**
     * Ajoute un membre au jury
     */
    public function ajouterMembre(
        int $dossierId,
        int $enseignantId,
        string $role,
        int $ajoutePar
    ): JuryMembre {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        $enseignant = Enseignant::find($enseignantId);
        if ($enseignant === null) {
            throw new NotFoundException('Enseignant non trouvé');
        }

        // Vérifier si déjà membre
        $existant = JuryMembre::firstWhere([
            'dossier_id' => $dossierId,
            'enseignant_id' => $enseignantId,
        ]);

        if ($existant !== null) {
            throw new JuryException('Cet enseignant fait déjà partie du jury');
        }

        // Vérifier le nombre de membres
        $nombreActuel = $this->compterMembres($dossierId);
        if ($nombreActuel >= self::NOMBRE_MEMBRES_REQUIS) {
            throw new JuryException('Le jury est déjà complet');
        }

        $membre = new JuryMembre([
            'dossier_id' => $dossierId,
            'enseignant_id' => $enseignantId,
            'role' => $role,
            'statut' => 'Invite',
        ]);
        $membre->save();

        ServiceAudit::logCreation('jury_membre', $membre->getId(), [
            'dossier_id' => $dossierId,
            'enseignant_id' => $enseignantId,
            'role' => $role,
        ]);

        // Envoyer l'invitation
        $this->envoyerInvitation($membre, $enseignant);

        return $membre;
    }

    /**
     * Envoie une invitation à un membre du jury
     */
    private function envoyerInvitation(JuryMembre $membre, Enseignant $enseignant): void
    {
        if ($enseignant->utilisateur_id === null) {
            return;
        }

        ServiceNotification::envoyerParCode(
            'invitation_jury',
            [(int) $enseignant->utilisateur_id],
            [
                'enseignant_nom' => $enseignant->nom_ens . ' ' . $enseignant->prenom_ens,
                'role' => $membre->role,
            ]
        );
    }

    /**
     * Accepte une invitation au jury
     */
    public function accepterInvitation(int $membreId, int $utilisateurId): bool
    {
        $membre = JuryMembre::find($membreId);
        if ($membre === null) {
            throw new NotFoundException('Membre de jury non trouvé');
        }

        $membre->statut = 'Accepte';
        $membre->repondu_le = date('Y-m-d H:i:s');
        $membre->save();

        ServiceAudit::log('acceptation_jury', 'jury_membre', $membreId);

        // Vérifier si le jury est complet
        $this->verifierJuryComplet((int) $membre->dossier_id, $utilisateurId);

        return true;
    }

    /**
     * Refuse une invitation au jury
     */
    public function refuserInvitation(int $membreId, string $motif, int $utilisateurId): bool
    {
        $membre = JuryMembre::find($membreId);
        if ($membre === null) {
            throw new NotFoundException('Membre de jury non trouvé');
        }

        $membre->statut = 'Refuse';
        $membre->motif_refus = $motif;
        $membre->repondu_le = date('Y-m-d H:i:s');
        $membre->save();

        ServiceAudit::log('refus_jury', 'jury_membre', $membreId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Vérifie si le jury est complet et avance le workflow
     */
    private function verifierJuryComplet(int $dossierId, int $utilisateurId): void
    {
        $membresAcceptes = $this->compterMembresAcceptes($dossierId);

        if ($membresAcceptes >= self::NOMBRE_MEMBRES_MINIMUM) {
            $dossier = DossierEtudiant::find($dossierId);
            if ($dossier === null) {
                return;
            }

            $etat = $dossier->getEtatActuel();
            if ($etat !== null && $etat->code_etat === 'JURY_EN_CONSTITUTION') {
                $serviceWorkflow = new ServiceWorkflow();
                try {
                    $serviceWorkflow->effectuerTransition(
                        $dossierId,
                        'SOUTENANCE_PLANIFIEE',
                        $utilisateurId,
                        'Jury constitué'
                    );
                } catch (\Exception $e) {
                    error_log('Erreur transition jury: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Compte les membres du jury
     */
    public function compterMembres(int $dossierId): int
    {
        return JuryMembre::count(['dossier_id' => $dossierId]);
    }

    /**
     * Compte les membres ayant accepté
     */
    public function compterMembresAcceptes(int $dossierId): int
    {
        return JuryMembre::count([
            'dossier_id' => $dossierId,
            'statut' => 'Accepte',
        ]);
    }

    /**
     * Retourne les membres du jury
     */
    public function getMembres(int $dossierId): array
    {
        $sql = "SELECT jm.*, e.nom_ens, e.prenom_ens, e.email_ens, e.grade
                FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                WHERE jm.dossier_id = :id
                ORDER BY jm.role";

        $stmt = \App\Orm\Model::raw($sql, ['id' => $dossierId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si le jury est complet
     */
    public function estComplet(int $dossierId): bool
    {
        return $this->compterMembresAcceptes($dossierId) >= self::NOMBRE_MEMBRES_MINIMUM;
    }

    /**
     * Retire un membre du jury
     */
    public function retirerMembre(int $membreId, int $utilisateurId): bool
    {
        $membre = JuryMembre::find($membreId);
        if ($membre === null) {
            throw new NotFoundException('Membre de jury non trouvé');
        }

        ServiceAudit::log('retrait_jury', 'jury_membre', $membreId);

        return $membre->delete();
    }
}
