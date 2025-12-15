<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CommissionSession
 * 
 * Représente une session de la commission d'évaluation.
 * Table: sessions_commission
 */
class CommissionSession extends Model
{
    protected string $table = 'sessions_commission';
    protected string $primaryKey = 'id_session';
    protected array $fillable = [
        'date_session',
        'lieu',
        'statut',
        'tour_vote',
        'pv_genere',
        'pv_chemin',
    ];

    /**
     * Statuts de session
     */
    public const STATUT_PLANIFIEE = 'Planifiee';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_TERMINEE = 'Terminee';
    public const STATUT_ANNULEE = 'Annulee';

    /**
     * Démarre la session
     */
    public function demarrer(): void
    {
        $this->statut = self::STATUT_EN_COURS;
        $this->save();
    }

    /**
     * Termine la session
     */
    public function terminer(): void
    {
        $this->statut = self::STATUT_TERMINEE;
        $this->save();
    }

    /**
     * Annule la session
     */
    public function annuler(): void
    {
        $this->statut = self::STATUT_ANNULEE;
        $this->save();
    }

    /**
     * Retourne les votes de cette session
     */
    public function getVotes(?int $tour = null): array
    {
        $sql = "SELECT v.*, r.titre as rapport_titre, e.nom_ens, e.prenom_ens
                FROM votes_commission v
                INNER JOIN rapports_etudiants r ON r.id_rapport = v.rapport_id
                INNER JOIN enseignants e ON e.id_enseignant = v.membre_id
                WHERE v.session_id = :id";

        $params = ['id' => $this->getId()];

        if ($tour !== null) {
            $sql .= " AND v.tour = :tour";
            $params['tour'] = $tour;
        }

        $sql .= " ORDER BY r.id_rapport, v.tour";

        $stmt = self::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Passe au tour suivant
     */
    public function tourSuivant(): void
    {
        $this->tour_vote = ($this->tour_vote ?? 1) + 1;
        $this->save();
    }

    /**
     * Marque le PV comme généré
     */
    public function marquerPvGenere(string $chemin): void
    {
        $this->pv_genere = true;
        $this->pv_chemin = $chemin;
        $this->save();
    }

    /**
     * Retourne les sessions planifiées
     *
     * @return self[]
     */
    public static function planifiees(): array
    {
        return self::where(['statut' => self::STATUT_PLANIFIEE]);
    }

    /**
     * Retourne la prochaine session planifiée
     */
    public static function prochaine(): ?self
    {
        $sql = "SELECT * FROM sessions_commission 
                WHERE statut = :statut AND date_session > NOW()
                ORDER BY date_session ASC LIMIT 1";

        $stmt = self::raw($sql, ['statut' => self::STATUT_PLANIFIEE]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Compte les rapports à évaluer dans cette session
     */
    public function nombreRapportsAEvaluer(): int
    {
        $sql = "SELECT COUNT(DISTINCT rapport_id) FROM votes_commission WHERE session_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
