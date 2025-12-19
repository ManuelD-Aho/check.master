<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CommissionVote
 * 
 * Représente un vote d'un membre de commission sur un rapport.
 * Table: votes_commission
 */
class CommissionVote extends Model
{
    protected string $table = 'votes_commission';
    protected string $primaryKey = 'id_vote';
    protected array $fillable = [
        'session_id',
        'rapport_id',
        'membre_id',
        'tour',
        'decision',
        'commentaire',
    ];

    /**
     * Décisions possibles
     */
    public const DECISION_VALIDER = 'Valider';
    public const DECISION_A_REVOIR = 'A_revoir';
    public const DECISION_REJETER = 'Rejeter';

    // ===== RELATIONS =====

    /**
     * Retourne la session
     */
    public function session(): ?CommissionSession
    {
        return $this->belongsTo(CommissionSession::class, 'session_id', 'id_session');
    }

    /**
     * Retourne le rapport
     */
    public function rapport(): ?RapportEtudiant
    {
        return $this->belongsTo(RapportEtudiant::class, 'rapport_id', 'id_rapport');
    }

    /**
     * Retourne le membre (enseignant)
     */
    public function membre(): ?Enseignant
    {
        return $this->belongsTo(Enseignant::class, 'membre_id', 'id_enseignant');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les votes d'une session
     * @return self[]
     */
    public static function pourSession(int $sessionId): array
    {
        return self::where(['session_id' => $sessionId]);
    }

    /**
     * Retourne les votes pour un rapport
     * @return self[]
     */
    public static function pourRapport(int $rapportId): array
    {
        return self::where(['rapport_id' => $rapportId]);
    }

    /**
     * Retourne les votes d'un membre
     * @return self[]
     */
    public static function parMembre(int $membreId): array
    {
        return self::where(['membre_id' => $membreId]);
    }

    /**
     * Trouve un vote spécifique
     */
    public static function trouver(int $sessionId, int $rapportId, int $membreId, int $tour): ?self
    {
        return self::firstWhere([
            'session_id' => $sessionId,
            'rapport_id' => $rapportId,
            'membre_id' => $membreId,
            'tour' => $tour,
        ]);
    }

    /**
     * Vérifie si un membre a déjà voté
     */
    public static function aDejaVote(int $sessionId, int $rapportId, int $membreId, int $tour): bool
    {
        return self::count([
            'session_id' => $sessionId,
            'rapport_id' => $rapportId,
            'membre_id' => $membreId,
            'tour' => $tour,
        ]) > 0;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre un vote
     */
    public static function voter(
        int $sessionId,
        int $rapportId,
        int $membreId,
        int $tour,
        string $decision,
        ?string $commentaire = null
    ): self {
        // Vérifier si déjà voté
        if (self::aDejaVote($sessionId, $rapportId, $membreId, $tour)) {
            throw new \RuntimeException('Ce membre a déjà voté pour ce rapport à ce tour.');
        }

        $vote = new self([
            'session_id' => $sessionId,
            'rapport_id' => $rapportId,
            'membre_id' => $membreId,
            'tour' => $tour,
            'decision' => $decision,
            'commentaire' => $commentaire,
        ]);
        $vote->save();
        return $vote;
    }

    /**
     * Retourne les statistiques de vote pour un rapport à un tour
     */
    public static function statistiquesVote(int $sessionId, int $rapportId, int $tour): array
    {
        $sql = "SELECT decision, COUNT(*) as total
                FROM votes_commission
                WHERE session_id = :sid AND rapport_id = :rid AND tour = :tour
                GROUP BY decision";

        $stmt = self::raw($sql, [
            'sid' => $sessionId,
            'rid' => $rapportId,
            'tour' => $tour,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre de votes pour un rapport à un tour
     */
    public static function nombreVotes(int $sessionId, int $rapportId, int $tour): int
    {
        return self::count([
            'session_id' => $sessionId,
            'rapport_id' => $rapportId,
            'tour' => $tour,
        ]);
    }

    /**
     * Vérifie si l'unanimité est atteinte
     */
    public static function unanimiteAtteinte(int $sessionId, int $rapportId, int $tour, int $nombreMembres): ?string
    {
        $stats = self::statistiquesVote($sessionId, $rapportId, $tour);

        if (count($stats) !== 1) {
            return null; // Votes divergents
        }

        if ((int) $stats[0]['total'] >= $nombreMembres) {
            return $stats[0]['decision']; // Unanimité
        }

        return null; // Pas encore tous les votes
    }
}
