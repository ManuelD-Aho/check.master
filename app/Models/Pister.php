<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Pister (Audit Trail)
 * 
 * Enregistre toutes les actions critiques du système.
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
     * Actions d'audit connues
     */
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_LOGIN_ECHEC = 'login_echec';
    public const ACTION_CREATION = 'creation';
    public const ACTION_MODIFICATION = 'modification';
    public const ACTION_SUPPRESSION = 'suppression';
    public const ACTION_DECONNEXION_FORCEE = 'deconnexion_forcee';

    /**
     * Crée une entrée d'audit
     */
    public static function enregistrer(
        string $action,
        ?int $utilisateurId = null,
        ?string $entiteType = null,
        ?int $entiteId = null,
        ?array $snapshot = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        $audit = new self([
            'utilisateur_id' => $utilisateurId,
            'action' => $action,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'donnees_snapshot' => $snapshot !== null ? json_encode($snapshot) : null,
            'ip_adresse' => $ip,
            'user_agent' => $userAgent,
        ]);
        $audit->save();
        return $audit;
    }

    /**
     * Retourne les entrées d'audit pour une entité
     */
    public static function getHistoriqueEntite(string $entiteType, int $entiteId): array
    {
        $sql = "SELECT * FROM pister 
                WHERE entite_type = :type AND entite_id = :id 
                ORDER BY created_at DESC";
        $stmt = self::raw($sql, ['type' => $entiteType, 'id' => $entiteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => new self($row), $rows);
    }

    /**
     * Retourne les dernières actions d'un utilisateur
     */
    public static function getDernieresActionsUtilisateur(int $userId, int $limit = 50): array
    {
        $sql = "SELECT * FROM pister 
                WHERE utilisateur_id = :id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        $stmt = self::raw($sql, ['id' => $userId, 'limit' => $limit]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => new self($row), $rows);
    }

    /**
     * Retourne le snapshot décodé
     */
    public function getSnapshot(): ?array
    {
        if ($this->donnees_snapshot === null) {
            return null;
        }
        return json_decode($this->donnees_snapshot, true);
    }
}
