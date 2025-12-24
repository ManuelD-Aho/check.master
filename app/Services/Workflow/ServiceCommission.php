<?php

declare(strict_types=1);

namespace App\Services\Workflow;

use App\Models\CommissionSession;
use App\Models\CommissionVote;
use App\Models\RapportEtudiant;
use App\Models\DossierEtudiant;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\CommissionException;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Commission
 * 
 * Gère les sessions de commission et le système de vote à 3 tours
 * avec exigence d'unanimité et escalade vers le Doyen.
 * 
 * @see PRD Section Commission
 */
class ServiceCommission
{
    private const NOMBRE_MEMBRES_MINIMUM = 3;
    private const MAX_TOURS = 3;

    /**
     * Crée une nouvelle session de commission
     */
    public function creerSession(
        string $dateSession,
        string $lieu,
        int $creePar
    ): CommissionSession {
        $session = new CommissionSession([
            'date_session' => $dateSession,
            'lieu' => $lieu,
            'statut' => CommissionSession::STATUT_PLANIFIEE,
            'tour_vote' => 1,
            'pv_genere' => false,
        ]);
        $session->save();

        ServiceAudit::logCreation('session_commission', $session->getId(), [
            'date_session' => $dateSession,
            'lieu' => $lieu,
        ]);

        return $session;
    }

    /**
     * Démarre une session de commission
     */
    public function demarrerSession(int $sessionId, int $utilisateurId): bool
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        if (!$session->estPlanifiee()) {
            throw new CommissionException('La session n\'est pas dans un état permettant le démarrage');
        }

        $session->demarrer();

        ServiceAudit::log('demarrage_session', 'session_commission', $sessionId);

