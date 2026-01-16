<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CommissionSession (SessionCommission)
 * 
 * Représente une session de commission d'évaluation.
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
     * Statuts possibles
     */
    public const STATUT_PLANIFIEE = 'Planifiee';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_TERMINEE = 'Terminee';
    public const STATUT_ANNULEE = 'Annulee';

    /**
     * Nombre maximum de tours de vote
     */
    public const MAX_TOURS_VOTE = 3;

    // ===== RELATIONS =====

    /**
     * Retourne les membres de la session
     * @return CommissionMembre[]
     */
    public function membres(): array
    {
        return $this->hasMany(CommissionMembre::class, 'session_id', 'id_session');
    }

    /**
     * Retourne les votes de la session
     * @return CommissionVote[]
     */
    public function votes(): array
    {
        return $this->hasMany(CommissionVote::class, 'session_id', 'id_session');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les sessions planifiées
     * @return self[]
     */
    public static function planifiees(): array
    {
        $sql = "SELECT * FROM sessions_commission 
                WHERE statut = :statut AND date_session >= NOW()
                ORDER BY date_session";

        $stmt = self::raw($sql, ['statut' => self::STATUT_PLANIFIEE]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les sessions en cours
     * @return self[]
     */
    public static function enCours(): array
    {
        return self::where(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Retourne la prochaine session planifiée
     */
    public static function prochaine(): ?self
    {
        $sql = "SELECT * FROM sessions_commission 
                WHERE statut = :statut AND date_session >= NOW()
                ORDER BY date_session
                LIMIT 1";

        $stmt = self::raw($sql, ['statut' => self::STATUT_PLANIFIEE]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si la session est planifiée
     */
    public function estPlanifiee(): bool
    {
        return $this->statut === self::STATUT_PLANIFIEE;
    }

    /**
     * Vérifie si la session est en cours
     */
    public function estEnCours(): bool
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    /**
     * Vérifie si la session est terminée
     */
    public function estTerminee(): bool
    {
        return $this->statut === self::STATUT_TERMINEE;
    }

    /**
     * Vérifie si la session est annulée
     */
    public function estAnnulee(): bool
    {
        return $this->statut === self::STATUT_ANNULEE;
    }

    /**
     * Vérifie si on peut voter (session en cours et pas au max de tours)
     */
    public function peutVoter(): bool
    {
        return $this->estEnCours() && $this->tour_vote <= self::MAX_TOURS_VOTE;
    }

    // ===== MÉTHODES MÉTIER =====

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
     * Passe au tour suivant
     */
    public function passerAuTourSuivant(): bool
    {
        if ($this->tour_vote >= self::MAX_TOURS_VOTE) {
            return false;
        }
        $this->tour_vote = ($this->tour_vote ?? 1) + 1;
        $this->save();
        return true;
    }

    /**
     * Vérifie si escalade nécessaire (3 tours épuisés sans unanimité)
     */
    public function doitEscalader(): bool
    {
        return $this->tour_vote >= self::MAX_TOURS_VOTE;
    }

    /**
     * Retourne les rapports évalués dans cette session
     */
    public function getRapportsEvalues(): array
    {
        $sql = "SELECT DISTINCT r.*, e.nom_etu, e.prenom_etu
                FROM rapports_etudiants r
                INNER JOIN votes_commission vc ON vc.rapport_id = r.id_rapport
                INNER JOIN dossiers_etudiants de ON de.id_dossier = r.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                WHERE vc.session_id = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les résultats de vote pour un rapport
     */
    public function getResultatsVote(int $rapportId): array
    {
        $sql = "SELECT decision, COUNT(*) as total, GROUP_CONCAT(e.nom_ens) as votants
                FROM votes_commission vc
                INNER JOIN enseignants e ON e.id_enseignant = vc.membre_id
                WHERE vc.session_id = :sid AND vc.rapport_id = :rid AND vc.tour = :tour
                GROUP BY decision";

        $stmt = self::raw($sql, [
            'sid' => $this->getId(),
            'rid' => $rapportId,
            'tour' => $this->tour_vote,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si l'unanimité est atteinte pour un rapport
     */
    public function unanimiteAtteinte(int $rapportId): ?string
    {
        $resultats = $this->getResultatsVote($rapportId);

        if (count($resultats) === 1) {
            return $resultats[0]['decision'];
        }

        return null; // Pas d'unanimité
    }

    /**
     * Marque le PV comme généré
     */
    public function marquerPvGenere(string $cheminPv): void
    {
        $this->pv_genere = true;
        $this->pv_chemin = $cheminPv;
        $this->save();
    }
}
