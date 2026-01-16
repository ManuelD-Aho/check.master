<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Exoneration;
use App\Models\Etudiant;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;
use App\Orm\Model;

/**
 * Service Exonération
 * 
 * Gestion des exonérations totales ou partielles.
 * Approbation par administrateur, impact sur le solde.
 * 
 * @see PRD 07 - Financier
 */
class ServiceExoneration
{
    /**
     * Types d'exonération
     */
    public const TYPE_MONTANT = 'Montant';
    public const TYPE_POURCENTAGE = 'Pourcentage';

    /**
     * Statuts d'exonération
     */
    public const STATUT_EN_ATTENTE = 'En_attente';
    public const STATUT_APPROUVEE = 'Approuvee';
    public const STATUT_REFUSEE = 'Refusee';

    /**
     * Motifs prédéfinis d'exonération
     */
    public const MOTIF_EXCELLENCE = 'Excellence académique';
    public const MOTIF_SOCIAL = 'Situation sociale';
    public const MOTIF_PERSONNEL = 'Personnel de l\'institution';
    public const MOTIF_PARTENARIAT = 'Partenariat/Convention';
    public const MOTIF_AUTRE = 'Autre';

    /**
     * Crée une demande d'exonération
     */
    public function creer(array $donnees, int $creePar): Exoneration
    {
        // Valider les données
        $this->validerDonnees($donnees);

        $etudiant = Etudiant::find($donnees['etudiant_id']);
        if ($etudiant === null) {
            throw new NotFoundException('Étudiant non trouvé');
        }

        // Vérifier qu'une exonération n'existe pas déjà pour cette année
        $existante = Exoneration::firstWhere([
            'etudiant_id' => $donnees['etudiant_id'],
            'annee_acad_id' => $donnees['annee_acad_id'],
            'statut' => self::STATUT_APPROUVEE,
        ]);

        if ($existante !== null) {
            throw new ValidationException('Une exonération approuvée existe déjà pour cette année');
        }

        // Calculer le montant si pourcentage
        $montant = 0;
        if ($donnees['type'] === self::TYPE_POURCENTAGE) {
            $montant = $this->calculerMontantPourcentage(
                (int) $donnees['annee_acad_id'],
                (float) $donnees['pourcentage']
            );
        } else {
            $montant = (float) $donnees['montant'];
        }

        $exoneration = new Exoneration([
            'etudiant_id' => $donnees['etudiant_id'],
            'annee_acad_id' => $donnees['annee_acad_id'],
            'type' => $donnees['type'],
            'montant' => $montant,
            'pourcentage' => $donnees['pourcentage'] ?? null,
            'motif' => $donnees['motif'],
            'justificatif' => $donnees['justificatif'] ?? null,
            'statut' => self::STATUT_EN_ATTENTE,
            'demandee_par' => $creePar,
            'demandee_le' => date('Y-m-d H:i:s'),
        ]);
        $exoneration->save();

        ServiceAudit::logCreation('exoneration', $exoneration->getId(), [
            'etudiant_id' => $donnees['etudiant_id'],
            'montant' => $montant,
            'type' => $donnees['type'],
        ]);

        return $exoneration;
    }

    /**
     * Valide les données d'exonération
     */
    private function validerDonnees(array $donnees): void
    {
        if (empty($donnees['etudiant_id'])) {
            throw new ValidationException('L\'identifiant étudiant est requis');
        }

        if (empty($donnees['annee_acad_id'])) {
            throw new ValidationException('L\'année académique est requise');
        }

        if (empty($donnees['type'])) {
            throw new ValidationException('Le type d\'exonération est requis');
        }

        if ($donnees['type'] === self::TYPE_POURCENTAGE) {
            if (empty($donnees['pourcentage']) || (float) $donnees['pourcentage'] <= 0 || (float) $donnees['pourcentage'] > 100) {
                throw new ValidationException('Le pourcentage doit être entre 1 et 100');
            }
        } else {
            if (empty($donnees['montant']) || (float) $donnees['montant'] <= 0) {
                throw new ValidationException('Le montant doit être supérieur à 0');
            }
        }

        if (empty($donnees['motif'])) {
            throw new ValidationException('Le motif est requis');
        }
    }

    /**
     * Calcule le montant à partir d'un pourcentage
     */
    private function calculerMontantPourcentage(int $anneeAcadId, float $pourcentage): float
    {
        $sql = "SELECT COALESCE(montant, 0) as montant FROM frais_scolarite WHERE annee_acad_id = :annee";
        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $montantTotal = (float) ($result['montant'] ?? 0);

        return round($montantTotal * $pourcentage / 100, 2);
    }

    /**
     * Approuve une exonération
     */
    public function approuver(int $exonerationId, int $approuvePar, ?string $commentaire = null): Exoneration
    {
        $exoneration = Exoneration::find($exonerationId);
        if ($exoneration === null) {
            throw new NotFoundException('Exonération non trouvée');
        }

        if ($exoneration->statut !== self::STATUT_EN_ATTENTE) {
            throw new ValidationException('Cette exonération a déjà été traitée');
        }

        $exoneration->statut = self::STATUT_APPROUVEE;
        $exoneration->approuvee_par = $approuvePar;
        $exoneration->approuvee_le = date('Y-m-d H:i:s');
        $exoneration->commentaire_decision = $commentaire;
        $exoneration->save();

        ServiceAudit::log('approbation_exoneration', 'exoneration', $exonerationId, [
            'approuve_par' => $approuvePar,
        ]);

        // Notifier l'étudiant
        $this->notifierDecision($exoneration, true);

        return $exoneration;
    }

