<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowHistorique
 * 
 * Historique des transitions de workflow pour un dossier.
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

    /**
     * Enregistre une transition dans l'historique
     */
    public static function enregistrer(
        int $dossierId,
        ?int $etatSourceId,
        int $etatCibleId,
        ?int $transitionId = null,
        ?int $utilisateurId = null,
        ?string $commentaire = null,
        ?array $snapshot = null
    ): self {
        $historique = new self([
            'dossier_id' => $dossierId,
            'etat_source_id' => $etatSourceId,
            'etat_cible_id' => $etatCibleId,
            'transition_id' => $transitionId,
            'utilisateur_id' => $utilisateurId,
            'commentaire' => $commentaire,
            'snapshot_json' => $snapshot ? json_encode($snapshot) : null,
        ]);
        $historique->save();
        return $historique;
    }

    /**
     * Retourne l'historique d'un dossier
     *
     * @return self[]
     */
    public static function pourDossier(int $dossierId): array
    {
        $sql = "SELECT wh.*, 
                       ws.code_etat as source_code, ws.nom_etat as source_nom,
                       wc.code_etat as cible_code, wc.nom_etat as cible_nom,
                       u.nom_utilisateur
                FROM workflow_historique wh
                LEFT JOIN workflow_etats ws ON ws.id_etat = wh.etat_source_id
                INNER JOIN workflow_etats wc ON wc.id_etat = wh.etat_cible_id
                LEFT JOIN utilisateurs u ON u.id_utilisateur = wh.utilisateur_id
                WHERE wh.dossier_id = :id
                ORDER BY wh.created_at DESC";

        $stmt = self::raw($sql, ['id' => $dossierId]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

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
     * Retourne la dernière transition d'un dossier
     */
    public static function derniereTransition(int $dossierId): ?object
    {
        $sql = "SELECT * FROM workflow_historique 
                WHERE dossier_id = :id 
                ORDER BY created_at DESC LIMIT 1";

        $stmt = self::raw($sql, ['id' => $dossierId]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }
}
