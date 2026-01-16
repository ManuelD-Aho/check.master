<?php

declare(strict_types=1);

namespace App\Services\Commission;

use App\Models\CommissionSession;
use App\Models\CommissionMembre;
use App\Models\CommissionVote;
use App\Models\RapportEtudiant;
use App\Models\Enseignant;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\ValidationException;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Commission
 * 
 * Gère les sessions de commission, les évaluations et les votes.
 * 
 * @see PRD 03 - Workflow & Commission
 */
class ServiceCommission
{
    // =========================================================================
    // SESSIONS DE COMMISSION
    // =========================================================================

    /**
     * Crée une nouvelle session de commission
     */
    public function creerSession(array $donnees, int $creePar): CommissionSession
    {
        if (empty($donnees['date_session'])) {
            throw new ValidationException('La date de session est obligatoire');
        }

        $session = new CommissionSession([
            'date_session' => $donnees['date_session'],
            'heure_debut' => $donnees['heure_debut'] ?? '09:00:00',
            'heure_fin' => $donnees['heure_fin'] ?? null,
            'lieu' => $donnees['lieu'] ?? null,
            'type_session' => $donnees['type_session'] ?? 'Evaluation',
            'statut' => 'Planifiee',
            'ordre_du_jour' => $donnees['ordre_du_jour'] ?? null,
        ]);
        $session->save();

        ServiceAudit::logCreation('session_commission', $session->getId(), $donnees);

        return $session;
    }

    /**
     * Démarre une session de commission
     */
    public function demarrerSession(int $sessionId, int $demarrePar): CommissionSession
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        if ($session->statut !== 'Planifiee') {
            throw new ValidationException('Seule une session planifiée peut être démarrée');
        }

        // Vérifier qu'il y a des membres
        $membres = $session->membres();
        if (count($membres) < 3) {
            throw new ValidationException('Une session nécessite au moins 3 membres');
        }

        $session->statut = 'En_cours';
        $session->heure_debut_effective = date('H:i:s');
        $session->save();

        ServiceAudit::log('demarrage_session', 'session_commission', $sessionId);

        // Notifier les membres
        $this->notifierMembresDebutSession($session);

