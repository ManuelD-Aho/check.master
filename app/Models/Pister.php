<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Pister (Audit Trail)
 * 
 * Enregistre toutes les actions critiques dans le système.
 * Table: pister
 * 
 * Note: Aucune suppression autorisée (auditabilité totale).
 */
class Pister extends Model
{
    protected string $table = 'pister';
    protected string $primaryKey = 'id_pister';
    protected array $fillable = [
        'utilisateur_id',
        'action',
        'entite_type',
        'entite_id',
        'donnees_snapshot',
        'ip_adresse',
        'user_agent',
    ];

    /**
     * Actions prédéfinies
     */
    public const ACTION_CONNEXION = 'connexion';
    public const ACTION_DECONNEXION = 'deconnexion';
    public const ACTION_CREATION = 'creation';
    public const ACTION_MODIFICATION = 'modification';
    public const ACTION_SUPPRESSION = 'suppression';
    public const ACTION_CONSULTATION = 'consultation';
    public const ACTION_EXPORT = 'export';
    public const ACTION_VALIDATION = 'validation';
    public const ACTION_TRANSITION = 'transition_workflow';
    public const ACTION_TENTATIVE_ECHEC = 'tentative_echec';

    /**
     * Enregistre une action dans l'audit trail
     */
    public static function enregistrer(
        string $action,
        ?int $utilisateurId = null,
        ?string $entiteType = null,
        ?int $entiteId = null,
        ?array $donnees = null,
        ?string $ipAdresse = null,
        ?string $userAgent = null
    ): self {
        $entry = new self([
            'utilisateur_id' => $utilisateurId,
            'action' => $action,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'donnees_snapshot' => $donnees !== null ? json_encode($donnees) : null,
            'ip_adresse' => $ipAdresse ?? self::getClientIp(),
            'user_agent' => $userAgent ?? self::getUserAgent(),
        ]);
        $entry->save();
        return $entry;
    }

    /**
     * Enregistre une connexion réussie
     */
    public static function connexion(int $utilisateurId): self
    {
        return self::enregistrer(
            self::ACTION_CONNEXION,
            $utilisateurId,
            'utilisateur',
            $utilisateurId
        );
    }

    /**
     * Enregistre une déconnexion
     */
    public static function deconnexion(int $utilisateurId): self
    {
        return self::enregistrer(
            self::ACTION_DECONNEXION,
            $utilisateurId,
            'utilisateur',
            $utilisateurId
        );
    }

    /**
     * Enregistre une tentative de connexion échouée
     */
    public static function tentativeEchouee(string $login, string $raison): self
    {
        return self::enregistrer(
            self::ACTION_TENTATIVE_ECHEC,
            null,
            null,
            null,
            ['login' => $login, 'raison' => $raison]
        );
    }

    /**
     * Enregistre la création d'une entité
     */
    public static function creation(int $utilisateurId, string $entiteType, int $entiteId, array $donnees = []): self
    {
        return self::enregistrer(
            self::ACTION_CREATION,
            $utilisateurId,
            $entiteType,
            $entiteId,
            $donnees
        );
    }

    /**
     * Enregistre une modification avec snapshot avant/après
     */
    public static function modification(
        int $utilisateurId,
        string $entiteType,
        int $entiteId,
        array $avant,
        array $apres
    ): self {
        return self::enregistrer(
            self::ACTION_MODIFICATION,
            $utilisateurId,
            $entiteType,
            $entiteId,
            ['avant' => $avant, 'apres' => $apres]
        );
    }

    /**
     * Enregistre une suppression avec snapshot
     */
    public static function suppression(int $utilisateurId, string $entiteType, int $entiteId, array $donnees): self
    {
        return self::enregistrer(
            self::ACTION_SUPPRESSION,
            $utilisateurId,
            $entiteType,
            $entiteId,
            $donnees
        );
    }

    /**
     * Enregistre une transition de workflow
     */
    public static function transitionWorkflow(
        int $utilisateurId,
        int $dossierId,
        string $etatSource,
        string $etatCible,
        ?string $commentaire = null
    ): self {
        return self::enregistrer(
            self::ACTION_TRANSITION,
            $utilisateurId,
            'dossier',
            $dossierId,
            [
                'etat_source' => $etatSource,
                'etat_cible' => $etatCible,
                'commentaire' => $commentaire,
            ]
        );
    }

    /**
     * Retourne les logs d'un utilisateur
     *
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId, int $limit = 100): array
    {
        $sql = "SELECT * FROM pister WHERE utilisateur_id = :id 
                ORDER BY created_at DESC LIMIT :limit";

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
     * Retourne les logs d'une entité
     *
     * @return self[]
     */
    public static function pourEntite(string $type, int $id, int $limit = 50): array
    {
        $sql = "SELECT * FROM pister 
                WHERE entite_type = :type AND entite_id = :id 
                ORDER BY created_at DESC LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('type', $type, \PDO::PARAM_STR);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
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
     * Recherche dans les logs
     */
    public static function rechercher(array $criteres, int $limit = 100): array
    {
        $where = [];
        $params = [];

        if (!empty($criteres['action'])) {
            $where[] = 'action = :action';
            $params['action'] = $criteres['action'];
        }

        if (!empty($criteres['utilisateur_id'])) {
            $where[] = 'utilisateur_id = :user_id';
            $params['user_id'] = $criteres['utilisateur_id'];
        }

        if (!empty($criteres['entite_type'])) {
            $where[] = 'entite_type = :type';
            $params['type'] = $criteres['entite_type'];
        }

        if (!empty($criteres['date_debut'])) {
            $where[] = 'created_at >= :debut';
            $params['debut'] = $criteres['date_debut'];
        }

        if (!empty($criteres['date_fin'])) {
            $where[] = 'created_at <= :fin';
            $params['fin'] = $criteres['date_fin'];
        }

        $sql = "SELECT * FROM pister";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY created_at DESC LIMIT {$limit}";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les données snapshot décodées
     */
    public function getDonnees(): array
    {
        if (empty($this->donnees_snapshot)) {
            return [];
        }
        return json_decode($this->donnees_snapshot, true) ?? [];
    }

    /**
     * Récupère l'IP du client
     */
    private static function getClientIp(): string
    {
        $headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                return explode(',', $_SERVER[$header])[0];
            }
        }
        return 'unknown';
    }

    /**
     * Récupère le User-Agent
     */
    private static function getUserAgent(): string
    {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 500);
    }

    /**
     * Interdire la suppression des logs d'audit
     */
    public function delete(): bool
    {
        // Audit logs are immutable
        return false;
    }
}
