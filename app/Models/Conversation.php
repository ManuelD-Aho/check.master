<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Conversation
 * 
 * Représente une discussion entre utilisateurs.
 * Gère les conversations internes de l'application.
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

    // ===== RELATIONS =====

    /**
     * Retourne le créateur de la conversation
     */
    public function createur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par', 'id_utilisateur');
    }

    /**
     * Retourne les messages de la conversation
     * @return Message[]
     */
    public function messages(): array
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id_conversation');
    }

    /**
     * Retourne les participants (via Correspondre)
     * @return Utilisateur[]
     */
    public function participants(): array
    {
        $sql = "SELECT u.* FROM utilisateurs u
                INNER JOIN correspondre c ON c.utilisateur_id = u.id_utilisateur
                WHERE c.conversation_id = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Utilisateur($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les conversations d'un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT c.* FROM conversations c
                INNER JOIN correspondre co ON co.conversation_id = c.id_conversation
                WHERE co.utilisateur_id = :id
                ORDER BY c.derniere_activite DESC";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les conversations non archivées d'un utilisateur
     * @return self[]
     */
    public static function activesUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT c.* FROM conversations c
                INNER JOIN correspondre co ON co.conversation_id = c.id_conversation
                WHERE co.utilisateur_id = :id AND c.archivee = 0
                ORDER BY c.derniere_activite DESC";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Trouve une conversation entre deux utilisateurs
     */
    public static function entreUtilisateurs(int $utilisateur1Id, int $utilisateur2Id): ?self
    {
        $sql = "SELECT c.* FROM conversations c
                INNER JOIN correspondre co1 ON co1.conversation_id = c.id_conversation
                INNER JOIN correspondre co2 ON co2.conversation_id = c.id_conversation
                WHERE co1.utilisateur_id = :u1 AND co2.utilisateur_id = :u2
                LIMIT 1";

        $stmt = self::raw($sql, ['u1' => $utilisateur1Id, 'u2' => $utilisateur2Id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée une nouvelle conversation
     */
    public static function creer(
        int $createurId,
        string $titre,
        array $participantsIds
    ): self {
        $conversation = new self([
            'titre' => $titre,
            'cree_par' => $createurId,
            'date_creation' => date('Y-m-d H:i:s'),
            'derniere_activite' => date('Y-m-d H:i:s'),
            'archivee' => false,
        ]);
        $conversation->save();

        // Ajouter le créateur comme participant admin
        Correspondre::ajouterParticipant(
            (int) $conversation->getId(),
            $createurId,
            true
        );

        // Ajouter les autres participants
        foreach ($participantsIds as $participantId) {
            if ((int) $participantId !== $createurId) {
                Correspondre::ajouterParticipant(
                    (int) $conversation->getId(),
                    (int) $participantId,
                    false
                );
            }
        }

        return $conversation;
    }

    /**
     * Ajoute un participant
     */
    public function ajouterParticipant(int $utilisateurId, bool $estAdmin = false): void
    {
        Correspondre::ajouterParticipant($this->getId(), $utilisateurId, $estAdmin);
    }

    /**
     * Met à jour l'activité de la conversation
     */
    public function mettreAJourActivite(): void
    {
        $this->derniere_activite = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Archive la conversation
     */
    public function archiver(): void
    {
        $this->archivee = true;
        $this->save();
    }

    /**
     * Désarchive la conversation
     */
    public function desarchiver(): void
    {
        $this->archivee = false;
        $this->save();
    }

    /**
     * Vérifie si la conversation est archivée
     */
    public function estArchivee(): bool
    {
        return (bool) $this->archivee;
    }

    /**
     * Vérifie si un utilisateur participe à la conversation
     */
    public function aParticipant(int $utilisateurId): bool
    {
        return Correspondre::participe($this->getId(), $utilisateurId);
    }

    /**
     * Compte le nombre de participants
     */
    public function nombreParticipants(): int
    {
        return Correspondre::nombreParticipants($this->getId());
    }

    /**
     * Compte le nombre de messages
     */
    public function nombreMessages(): int
    {
        $sql = "SELECT COUNT(*) FROM messages WHERE conversation_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne le dernier message
     */
    public function dernierMessage(): ?Message
    {
        $sql = "SELECT * FROM messages 
                WHERE conversation_id = :id 
                ORDER BY created_at DESC 
                LIMIT 1";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $model = new Message($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne les IDs des participants
     * @return int[]
     */
    public function idsParticipants(): array
    {
        return Correspondre::idsParticipants($this->getId());
    }
}
