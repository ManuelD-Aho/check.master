<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CommissionVote
 * 
 * Représente un vote d'un membre de la commission.
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

    /**
     * Retourne la session
     */
    public function getSession(): ?CommissionSession
    {
        if ($this->session_id === null) {
            return null;
        }
        return CommissionSession::find((int) $this->session_id);
    }

    /**
     * Retourne le rapport
     */
    public function getRapport(): ?RapportEtudiant
    {
        if ($this->rapport_id === null) {
            return null;
        }
        return RapportEtudiant::find((int) $this->rapport_id);
    }

    /**
     * Retourne le membre (enseignant)
     */
    public function getMembre(): ?Enseignant
    {
        if ($this->membre_id === null) {
            return null;
        }
        return Enseignant::find((int) $this->membre_id);
    }

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
     * Retourne les votes pour un rapport dans une session
     */
    public static function pourRapport(int $sessionId, int $rapportId, ?int $tour = null): array
    {
        $conditions = [
            'session_id' => $sessionId,
            'rapport_id' => $rapportId,
        ];

        if ($tour !== null) {
            $conditions['tour'] = $tour;
        }

        return self::where($conditions);
    }

    /**
     * Calcule le résultat d'un vote (unanimité, majorité, etc.)
     */
    public static function resultat(int $sessionId, int $rapportId, int $tour): array
    {
        $votes = self::pourRapport($sessionId, $rapportId, $tour);

        $compteur = [
            self::DECISION_VALIDER => 0,
            self::DECISION_A_REVOIR => 0,
            self::DECISION_REJETER => 0,
        ];

        foreach ($votes as $vote) {
            $compteur[$vote->decision] = ($compteur[$vote->decision] ?? 0) + 1;
        }

        $total = count($votes);
        $unanimite = false;
        $decisionFinale = null;

        // Vérifier l'unanimité
        if ($compteur[self::DECISION_VALIDER] === $total) {
            $unanimite = true;
            $decisionFinale = self::DECISION_VALIDER;
        } elseif ($compteur[self::DECISION_REJETER] === $total) {
            $unanimite = true;
            $decisionFinale = self::DECISION_REJETER;
        } else {
            // Majorité simple
            $max = max($compteur);
            $decisionFinale = array_search($max, $compteur, true);
        }

        return [
            'votes' => $compteur,
            'total' => $total,
            'unanimite' => $unanimite,
            'decision' => $decisionFinale,
        ];
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
}
