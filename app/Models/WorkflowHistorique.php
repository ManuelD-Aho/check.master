<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowHistorique
 * 
 * Enregistre l'historique des transitions de workflow.
 * Table: workflow_historique
 */
class WorkflowHistorique extends Model
{
    protected string $table = 'workflow_historique';
    protected string $primaryKey = 'id_historique';
    protected array $fillable = [
        'dossier_id',
        'etat_source_id',
        'etat_cible_id',
        'transition_id',
        'utilisateur_id',
        'commentaire',
        'snapshot_json',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne le dossier étudiant
     */
    public function dossier(): ?DossierEtudiant
    {
        return $this->belongsTo(DossierEtudiant::class, 'dossier_id', 'id_dossier');
    }

    /**
     * Retourne l'état source
     */
    public function etatSource(): ?WorkflowEtat
    {
        if ($this->etat_source_id === null) {
            return null;
        }
        return $this->belongsTo(WorkflowEtat::class, 'etat_source_id', 'id_etat');
    }

    /**
     * Retourne l'état cible
     */
    public function etatCible(): ?WorkflowEtat
    {
        return $this->belongsTo(WorkflowEtat::class, 'etat_cible_id', 'id_etat');
    }

    /**
     * Retourne la transition
     */
    public function transition(): ?WorkflowTransition
    {
        if ($this->transition_id === null) {
            return null;
        }
        return $this->belongsTo(WorkflowTransition::class, 'transition_id', 'id_transition');
    }

    /**
     * Retourne l'utilisateur qui a effectué la transition
     */
    public function utilisateur(): ?Utilisateur
    {
        if ($this->utilisateur_id === null) {
            return null;
        }
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne l'historique d'un dossier
     * @return self[]
     */
    public static function pourDossier(int $dossierId): array
    {
        $sql = "SELECT * FROM workflow_historique 
                WHERE dossier_id = :id 
                ORDER BY created_at DESC";

        $stmt = self::raw($sql, ['id' => $dossierId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne l'historique des transitions effectuées par un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId, int $limit = 100): array
    {
        $sql = "SELECT * FROM workflow_historique 
                WHERE utilisateur_id = :id 
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('id', $utilisateurId, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les transitions récentes (tous dossiers)
     * @return self[]
     */
    public static function recentes(int $limit = 50): array
    {
        $sql = "SELECT wh.*, de.etudiant_id, e.nom_etu, e.prenom_etu,
                       ws.nom_etat as nom_source, wc.nom_etat as nom_cible
                FROM workflow_historique wh
                INNER JOIN dossiers_etudiants de ON de.id_dossier = wh.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN workflow_etats ws ON ws.id_etat = wh.etat_source_id
                INNER JOIN workflow_etats wc ON wc.id_etat = wh.etat_cible_id
                ORDER BY wh.created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne le snapshot décodé
     */
    public function getSnapshot(): array
    {
        if (empty($this->snapshot_json)) {
            return [];
        }
        return json_decode($this->snapshot_json, true) ?? [];
    }

    /**
     * Enregistre une transition
     */
    public static function enregistrer(
        int $dossierId,
        ?int $etatSourceId,
        int $etatCibleId,
        ?int $transitionId,
        ?int $utilisateurId,
        ?string $commentaire = null,
        ?array $snapshot = null
    ): self {
        $model = new self([
            'dossier_id' => $dossierId,
            'etat_source_id' => $etatSourceId,
            'etat_cible_id' => $etatCibleId,
            'transition_id' => $transitionId,
            'utilisateur_id' => $utilisateurId,
            'commentaire' => $commentaire,
            'snapshot_json' => $snapshot !== null ? json_encode($snapshot) : null,
        ]);
        $model->save();
        return $model;
    }

    /**
     * Retourne la durée passée dans l'état précédent (en jours)
     */
    public static function dureeEtatPrecedent(int $dossierId, int $etatSourceId): ?int
    {
        $sql = "SELECT DATEDIFF(NOW(), created_at) as jours
                FROM workflow_historique
                WHERE dossier_id = :did AND etat_cible_id = :eid
                ORDER BY created_at DESC
                LIMIT 1";

        $stmt = self::raw($sql, ['did' => $dossierId, 'eid' => $etatSourceId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? (int) $result['jours'] : null;
    }

    /**
     * Statistiques des transitions par période
     */
    public static function statistiquesParPeriode(\DateTime $debut, \DateTime $fin): array
    {
        $sql = "SELECT DATE(created_at) as date, COUNT(*) as total
                FROM workflow_historique
                WHERE created_at BETWEEN :debut AND :fin
                GROUP BY DATE(created_at)
                ORDER BY date";

        $stmt = self::raw($sql, [
            'debut' => $debut->format('Y-m-d H:i:s'),
            'fin' => $fin->format('Y-m-d H:i:s'),
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
