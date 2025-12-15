<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Conversation
 * 
 * Discussion entre utilisateurs.
 * Table: conversations
 */
class Conversation extends Model
{
    protected string $table = 'conversations';
    protected string $primaryKey = 'id_conversation';
    protected array $fillable = [
        'titre',
        'cree_par',
        'date_creation',
        'derniere_activite',
        'archivee',
    ];

    /**
     * Retourne les messages de la conversation
     */
    public function getMessages(): array
    {
        // Supposons Message model
        return Message::where(['conversation_id' => $this->getId()]);
    }

    /**
     * Retourne les participants
     */
    public function getParticipants(): array
    {
        // Table pivot 'correspondre' ou 'conversation_participants'
        $sql = "SELECT u.* FROM utilisateurs u
                INNER JOIN correspondre c ON c.utilisateur_id = u.id_utilisateur
                WHERE c.conversation_id = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