        return $session;
    }

    /**
     * Termine une session de commission
     */
    public function terminerSession(int $sessionId, int $terminePar): CommissionSession
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        if ($session->statut !== 'En_cours') {
            throw new ValidationException('Seule une session en cours peut être terminée');
        }

        $session->statut = 'Terminee';
        $session->heure_fin_effective = date('H:i:s');
        $session->save();

        ServiceAudit::log('fin_session', 'session_commission', $sessionId);

        // Générer le PV
        $this->genererPV($session);

        return $session;
    }

    /**
     * Ajoute un membre à une session
     */
    public function ajouterMembreSession(int $sessionId, int $enseignantId): CommissionMembre
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        $enseignant = Enseignant::find($enseignantId);
        if ($enseignant === null) {
            throw new NotFoundException('Enseignant non trouvé');
        }

        // Vérifier s'il n'est pas déjà membre
        $existant = CommissionMembre::firstWhere([
            'session_id' => $sessionId,
            'enseignant_id' => $enseignantId,
        ]);

        if ($existant !== null) {
            throw new ValidationException('Cet enseignant est déjà membre de cette session');
        }

        $membre = new CommissionMembre([
            'session_id' => $sessionId,
            'enseignant_id' => $enseignantId,
            'role' => 'Membre',
            'present' => true,
        ]);
        $membre->save();

        ServiceAudit::log('ajout_membre_session', 'session_commission', $sessionId, [
            'enseignant_id' => $enseignantId,
        ]);

        return $membre;
    }

    /**
     * Retire un membre d'une session
     */
    public function retirerMembreSession(int $sessionId, int $membreId): void
    {
        $membre = CommissionMembre::find($membreId);
        if ($membre === null || (int) $membre->session_id !== $sessionId) {
            throw new NotFoundException('Membre non trouvé dans cette session');
        }

        // Vérifier que la session n'est pas terminée
        $session = CommissionSession::find($sessionId);
        if ($session !== null && $session->statut === 'Terminee') {
            throw new ValidationException('Impossible de modifier une session terminée');
        }

        $membre->delete();

        ServiceAudit::log('retrait_membre_session', 'session_commission', $sessionId, [
            'membre_id' => $membreId,
        ]);
    }

    // =========================================================================
    // VOTES
    // =========================================================================

    /**
     * Enregistre un vote pour un rapport
     */
    public function voter(
        int $sessionId,
        int $rapportId,
        int $membreId,
        int $tour,
        string $decision,
        ?string $commentaire = null
    ): CommissionVote {
        // Vérifier que la session est en cours
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        if ($session->statut !== 'En_cours') {
            throw new ValidationException('Les votes ne sont autorisés que pendant une session en cours');
        }

        // Vérifier que le membre appartient à la session
        $membre = CommissionMembre::firstWhere([
            'session_id' => $sessionId,
            'enseignant_id' => $membreId,
        ]);

        if ($membre === null) {
            throw new ValidationException('Vous n\'êtes pas membre de cette session');
        }

        $vote = CommissionVote::voter($sessionId, $rapportId, $membreId, $tour, $decision, $commentaire);

        ServiceAudit::log('vote_commission', 'session_commission', $sessionId, [
            'rapport_id' => $rapportId,
            'membre_id' => $membreId,
            'decision' => $decision,
        ]);

        // Vérifier si tous les membres ont voté
        $this->verifierQuorumVote($sessionId, $rapportId, $tour);

        return $vote;
    }

    /**
     * Vérifie si le quorum est atteint et applique la décision
     */
    private function verifierQuorumVote(int $sessionId, int $rapportId, int $tour): void
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            return;
        }

        $membres = $session->membres();
        $nbMembresPresents = count(array_filter($membres, fn($m) => $m->present));

        $nbVotes = CommissionVote::nombreVotes($sessionId, $rapportId, $tour);

        // Tous les membres ont voté
        if ($nbVotes >= $nbMembresPresents) {
            $decision = CommissionVote::unanimiteAtteinte($sessionId, $rapportId, $tour, $nbMembresPresents);

            if ($decision !== null) {
                // Appliquer la décision au rapport
                $this->appliquerDecisionRapport($rapportId, $decision);
            }
        }
    }

    /**
     * Applique une décision à un rapport
     */
    private function appliquerDecisionRapport(int $rapportId, string $decision): void
    {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            return;
        }

        $nouveauStatut = match ($decision) {
            'Valider' => 'Valide',
            'A_revoir' => 'A_reviser',
            'Rejeter' => 'Rejete',
            default => $rapport->statut,
        };

        $rapport->statut = $nouveauStatut;
        $rapport->save();

        ServiceAudit::log('decision_rapport', 'rapport', $rapportId, [
            'decision' => $decision,
            'nouveau_statut' => $nouveauStatut,
        ]);
    }

    // =========================================================================
    // RAPPORTS & ÉVALUATION
    // =========================================================================

    /**
     * Ajoute un rapport à l'ordre du jour d'une session
     */
    public function ajouterRapportSession(int $sessionId, int $rapportId): void
    {
        $session = CommissionSession::find($sessionId);
        if ($session === null) {
            throw new NotFoundException('Session non trouvée');
        }

        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            throw new NotFoundException('Rapport non trouvé');
        }

        // Créer le lien rapport-session
        $sql = "INSERT INTO rapports_sessions (session_id, rapport_id, ordre_passage) 
                SELECT :session, :rapport, COALESCE(MAX(ordre_passage), 0) + 1 
                FROM rapports_sessions WHERE session_id = :session2";

        Model::raw($sql, [
            'session' => $sessionId,
            'rapport' => $rapportId,
            'session2' => $sessionId,
        ]);

        ServiceAudit::log('ajout_rapport_session', 'session_commission', $sessionId, [
            'rapport_id' => $rapportId,
        ]);
    }

    // =========================================================================
    // STATISTIQUES
    // =========================================================================

    /**
     * Statistiques générales des sessions
     */
    public function getStatistiques(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'Planifiee' THEN 1 ELSE 0 END) as planifiees,
                    SUM(CASE WHEN statut = 'En_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'Terminee' THEN 1 ELSE 0 END) as terminees,
                    SUM(CASE WHEN statut = 'Annulee' THEN 1 ELSE 0 END) as annulees
                FROM sessions_commission";

        $stmt = Model::raw($sql, []);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Rapports évalués
        $sqlRapports = "SELECT COUNT(DISTINCT rapport_id) as total FROM votes_commission";
        $stmtRapports = Model::raw($sqlRapports, []);
        $rapports = $stmtRapports->fetch(\PDO::FETCH_ASSOC);

        return [
            'sessions' => $stats,
            'rapports_evalues' => (int) ($rapports['total'] ?? 0),
        ];
    }

    /**
     * Statistiques d'une session spécifique
     */
    public function getStatistiquesSession(int $sessionId): array
    {
        // Nombre de rapports
        $sqlRapports = "SELECT COUNT(*) as total FROM rapports_sessions WHERE session_id = :id";
        $stmtRapports = Model::raw($sqlRapports, ['id' => $sessionId]);
        $nbRapports = (int) $stmtRapports->fetchColumn();

        // Votes par décision
        $sqlVotes = "SELECT decision, COUNT(*) as total 
                     FROM votes_commission 
                     WHERE session_id = :id 
                     GROUP BY decision";
        $stmtVotes = Model::raw($sqlVotes, ['id' => $sessionId]);
        $votes = $stmtVotes->fetchAll(\PDO::FETCH_ASSOC);

        // Membres présents
        $sqlMembres = "SELECT COUNT(*) as total, 
                              SUM(CASE WHEN present = 1 THEN 1 ELSE 0 END) as presents
                       FROM commission_membres 
                       WHERE session_id = :id";
        $stmtMembres = Model::raw($sqlMembres, ['id' => $sessionId]);
        $membres = $stmtMembres->fetch(\PDO::FETCH_ASSOC);

        return [
            'rapports' => $nbRapports,
            'votes_par_decision' => $votes,
            'membres' => $membres,
        ];
    }

    // =========================================================================
    // PV & NOTIFICATIONS
    // =========================================================================

    /**
     * Génère le PV d'une session
     */
    private function genererPV(CommissionSession $session): void
    {
        // TODO: Implémentation de la génération du PV PDF
        ServiceAudit::log('generation_pv', 'session_commission', $session->getId());
    }

    /**
     * Notifie les membres du début d'une session
     */
    private function notifierMembresDebutSession(CommissionSession $session): void
    {
        $membres = $session->membres();
        $utilisateurIds = [];

        foreach ($membres as $membre) {
            $enseignant = Enseignant::find((int) $membre->enseignant_id);
            if ($enseignant !== null) {
                // Trouver l'utilisateur associé
                $sql = "SELECT id_utilisateur FROM utilisateurs WHERE login_utilisateur = :email";
                $stmt = Model::raw($sql, ['email' => $enseignant->email_ens]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($result) {
                    $utilisateurIds[] = (int) $result['id_utilisateur'];
                }
            }
        }

        if (!empty($utilisateurIds)) {
            ServiceNotification::envoyerParCode(
                'commission_debut_session',
                $utilisateurIds,
                [
                    'session_date' => $session->date_session,
                    'lieu' => $session->lieu ?? 'Non défini',
                ]
            );
        }
    }
}
