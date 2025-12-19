<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle NotificationHistorique
 * 
 * Historique des notifications envoyées.
 * Table: notifications_historique
 */
class NotificationHistorique extends Model
{
    protected string $table = 'notifications_historique';
    protected string $primaryKey = 'id_historique';

    protected array $fillable = [
        'template_code',
        'destinataire_id',
        'canal',
        'sujet',
        'statut',
        'erreur_message',
    ];

    /**
     * Statuts
     */
    public const STATUT_ENVOYE = 'Envoye';
    public const STATUT_ECHEC = 'Echec';
    public const STATUT_BOUNCE = 'Bounce';

    /**
     * Canaux
     */
    public const CANAL_EMAIL = 'Email';
    public const CANAL_SMS = 'SMS';
    public const CANAL_MESSAGERIE = 'Messagerie';

    // ===== RELATIONS =====

    /**
     * Retourne le destinataire
     */
    public function destinataire(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'destinataire_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne l'historique d'un destinataire
     * @return self[]
     */
    public static function pourDestinataire(int $destinataireId, int $limit = 100): array
    {
        $sql = "SELECT * FROM notifications_historique 
                WHERE destinataire_id = :id 
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('id', $destinataireId, \PDO::PARAM_INT);
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
     * Retourne les envois échoués
     * @return self[]
     */
    public static function echecs(int $limit = 100): array
    {
        $sql = "SELECT * FROM notifications_historique 
                WHERE statut IN ('Echec', 'Bounce')
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
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
     * Enregistre un envoi réussi
     */
    public static function logEnvoi(
        string $templateCode,
        int $destinataireId,
        string $canal,
        string $sujet
    ): self {
        $historique = new self([
            'template_code' => $templateCode,
            'destinataire_id' => $destinataireId,
            'canal' => $canal,
            'sujet' => $sujet,
            'statut' => self::STATUT_ENVOYE,
        ]);
        $historique->save();
        return $historique;
    }

    /**
     * Enregistre un échec
     */
    public static function logEchec(
        string $templateCode,
        int $destinataireId,
        string $canal,
        string $sujet,
        string $erreur
    ): self {
        $historique = new self([
            'template_code' => $templateCode,
            'destinataire_id' => $destinataireId,
            'canal' => $canal,
            'sujet' => $sujet,
            'statut' => self::STATUT_ECHEC,
            'erreur_message' => $erreur,
        ]);
        $historique->save();
        return $historique;
    }

    /**
     * Enregistre un bounce
     */
    public static function logBounce(
        string $templateCode,
        int $destinataireId,
        string $canal,
        string $sujet,
        string $raison
    ): self {
        $historique = new self([
            'template_code' => $templateCode,
            'destinataire_id' => $destinataireId,
            'canal' => $canal,
            'sujet' => $sujet,
            'statut' => self::STATUT_BOUNCE,
            'erreur_message' => $raison,
        ]);
        $historique->save();
        return $historique;
    }

    /**
     * Statistiques par canal
     */
    public static function statistiquesParCanal(): array
    {
        $sql = "SELECT canal, statut, COUNT(*) as total
                FROM notifications_historique
                GROUP BY canal, statut
                ORDER BY canal, statut";

        $stmt = self::raw($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Taux de succès
     */
    public static function tauxSucces(?int $jours = 30): float
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'Envoye' THEN 1 ELSE 0 END) as succes
                FROM notifications_historique
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :jours DAY)";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('jours', $jours, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ((int) $result['total'] === 0) {
            return 100.0;
        }

        return round(((int) $result['succes'] / (int) $result['total']) * 100, 2);
    }
}
