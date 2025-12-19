<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Pister
 * 
 * Journal d'audit inaltérable pour traçabilité complète.
 * Table: pister
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
     * Actions courantes
     */
    public const ACTION_CONNEXION = 'connexion';
    public const ACTION_DECONNEXION = 'deconnexion';
    public const ACTION_ECHEC_CONNEXION = 'echec_connexion';
    public const ACTION_CREATION = 'creation';
    public const ACTION_MODIFICATION = 'modification';
    public const ACTION_SUPPRESSION = 'suppression';
    public const ACTION_CONSULTATION = 'consultation';
    public const ACTION_EXPORT = 'export';
    public const ACTION_VALIDATION = 'validation';
    public const ACTION_TRANSITION = 'transition_workflow';
    public const ACTION_UPLOAD = 'upload';
    public const ACTION_TELECHARGEMENT = 'telechargement';

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur qui a effectué l'action
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
     * Retourne les entrées pour un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId, int $limit = 100): array
    {
        $sql = "SELECT * FROM pister 
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
     * Retourne les entrées pour une entité
     * @return self[]
     */
    public static function pourEntite(string $entiteType, int $entiteId): array
    {
        $sql = "SELECT * FROM pister 
                WHERE entite_type = :type AND entite_id = :id 
                ORDER BY created_at DESC";

        $stmt = self::raw($sql, ['type' => $entiteType, 'id' => $entiteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les entrées par action
     * @return self[]
     */
    public static function parAction(string $action, int $limit = 100): array
    {
        $sql = "SELECT * FROM pister 
                WHERE action = :action 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('action', $action, \PDO::PARAM_STR);
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
     * Retourne les entrées dans une plage de dates
     * @return self[]
     */
    public static function entreDates(\DateTime $debut, \DateTime $fin, int $limit = 1000): array
    {
        $sql = "SELECT * FROM pister 
                WHERE created_at BETWEEN :debut AND :fin 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('debut', $debut->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $stmt->bindValue('fin', $fin->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
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
    public static function rechercher(array $filtres, int $page = 1, int $parPage = 50): array
    {
        $sql = "SELECT * FROM pister WHERE 1=1";
        $params = [];

        if (!empty($filtres['utilisateur_id'])) {
            $sql .= " AND utilisateur_id = :uid";
            $params['uid'] = $filtres['utilisateur_id'];
        }

        if (!empty($filtres['action'])) {
            $sql .= " AND action = :action";
            $params['action'] = $filtres['action'];
        }

        if (!empty($filtres['entite_type'])) {
            $sql .= " AND entite_type = :etype";
            $params['etype'] = $filtres['entite_type'];
        }

        if (!empty($filtres['date_debut'])) {
            $sql .= " AND created_at >= :debut";
            $params['debut'] = $filtres['date_debut'];
        }

        if (!empty($filtres['date_fin'])) {
            $sql .= " AND created_at <= :fin";
            $params['fin'] = $filtres['date_fin'];
        }

        if (!empty($filtres['ip_adresse'])) {
            $sql .= " AND ip_adresse = :ip";
            $params['ip'] = $filtres['ip_adresse'];
        }

        $sql .= " ORDER BY created_at DESC";
        $offset = ($page - 1) * $parPage;
        $sql .= " LIMIT {$parPage} OFFSET {$offset}";

        $stmt = self::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne le snapshot JSON décodé
     */
    public function getSnapshot(): array
    {
        if (empty($this->donnees_snapshot)) {
            return [];
        }
        return json_decode($this->donnees_snapshot, true) ?? [];
    }

    /**
     * Enregistre une action
     */
    public static function log(
        string $action,
        ?int $utilisateurId = null,
        ?string $entiteType = null,
        ?int $entiteId = null,
        ?array $snapshot = null,
        ?string $ipAdresse = null,
        ?string $userAgent = null
    ): self {
        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'action' => $action,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'donnees_snapshot' => $snapshot !== null ? json_encode($snapshot) : null,
            'ip_adresse' => $ipAdresse,
            'user_agent' => $userAgent,
        ]);
        $model->save();
        return $model;
    }

    /**
     * Log une connexion réussie
     */
    public static function logConnexion(int $utilisateurId, string $ip, string $userAgent): self
    {
        return self::log(self::ACTION_CONNEXION, $utilisateurId, 'utilisateur', $utilisateurId, null, $ip, $userAgent);
    }

    /**
     * Log une déconnexion
     */
    public static function logDeconnexion(int $utilisateurId, string $ip): self
    {
        return self::log(self::ACTION_DECONNEXION, $utilisateurId, 'utilisateur', $utilisateurId, null, $ip);
    }

    /**
     * Log un échec de connexion
     */
    public static function logEchecConnexion(string $login, string $ip, string $userAgent): self
    {
        return self::log(
            self::ACTION_ECHEC_CONNEXION,
            null,
            'tentative_connexion',
            null,
            ['login' => $login],
            $ip,
            $userAgent
        );
    }

    /**
     * Log une création d'entité
     */
    public static function logCreation(
        int $utilisateurId,
        string $entiteType,
        int $entiteId,
        array $donnees,
        ?string $ip = null
    ): self {
        return self::log(self::ACTION_CREATION, $utilisateurId, $entiteType, $entiteId, $donnees, $ip);
    }

    /**
     * Log une modification d'entité
     */
    public static function logModification(
        int $utilisateurId,
        string $entiteType,
        int $entiteId,
        array $avant,
        array $apres,
        ?string $ip = null
    ): self {
        return self::log(
            self::ACTION_MODIFICATION,
            $utilisateurId,
            $entiteType,
            $entiteId,
            ['avant' => $avant, 'apres' => $apres],
            $ip
        );
    }

    /**
     * Log une suppression d'entité
     */
    public static function logSuppression(
        int $utilisateurId,
        string $entiteType,
        int $entiteId,
        array $donnees,
        ?string $ip = null
    ): self {
        return self::log(self::ACTION_SUPPRESSION, $utilisateurId, $entiteType, $entiteId, $donnees, $ip);
    }

    /**
     * Log une transition de workflow
     */
    public static function logTransition(
        int $utilisateurId,
        int $dossierId,
        string $etatSource,
        string $etatCible,
        ?string $commentaire = null,
        ?string $ip = null
    ): self {
        return self::log(
            self::ACTION_TRANSITION,
            $utilisateurId,
            'dossier_etudiant',
            $dossierId,
            [
                'etat_source' => $etatSource,
                'etat_cible' => $etatCible,
                'commentaire' => $commentaire,
            ],
            $ip
        );
    }

    /**
     * Compte les entrées par action (statistiques)
     */
    public static function statistiquesParAction(\DateTime $debut, \DateTime $fin): array
    {
        $sql = "SELECT action, COUNT(*) as total 
                FROM pister 
                WHERE created_at BETWEEN :debut AND :fin 
                GROUP BY action 
                ORDER BY total DESC";

        $stmt = self::raw($sql, [
            'debut' => $debut->format('Y-m-d H:i:s'),
            'fin' => $fin->format('Y-m-d H:i:s'),
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les IPs les plus fréquentes
     */
    public static function topIPs(int $limit = 10, ?\DateTime $depuis = null): array
    {
        $sql = "SELECT ip_adresse, COUNT(*) as total 
                FROM pister 
                WHERE ip_adresse IS NOT NULL";
        $params = [];

        if ($depuis !== null) {
            $sql .= " AND created_at >= :depuis";
            $params['depuis'] = $depuis->format('Y-m-d H:i:s');
        }

        $sql .= " GROUP BY ip_adresse ORDER BY total DESC LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
