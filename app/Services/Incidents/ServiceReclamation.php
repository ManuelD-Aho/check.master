<?php

declare(strict_types=1);

namespace App\Services\Incidents;

use App\Models\Reclamation;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\NotFoundException;

/**
 * Service Réclamation
 * 
 * Gestion des réclamations étudiantes.
 */
class ServiceReclamation
{
    /**
     * Statuts des réclamations
     */
    public const STATUT_SOUMISE = 'Soumise';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_RESOLUE = 'Resolue';
    public const STATUT_REJETEE = 'Rejetee';

    /**
     * Types de réclamations
     */
    public const TYPE_NOTE = 'note';
    public const TYPE_PAIEMENT = 'paiement';
    public const TYPE_ADMINISTRATIF = 'administratif';
    public const TYPE_TECHNIQUE = 'technique';
    public const TYPE_AUTRE = 'autre';

    /**
     * Soumet une nouvelle réclamation
     */
    public static function soumettre(
        int $etudiantId,
        string $type,
        string $sujet,
        string $description,
        ?int $entiteId = null
    ): Reclamation {
        $reclamation = new Reclamation([
            'etudiant_id' => $etudiantId,
            'type_reclamation' => $type,
            'sujet' => $sujet,
            'description' => $description,
            'entite_concernee_id' => $entiteId,
            'statut' => self::STATUT_SOUMISE,
        ]);
        $reclamation->save();

        ServiceAudit::logCreation('reclamation', $reclamation->getId(), [
            'etudiant_id' => $etudiantId,
            'type' => $type,
            'sujet' => $sujet,
        ]);

        return $reclamation;
    }

    /**
     * Prend en charge une réclamation
     */
    public static function prendreEnCharge(int $reclamationId, int $utilisateurId): bool
    {
        $reclamation = Reclamation::find($reclamationId);
        if ($reclamation === null) {
            throw new NotFoundException('Réclamation non trouvée');
        }

        $reclamation->statut = self::STATUT_EN_COURS;
        $reclamation->prise_en_charge_par = $utilisateurId;
        $reclamation->prise_en_charge_le = date('Y-m-d H:i:s');
        $reclamation->save();

        ServiceAudit::log('prise_en_charge_reclamation', 'reclamation', $reclamationId);

        return true;
    }

    /**
     * Résoud une réclamation
     */
    public static function resoudre(
        int $reclamationId,
        string $resolution,
        int $resoluepar
    ): bool {
        $reclamation = Reclamation::find($reclamationId);
        if ($reclamation === null) {
            throw new NotFoundException('Réclamation non trouvée');
        }

        $reclamation->statut = self::STATUT_RESOLUE;
        $reclamation->resolution = $resolution;
        $reclamation->resolue_par = $resoluepar;
        $reclamation->resolue_le = date('Y-m-d H:i:s');
        $reclamation->save();

        ServiceAudit::log('resolution_reclamation', 'reclamation', $reclamationId, [
            'resolution' => $resolution,
        ]);

        // Notifier l'étudiant
        self::notifierEtudiant($reclamation, 'resolution');

        return true;
    }

    /**
     * Rejette une réclamation
     */
    public static function rejeter(
        int $reclamationId,
        string $motif,
        int $rejetePar
    ): bool {
        $reclamation = Reclamation::find($reclamationId);
        if ($reclamation === null) {
            throw new NotFoundException('Réclamation non trouvée');
        }

        $reclamation->statut = self::STATUT_REJETEE;
        $reclamation->motif_rejet = $motif;
        $reclamation->resolue_par = $rejetePar;
        $reclamation->resolue_le = date('Y-m-d H:i:s');
        $reclamation->save();

        ServiceAudit::log('rejet_reclamation', 'reclamation', $reclamationId, [
            'motif' => $motif,
        ]);

        // Notifier l'étudiant
        self::notifierEtudiant($reclamation, 'rejet');

        return true;
    }

    /**
     * Notifie l'étudiant du statut de sa réclamation
     */
    private static function notifierEtudiant(Reclamation $reclamation, string $typeNotification): void
    {
        $sql = "SELECT e.utilisateur_id FROM etudiants e 
                WHERE e.id_etudiant = :id";

        $stmt = \App\Orm\Model::raw($sql, ['id' => $reclamation->etudiant_id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result && $result['utilisateur_id']) {
            $template = $typeNotification === 'resolution' 
                ? 'reclamation_resolue' 
                : 'reclamation_rejetee';

            ServiceNotification::envoyerParCode(
                $template,
                [(int) $result['utilisateur_id']],
                [
                    'sujet' => $reclamation->sujet,
                    'statut' => $reclamation->statut,
                ]
            );
        }
    }

    /**
     * Retourne les réclamations d'un étudiant
     */
    public static function getReclamationsEtudiant(int $etudiantId): array
    {
        return Reclamation::where(['etudiant_id' => $etudiantId]);
    }

    /**
     * Retourne les réclamations en attente
     */
    public static function getReclamationsEnAttente(): array
    {
        return Reclamation::where(['statut' => self::STATUT_SOUMISE]);
    }

    /**
     * Retourne les réclamations en cours de traitement
     */
    public static function getReclamationsEnCours(): array
    {
        return Reclamation::where(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Retourne les statistiques des réclamations
     */
    public static function getStatistiques(): array
    {
        $sql = "SELECT 
                    statut, 
                    type_reclamation,
                    COUNT(*) as total
                FROM reclamations
                GROUP BY statut, type_reclamation";

        $stmt = \App\Orm\Model::raw($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
