<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle EscaladeAction
 * 
 * Représente une action effectuée sur une escalade.
 * Table: escalades_actions
 */
class EscaladeAction extends Model
{
    protected string $table = 'escalades_actions';
    protected string $primaryKey = 'id_action';

    protected array $fillable = [
        'escalade_id',
        'utilisateur_id',
        'type_action',
        'description',
    ];

    /**
     * Types d'actions
     */
    public const TYPE_PRISE_EN_CHARGE = 'prise_en_charge';
    public const TYPE_COMMENTAIRE = 'commentaire';
    public const TYPE_ESCALADE_SUPERIEURE = 'escalade_superieure';
    public const TYPE_RESOLUTION = 'resolution';
    public const TYPE_FERMETURE = 'fermeture';
    public const TYPE_REASSIGNATION = 'reassignation';

    // ===== RELATIONS =====

    /**
     * Retourne l'escalade parente
     */
    public function escalade(): ?Escalade
    {
        return $this->belongsTo(Escalade::class, 'escalade_id', 'id_escalade');
    }

    /**
     * Retourne l'utilisateur ayant effectué l'action
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les actions d'une escalade
     * @return self[]
     */
    public static function pourEscalade(int $escaladeId): array
    {
        $sql = "SELECT * FROM escalades_actions 
                WHERE escalade_id = :id 
                ORDER BY created_at ASC";

        $stmt = self::raw($sql, ['id' => $escaladeId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les actions d'un utilisateur
     * @return self[]
     */
    public static function parUtilisateur(int $utilisateurId, int $limit = 50): array
    {
        $sql = "SELECT * FROM escalades_actions 
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

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre une nouvelle action
     */
    public static function enregistrer(
        int $escaladeId,
        int $utilisateurId,
        string $typeAction,
        string $description
    ): self {
        $action = new self([
            'escalade_id' => $escaladeId,
            'utilisateur_id' => $utilisateurId,
            'type_action' => $typeAction,
            'description' => $description,
        ]);
        $action->save();
        return $action;
    }

    /**
     * Ajoute un commentaire à une escalade
     */
    public static function commenter(
        int $escaladeId,
        int $utilisateurId,
        string $commentaire
    ): self {
        return self::enregistrer(
            $escaladeId,
            $utilisateurId,
            self::TYPE_COMMENTAIRE,
            $commentaire
        );
    }
}
