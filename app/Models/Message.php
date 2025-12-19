<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Message
 * 
 * Représente un message dans une conversation interne.
 * Table: messages
 */
class Message extends Model
{
    protected string $table = 'messages';
    protected string $primaryKey = 'id_message';
    protected array $fillable = [
        'conversation_id',
        'sender_id',
        'contenu',
        'type_contenu',
        'pieces_jointes',
        'envoye_a',
        'lu_par_tous',
    ];

    /**
     * Types de contenu
     */
    public const TYPE_TEXTE = 'text';
    public const TYPE_IMAGE = 'image';
    public const TYPE_FICHIER = 'file';

    // ===== RELATIONS =====

    /**
     * Retourne l'expéditeur
     */
    public function expediteur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'sender_id', 'id_utilisateur');
    }

    /**
     * Retourne la conversation
     */
    public function conversation(): ?Conversation
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id_conversation');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les messages d'une conversation
     * @return self[]
     */
    public static function pourConversation(int $conversationId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM messages 
                WHERE conversation_id = :id 
                ORDER BY envoye_a DESC
                LIMIT :limit OFFSET :offset";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('id', $conversationId, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les messages d'un utilisateur
     * @return self[]
     */
    public static function parUtilisateur(int $utilisateurId, int $limit = 50): array
    {
        $sql = "SELECT * FROM messages 
                WHERE sender_id = :id 
                ORDER BY envoye_a DESC
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
     * Recherche dans les messages
     * @return self[]
     */
    public static function rechercher(int $conversationId, string $terme): array
    {
        $sql = "SELECT * FROM messages 
                WHERE conversation_id = :id 
                AND contenu LIKE :terme
                ORDER BY envoye_a DESC";

        $stmt = self::raw($sql, [
            'id' => $conversationId,
            'terme' => '%' . $terme . '%',
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Envoie un nouveau message
     */
    public static function envoyer(
        int $conversationId,
        int $expediteurId,
        string $contenu,
        string $typeContenu = self::TYPE_TEXTE,
        ?array $piecesJointes = null
    ): self {
        $message = new self([
            'conversation_id' => $conversationId,
            'sender_id' => $expediteurId,
            'contenu' => $contenu,
            'type_contenu' => $typeContenu,
            'pieces_jointes' => $piecesJointes !== null ? json_encode($piecesJointes) : null,
            'envoye_a' => date('Y-m-d H:i:s'),
            'lu_par_tous' => false,
        ]);
        $message->save();

        // Mettre à jour l'activité de la conversation
        $conversation = Conversation::find($conversationId);
        if ($conversation !== null) {
            $conversation->mettreAJourActivite();
        }

        return $message;
    }

    /**
     * Marque le message comme lu par tous
     */
    public function marquerLuParTous(): void
    {
        $this->lu_par_tous = true;
        $this->save();
    }

    /**
     * Retourne les pièces jointes décodées
     */
    public function getPiecesJointes(): array
    {
        if (empty($this->pieces_jointes)) {
            return [];
        }
        return json_decode($this->pieces_jointes, true) ?? [];
    }

    /**
     * Vérifie si le message a des pièces jointes
     */
    public function aPiecesJointes(): bool
    {
        return !empty($this->getPiecesJointes());
    }

    /**
     * Vérifie si le message est de type texte
     */
    public function estTexte(): bool
    {
        return $this->type_contenu === self::TYPE_TEXTE;
    }

    /**
     * Vérifie si le message est de type image
     */
    public function estImage(): bool
    {
        return $this->type_contenu === self::TYPE_IMAGE;
    }

    /**
     * Vérifie si le message est de type fichier
     */
    public function estFichier(): bool
    {
        return $this->type_contenu === self::TYPE_FICHIER;
    }

    /**
     * Retourne un aperçu du contenu
     */
    public function getApercu(int $longueur = 100): string
    {
        $contenu = strip_tags($this->contenu ?? '');
        if (strlen($contenu) <= $longueur) {
            return $contenu;
        }
        return substr($contenu, 0, $longueur) . '...';
    }

    /**
     * Compte les messages d'une conversation
     */
    public static function nombreMessages(int $conversationId): int
    {
        $sql = "SELECT COUNT(*) FROM messages WHERE conversation_id = :id";
        $stmt = self::raw($sql, ['id' => $conversationId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne le dernier message d'une conversation
     */
    public static function dernierMessage(int $conversationId): ?self
    {
        $sql = "SELECT * FROM messages 
                WHERE conversation_id = :id 
                ORDER BY envoye_a DESC 
                LIMIT 1";

        $stmt = self::raw($sql, ['id' => $conversationId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Alias pour rétrocompatibilité
     */
    public function getExpediteur(): ?Utilisateur
    {
        return $this->expediteur();
    }
}