    /**
     * Refuse une exonération
     */
    public function refuser(int $exonerationId, string $motifRefus, int $refusePar): Exoneration
    {
        $exoneration = Exoneration::find($exonerationId);
        if ($exoneration === null) {
            throw new NotFoundException('Exonération non trouvée');
        }

        if ($exoneration->statut !== self::STATUT_EN_ATTENTE) {
            throw new ValidationException('Cette exonération a déjà été traitée');
        }

        $exoneration->statut = self::STATUT_REFUSEE;
        $exoneration->refusee_par = $refusePar;
        $exoneration->refusee_le = date('Y-m-d H:i:s');
        $exoneration->motif_refus = $motifRefus;
        $exoneration->save();

        ServiceAudit::log('refus_exoneration', 'exoneration', $exonerationId, [
            'motif' => $motifRefus,
        ]);

        // Notifier l'étudiant
        $this->notifierDecision($exoneration, false);

        return $exoneration;
    }

    /**
     * Notifie l'étudiant de la décision
     */
    private function notifierDecision(Exoneration $exoneration, bool $approuvee): void
    {
        $etudiant = Etudiant::find((int) $exoneration->etudiant_id);
        if ($etudiant === null || $etudiant->utilisateur_id === null) {
            return;
        }

        $templateCode = $approuvee ? 'exoneration_approuvee' : 'exoneration_refusee';

        ServiceNotification::envoyerParCode(
            $templateCode,
            [(int) $etudiant->utilisateur_id],
            [
                'etudiant_nom' => $etudiant->nom_etu . ' ' . $etudiant->prenom_etu,
                'montant' => number_format((float) $exoneration->montant, 0, ',', ' '),
                'motif' => $exoneration->motif,
                'date' => date('d/m/Y'),
            ]
        );
    }

    /**
     * Retourne le total des exonérations d'un étudiant
     */
    public function getTotalExonerations(int $etudiantId, int $anneeAcadId): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) as total
                FROM exonerations 
                WHERE etudiant_id = :etudiant 
                AND annee_acad_id = :annee 
                AND statut = :statut";

        $stmt = Model::raw($sql, [
            'etudiant' => $etudiantId,
            'annee' => $anneeAcadId,
            'statut' => self::STATUT_APPROUVEE,
        ]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Retourne les exonérations d'un étudiant
     */
    public function getExonerations(int $etudiantId, ?int $anneeAcadId = null): array
    {
        $sql = "SELECT * FROM exonerations WHERE etudiant_id = :etudiant";
        $params = ['etudiant' => $etudiantId];

        if ($anneeAcadId !== null) {
            $sql .= " AND annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $sql .= " ORDER BY demandee_le DESC";

        $stmt = Model::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les exonérations en attente
     */
    public function getEnAttente(): array
    {
        $sql = "SELECT ex.*, e.nom_etu, e.prenom_etu, e.numero_carte,
                       aa.libelle_annee_acad
                FROM exonerations ex
                INNER JOIN etudiants e ON e.id_etudiant = ex.etudiant_id
                LEFT JOIN annees_academiques aa ON aa.id_annee_acad = ex.annee_acad_id
                WHERE ex.statut = :statut
                ORDER BY ex.demandee_le ASC";

        $stmt = Model::raw($sql, ['statut' => self::STATUT_EN_ATTENTE]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Annule une exonération approuvée
     */
    public function annuler(int $exonerationId, string $motif, int $annulePar): bool
    {
        $exoneration = Exoneration::find($exonerationId);
        if ($exoneration === null) {
            throw new NotFoundException('Exonération non trouvée');
        }

        if ($exoneration->statut !== self::STATUT_APPROUVEE) {
            throw new ValidationException('Seules les exonérations approuvées peuvent être annulées');
        }

        $exoneration->statut = 'Annulee';
        $exoneration->motif_annulation = $motif;
        $exoneration->annulee_par = $annulePar;
        $exoneration->annulee_le = date('Y-m-d H:i:s');
        $exoneration->save();

        ServiceAudit::log('annulation_exoneration', 'exoneration', $exonerationId, [
            'motif' => $motif,
        ]);

        return true;
    }

    /**
     * Retourne les statistiques des exonérations
     */
    public function getStatistiques(int $anneeAcadId): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_demandes,
                    SUM(CASE WHEN statut = 'Approuvee' THEN montant ELSE 0 END) as montant_total_approuve,
                    COUNT(CASE WHEN statut = 'Approuvee' THEN 1 END) as demandes_approuvees,
                    COUNT(CASE WHEN statut = 'Refusee' THEN 1 END) as demandes_refusees,
                    COUNT(CASE WHEN statut = 'En_attente' THEN 1 END) as demandes_en_attente
                FROM exonerations 
                WHERE annee_acad_id = :annee";

        $stmt = Model::raw($sql, ['annee' => $anneeAcadId]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        // Répartition par motif
        $sql2 = "SELECT motif, COUNT(*) as nombre, SUM(montant) as total
                 FROM exonerations 
                 WHERE annee_acad_id = :annee AND statut = 'Approuvee'
                 GROUP BY motif";

        $stmt2 = Model::raw($sql2, ['annee' => $anneeAcadId]);
        $parMotif = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

        return array_merge($stats, ['par_motif' => $parMotif]);
    }

    /**
     * Attache un justificatif à une exonération
     */
    public function attacherJustificatif(int $exonerationId, string $cheminFichier): bool
    {
        $exoneration = Exoneration::find($exonerationId);
        if ($exoneration === null) {
            throw new NotFoundException('Exonération non trouvée');
        }

        $exoneration->justificatif = $cheminFichier;
        $exoneration->save();

        ServiceAudit::log('ajout_justificatif', 'exoneration', $exonerationId, [
            'fichier' => $cheminFichier,
        ]);

        return true;
    }
}