        return true;
    }

    /**
     * Enregistre un vote de membre de commission
     */
    public function voter(
        int $sessionId,
        int $rapportId,
        int $membreId,
        string $decision,
        ?string $commentaire = null
    ): CommissionVote {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        if (!$session->peutVoter()) {
            throw new CommissionException('Les votes ne sont pas autorisés pour cette session');
        }

        // Vérifier si déjà voté
        if (CommissionVote::aDejaVote($sessionId, $rapportId, $membreId, $session->tour_vote)) {
            throw new CommissionException('Ce membre a déjà voté pour ce rapport à ce tour');
        }

        // Enregistrer le vote
        $vote = CommissionVote::voter(
            $sessionId,
            $rapportId,
            $membreId,
            (int) $session->tour_vote,
            $decision,
            $commentaire
        );

        ServiceAudit::log('vote_commission', 'rapport', $rapportId, [
            'session_id' => $sessionId,
            'membre_id' => $membreId,
            'tour' => $session->tour_vote,
            'decision' => $decision,
        ]);

        return $vote;
    }

    /**
     * Vérifie et traite les résultats de vote pour un rapport
     */
    public function traiterResultatsVote(
        int $sessionId,
        int $rapportId,
        int $nombreMembres
    ): array {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        $tour = (int) $session->tour_vote;
        $nombreVotes = CommissionVote::nombreVotes($sessionId, $rapportId, $tour);

        // Vérifier si tous les membres ont voté
        if ($nombreVotes < $nombreMembres) {
            return [
                'complet' => false,
                'votes_recus' => $nombreVotes,
                'votes_attendus' => $nombreMembres,
            ];
        }

        // Vérifier l'unanimité
        $unanimite = CommissionVote::unanimiteAtteinte($sessionId, $rapportId, $tour, $nombreMembres);

        if ($unanimite !== null) {
            // Unanimité atteinte - appliquer la décision
            $this->appliquerDecision($rapportId, $unanimite);

            return [
                'complet' => true,
                'unanimite' => true,
                'decision' => $unanimite,
                'tour' => $tour,
            ];
        }

        // Pas d'unanimité
        if ($tour >= self::MAX_TOURS) {
            // Escalade au Doyen
            $this->escaladerAuDoyen($sessionId, $rapportId);

            return [
                'complet' => true,
                'unanimite' => false,
                'escalade' => true,
                'tour' => $tour,
            ];
        }

        // Passer au tour suivant
        return [
            'complet' => true,
            'unanimite' => false,
            'tour_suivant_requis' => true,
            'tour' => $tour,
        ];
    }

    /**
     * Passe au tour de vote suivant pour une session
     */
    public function passerAuTourSuivant(int $sessionId): bool
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        if (!$session->estEnCours()) {
            throw new CommissionException('La session n\'est pas en cours');
        }

        $result = $session->passerAuTourSuivant();

        if ($result) {
            ServiceAudit::log('nouveau_tour_vote', 'session_commission', $sessionId, [
                'tour' => $session->tour_vote,
            ]);
        }

        return $result;
    }

    /**
     * Applique une décision de la commission sur un rapport
     */
    private function appliquerDecision(int $rapportId, string $decision): void
    {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            return;
        }

        $nouveauStatut = match ($decision) {
            CommissionVote::DECISION_VALIDER => 'Valide',
            CommissionVote::DECISION_A_REVOIR => 'A_revoir',
            CommissionVote::DECISION_REJETER => 'Rejete',
            default => null,
        };

        if ($nouveauStatut !== null) {
            $rapport->statut = $nouveauStatut;
            $rapport->save();

            // Si validé, faire avancer le workflow du dossier
            if ($nouveauStatut === 'Valide') {
                $this->avancerWorkflowApresValidation($rapport);
            }
        }
    }

    /**
     * Fait avancer le workflow après validation du rapport
     */
    private function avancerWorkflowApresValidation(RapportEtudiant $rapport): void
    {
        $dossierId = $rapport->dossier_id;
        if ($dossierId === null) {
            return;
        }

        $serviceWorkflow = new ServiceWorkflow();
        try {
            $serviceWorkflow->effectuerTransition(
                (int) $dossierId,
                'RAPPORT_VALIDE',
                0, // Système
                'Rapport validé par la commission'
            );
        } catch (\Exception $e) {
            // Log mais ne pas bloquer
            error_log('Erreur avancement workflow: ' . $e->getMessage());
        }
    }

    /**
     * Escalade au Doyen après 3 tours sans unanimité
     */
    private function escaladerAuDoyen(int $sessionId, int $rapportId): void
    {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null || $rapport->dossier_id === null) {
            return;
        }

        $serviceEscalade = new ServiceEscalade();
        $serviceEscalade->creerEscalade(
            (int) $rapport->dossier_id,
            'commission_blocage',
            "Blocage commission après 3 tours sans unanimité pour le rapport #{$rapportId}",
            0 // Système
        );
    }

    /**
     * Termine une session de commission
     */
    public function terminerSession(int $sessionId, int $utilisateurId): bool
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        $session->terminer();

        ServiceAudit::log('fin_session', 'session_commission', $sessionId);

        return true;
    }

    /**
     * Retourne les statistiques de vote pour une session
     */
    public function getStatistiquesSession(int $sessionId): array
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            return [];
        }

        $rapportsEvalues = $session->getRapportsEvalues();
        $stats = [];

        foreach ($rapportsEvalues as $rapport) {
            $stats[] = [
                'rapport_id' => $rapport['id_rapport'],
                'etudiant' => $rapport['nom_etu'] . ' ' . $rapport['prenom_etu'],
                'resultats' => $session->getResultatsVote((int) $rapport['id_rapport']),
            ];
        }

        return [
            'session_id' => $sessionId,
            'tour_actuel' => $session->tour_vote,
            'statut' => $session->statut,
            'rapports' => $stats,
        ];
    }

    /**
     * Retourne les sessions planifiées
     */
    public function getSessionsPlanifiees(): array
    {
        return CommissionSession::planifiees();
    }

    /**
     * Retourne les sessions en cours
     */
    public function getSessionsEnCours(): array
    {
        return CommissionSession::enCours();
    }
}
