<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Notification
 * 
 * Représente une notification utilisateur.
 * Table: notifications
 */
class Notification extends Model
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'id_notification';
    protected array $fillable = [
        'destinataire_id',
        'type',
        'titre',
        'contenu',
        'lue',
        'lue_le',
        'lien',
        'donnees_json',
    ];

    /**
     * Types de notifications
     */
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCES = 'succes';
    public const TYPE_AVERTISSEMENT = 'avertissement';
    public const TYPE_ERREUR = 'erreur';
    public const TYPE_SYSTEME = 'systeme';

    /**
     * Marque la notification comme lue
     */
    public function marquerLue(): void
    {
        $this->lue = true;
        $this->lue_le = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Retourne les données additionnelles
     */
    public function getDonnees(): array
    {
        if (empty($this->donnees_json)) {
            return [];
        }
        return json_decode($this->donnees_json, true) ?? [];
    }

    /**
     * Envoie une notification
     */
    public static function envoyer(
        int $destinataireId,
        string $titre,
        string $contenu,
        string $type = self::TYPE_INFO,
        ?string $lien = null,
        ?array $donnees = null
    ): self {
        $notification = new self([
            'destinataire_id' => $destinataireId,
            'type' => $type,
            'titre' => $titre,
            'contenu' => $contenu,
            'lue' => false,
            'lien' => $lien,
            'donnees_json' => $donnees ? json_encode($donnees) : null,
        ]);
        $notification->save();
        return $notification;
    }

    /**
     * Envoie une notification à plusieurs destinataires
     */
    public static function envoyerMultiple(
        array $destinataireIds,
        string $titre,
        string $contenu,
        string $type = self::TYPE_INFO,
        ?string $lien = null
    ): int {
        $count = 0;
        foreach ($destinataireIds as $id) {
            self::envoyer($id, $titre, $contenu, $type, $lien);
            $count++;
        }
        return $count;
    }

    /**
     * Retourne les notifications non lues d'un utilisateur
     *
     * @return self[]
     */
    public static function nonLues(int $utilisateurId): array
    {
        return self::where([
            'destinataire_id' => $utilisateurId,
            'lue' => false,
        ]);
    }

    /**
     * Compte les notifications non lues
     */
    public static function compterNonLues(int $utilisateurId): int
    {
        $sql = "SELECT COUNT(*) FROM notifications WHERE destinataire_id = :id AND lue = 0";
        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Marque toutes les notifications comme lues
     */
    public static function marquerToutesLues(int $utilisateurId): int
    {
        $sql = "UPDATE notifications SET lue = 1, lue_le = :now 
                WHERE destinataire_id = :id AND lue = 0";

        $stmt = self::raw($sql, [
            'id' => $utilisateurId,
            'now' => date('Y-m-d H:i:s'),
        ]);

        return $stmt->rowCount();
    }

    /**
     * Supprime les vieilles notifications lues
     */
    public static function nettoyerAnciennes(int $joursRetention = 30): int
    {
        $sql = "DELETE FROM notifications 
                WHERE lue = 1 AND lue_le < DATE_SUB(NOW(), INTERVAL :jours DAY)";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('jours', $joursRetention, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
