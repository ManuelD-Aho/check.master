<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NotificationQueue
 * 
 * File d'attente des notifications à envoyer.
 * Table: notifications_queue
 */
class NotificationQueue extends Model
{
    protected string $table = 'notifications_queue';
    protected string $primaryKey = 'id_queue';

    protected array $fillable = [
        'template_id',
        'destinataire_id',
        'canal',
        'variables_json',
        'priorite',
        'statut',
        'tentatives',
        'erreur_message',
        'envoye_le',
    ];

    /**
     * Statuts de notification
     */
    public const STATUT_EN_ATTENTE = 'En_attente';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_ENVOYE = 'Envoye';
    public const STATUT_ECHEC = 'Echec';

    /**
     * Priorités
     */
    public const PRIORITE_BASSE = 10;
    public const PRIORITE_NORMALE = 5;
    public const PRIORITE_HAUTE = 1;

    /**
     * Nombre maximum de tentatives
     */
    public const MAX_TENTATIVES = 3;

    // ===== RELATIONS =====

    /**
     * Retourne le template de notification
     */
    public function template(): ?NotificationTemplate
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id', 'id_template');
    }

    /**
     * Retourne le destinataire
     */
    public function destinataire(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'destinataire_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les notifications en attente (par priorité)
     * @return self[]
     */
    public static function enAttente(int $limit = 50): array
    {
        $sql = "SELECT * FROM notifications_queue 
                WHERE statut = 'En_attente' 
                AND tentatives < :max_tentatives
                ORDER BY priorite ASC, created_at ASC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('max_tentatives', self::MAX_TENTATIVES, \PDO::PARAM_INT);
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
     * Retourne les notifications échouées
     * @return self[]
     */
    public static function echouees(): array
    {
        return self::where(['statut' => self::STATUT_ECHEC]);
    }

    /**
     * Retourne les notifications pour un destinataire
     * @return self[]
     */
    public static function pourDestinataire(int $destinataireId): array
    {
        $sql = "SELECT * FROM notifications_queue 
                WHERE destinataire_id = :id 
                ORDER BY created_at DESC";

        $stmt = self::raw($sql, ['id' => $destinataireId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Ajoute une notification à la file
     */
    public static function ajouter(
        int $templateId,
        int $destinataireId,
        string $canal,
        array $variables = [],
        int $priorite = self::PRIORITE_NORMALE
    ): self {
        $notification = new self([
            'template_id' => $templateId,
            'destinataire_id' => $destinataireId,
            'canal' => $canal,
            'variables_json' => json_encode($variables),
            'priorite' => $priorite,
            'statut' => self::STATUT_EN_ATTENTE,
            'tentatives' => 0,
        ]);
        $notification->save();
        return $notification;
    }

    /**
     * Retourne les variables
     */
    public function getVariables(): array
    {
        if (empty($this->variables_json)) {
            return [];
        }
        return json_decode($this->variables_json, true) ?? [];
    }

    /**
     * Marque comme en cours de traitement
     */
    public function marquerEnCours(): void
    {
        $this->statut = self::STATUT_EN_COURS;
        $this->save();
    }

    /**
     * Marque comme envoyée
     */
    public function marquerEnvoyee(): void
    {
        $this->statut = self::STATUT_ENVOYE;
        $this->envoye_le = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Marque comme échouée
     */
    public function marquerEchec(string $erreur): void
    {
        $this->tentatives = ($this->tentatives ?? 0) + 1;
        $this->erreur_message = $erreur;

        if ($this->tentatives >= self::MAX_TENTATIVES) {
            $this->statut = self::STATUT_ECHEC;
        } else {
            $this->statut = self::STATUT_EN_ATTENTE;
        }
        $this->save();
    }

    /**
     * Retente l'envoi
     */
    public function retenter(): void
    {
        if ($this->tentatives < self::MAX_TENTATIVES) {
            $this->statut = self::STATUT_EN_ATTENTE;
            $this->save();
        }
    }

    /**
     * Compte les notifications par statut
     */
    public static function statistiques(): array
    {
        $sql = "SELECT statut, COUNT(*) as total
                FROM notifications_queue
                GROUP BY statut";

        $stmt = self::raw($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Supprime les anciennes notifications envoyées
     */
    public static function nettoyer(int $joursRetention = 30): int
    {
        $sql = "DELETE FROM notifications_queue 
                WHERE statut = 'Envoye' 
                AND envoye_le < DATE_SUB(NOW(), INTERVAL :jours DAY)";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('jours', $joursRetention, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
