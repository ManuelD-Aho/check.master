<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle MessageInterne
 * 
 * Représente un message interne entre utilisateurs.
 * Table: messages_internes
 */
class MessageInterne extends Model
{
    protected string $table = 'messages_internes';
    protected string $primaryKey = 'id_message';
    protected array $fillable = [
        'expediteur_id',
        'destinataire_id',
        'sujet',
        'contenu',
        'contexte_type',
        'contexte_id',
        'piece_jointe',
        'lu',
        'lu_le',
        'date_lecture',
        'supprime',
        'supprime_le',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne l'expéditeur
     */
    public function expediteur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'expediteur_id', 'id_utilisateur');
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
     * Retourne les messages reçus par un utilisateur
     * @return self[]
     */
    public static function boiteReception(int $utilisateurId, int $limit = 50): array
    {
        $sql = "SELECT * FROM messages_internes 
                WHERE destinataire_id = :id 
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
     * Retourne les messages envoyés par un utilisateur
     * @return self[]
     */
    public static function boiteEnvoi(int $utilisateurId, int $limit = 50): array
    {
        $sql = "SELECT * FROM messages_internes 
                WHERE expediteur_id = :id 
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
     * Retourne les messages non lus d'un utilisateur
     * @return self[]
     */
    public static function nonLus(int $utilisateurId): array
    {
        return self::where([
            'destinataire_id' => $utilisateurId,
            'lu' => false,
        ]);
    }

    /**
     * Compte les messages non lus
     */
    public static function nombreNonLus(int $utilisateurId): int
    {
        $sql = "SELECT COUNT(*) FROM messages_internes 
                WHERE destinataire_id = :id AND lu = 0";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        return (int) $stmt->fetchColumn();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Envoie un nouveau message
     */
    public static function envoyer(
        int $expediteurId,
        int $destinataireId,
        string $sujet,
        string $contenu
    ): self {
        $message = new self([
            'expediteur_id' => $expediteurId,
            'destinataire_id' => $destinataireId,
            'sujet' => $sujet,
            'contenu' => $contenu,
            'lu' => false,
        ]);
        $message->save();
        return $message;
    }

    /**
     * Marque le message comme lu
     */
    public function marquerCommeLu(): void
    {
        if (!$this->lu) {
            $this->lu = true;
            $this->date_lecture = date('Y-m-d H:i:s');
            $this->save();
        }
    }

    /**
     * Marque le message comme non lu
     */
    public function marquerCommeNonLu(): void
    {
        $this->lu = false;
        $this->date_lecture = null;
        $this->save();
    }

    /**
     * Marque tous les messages comme lus
     */
    public static function marquerTousCommeLus(int $utilisateurId): int
    {
        $sql = "UPDATE messages_internes 
                SET lu = 1, date_lecture = NOW() 
                WHERE destinataire_id = :id AND lu = 0";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        return $stmt->rowCount();
    }

    /**
     * Vérifie si le message est lu
     */
    public function estLu(): bool
    {
        return (bool) $this->lu;
    }

    /**
     * Retourne l'aperçu du contenu (tronqué)
     */
    public function getApercu(int $longueur = 100): string
    {
        $contenu = strip_tags($this->contenu ?? '');
        if (strlen($contenu) <= $longueur) {
            return $contenu;
        }
        return substr($contenu, 0, $longueur) . '...';
    }
}
