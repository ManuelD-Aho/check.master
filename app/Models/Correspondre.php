<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Correspondre
 * 
 * Table de liaison entre utilisateurs et conversations.
 * Gère les participants aux conversations internes.
 * Table: correspondre
 */
class Correspondre extends Model
{
    protected string $table = 'correspondre';
    protected string $primaryKey = 'id_correspondre';
    protected array $fillable = [
        'conversation_id',
        'utilisateur_id',
        'vu_a',
        'est_admin',
        'date_ajout',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne la conversation
     */
    public function conversation(): ?Conversation
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id_conversation');
    }

    /**
     * Retourne l'utilisateur
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les participants d'une conversation
     * @return self[]
     */
    public static function pourConversation(int $conversationId): array
    {
        return self::where(['conversation_id' => $conversationId]);
    }

    /**
     * Retourne les conversations d'un utilisateur
     * @return self[]
     */
    public static function pourUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT * FROM correspondre 
                WHERE utilisateur_id = :id 
                ORDER BY vu_a DESC";
        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Trouve un participant spécifique
     */
    public static function trouverParticipant(int $conversationId, int $utilisateurId): ?self
    {
        return self::firstWhere([
            'conversation_id' => $conversationId,
            'utilisateur_id' => $utilisateurId,
        ]);
    }

    /**
     * Retourne les conversations non lues d'un utilisateur
     * @return self[]
     */
    public static function nonLues(int $utilisateurId): array
    {
        $sql = "SELECT c.* FROM correspondre c
                INNER JOIN conversation conv ON conv.id_conversation = c.conversation_id
                WHERE c.utilisateur_id = :id 
                AND (c.vu_a IS NULL OR c.vu_a < conv.updated_at)";
        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Ajoute un participant à une conversation
     */
    public static function ajouterParticipant(
        int $conversationId,
        int $utilisateurId,
        bool $estAdmin = false
    ): self {
        // Vérifier si déjà participant
        $existant = self::trouverParticipant($conversationId, $utilisateurId);
        if ($existant !== null) {
            return $existant;
        }

        $participant = new self([
            'conversation_id' => $conversationId,
            'utilisateur_id' => $utilisateurId,
            'est_admin' => $estAdmin,
            'date_ajout' => date('Y-m-d H:i:s'),
        ]);
        $participant->save();
        return $participant;
    }

    /**
     * Marque la conversation comme vue
     */
    public function marquerVue(): void
    {
        $this->vu_a = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Vérifie si l'utilisateur est admin de la conversation
     */
    public function estAdminConversation(): bool
    {
        return (bool) $this->est_admin;
    }

    /**
     * Promeut le participant en admin
     */
    public function promouvoirAdmin(): void
    {
        $this->est_admin = true;
        $this->save();
    }

    /**
     * Retire les droits admin
     */
    public function retirerAdmin(): void
    {
        $this->est_admin = false;
        $this->save();
    }

    /**
     * Vérifie si un utilisateur participe à une conversation
     */
    public static function participe(int $conversationId, int $utilisateurId): bool
    {
        return self::trouverParticipant($conversationId, $utilisateurId) !== null;
    }

    /**
     * Compte le nombre de participants d'une conversation
     */
    public static function nombreParticipants(int $conversationId): int
    {
        $sql = "SELECT COUNT(*) FROM correspondre WHERE conversation_id = :id";
        $stmt = self::raw($sql, ['id' => $conversationId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne les IDs des participants d'une conversation
     * @return int[]
     */
    public static function idsParticipants(int $conversationId): array
    {
        $participants = self::pourConversation($conversationId);
        return array_map(fn($p) => (int) $p->utilisateur_id, $participants);
    }
}
