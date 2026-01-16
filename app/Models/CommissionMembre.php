<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle CommissionMembre
 * 
 * Représente un membre d'une session de commission.
 * Table: commission_membres
 * 
 * @see PRD 03 - Workflow & Commission
 */
class CommissionMembre extends Model
{
    protected string $table = 'commission_membres';
    protected string $primaryKey = 'id_membre';
    protected array $fillable = [
        'session_id',
        'enseignant_id',
        'role',
        'present',
        'heure_arrivee',
        'heure_depart',
    ];

    /**
     * Rôles possibles
     */
    public const ROLE_PRESIDENT = 'President';
    public const ROLE_RAPPORTEUR = 'Rapporteur';
    public const ROLE_MEMBRE = 'Membre';

    // ===== RELATIONS =====

    /**
     * Retourne la session
     */
    public function session(): ?CommissionSession
    {
        return $this->belongsTo(CommissionSession::class, 'session_id', 'id_session');
    }

    /**
     * Retourne l'enseignant
     */
    public function enseignant(): ?Enseignant
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id', 'id_enseignant');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les membres d'une session
     * @return self[]
     */
    public static function pourSession(int $sessionId): array
    {
        $sql = "SELECT cm.*, e.nom_ens, e.prenom_ens, g.lib_grade
                FROM commission_membres cm
                INNER JOIN enseignants e ON e.id_enseignant = cm.enseignant_id
                LEFT JOIN grades g ON g.id_grade = e.grade_id
                WHERE cm.session_id = :id
                ORDER BY cm.role, e.nom_ens";

        $stmt = self::raw($sql, ['id' => $sessionId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Vérifie si un enseignant est membre d'une session
     */
    public static function estMembre(int $sessionId, int $enseignantId): bool
    {
        return self::count([
            'session_id' => $sessionId,
            'enseignant_id' => $enseignantId,
        ]) > 0;
    }

    /**
     * Retourne le président d'une session
     */
    public static function president(int $sessionId): ?self
    {
        return self::firstWhere([
            'session_id' => $sessionId,
            'role' => self::ROLE_PRESIDENT,
        ]);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le membre est présent
     */
    public function estPresent(): bool
    {
        return (bool) $this->present;
    }

    /**
     * Vérifie si le membre est président
     */
    public function estPresident(): bool
    {
        return $this->role === self::ROLE_PRESIDENT;
    }

    /**
     * Vérifie si le membre est rapporteur
     */
    public function estRapporteur(): bool
    {
        return $this->role === self::ROLE_RAPPORTEUR;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Marque le membre comme présent
     */
    public function marquerPresent(): void
    {
        $this->present = true;
        $this->heure_arrivee = date('H:i:s');
        $this->save();
    }

    /**
     * Marque le membre comme absent
     */
    public function marquerAbsent(): void
    {
        $this->present = false;
        $this->save();
    }

    /**
     * Marque le départ du membre
     */
    public function marquerDepart(): void
    {
        $this->heure_depart = date('H:i:s');
        $this->save();
    }

    /**
     * Définit comme président
     */
    public function definirPresident(): void
    {
        $this->role = self::ROLE_PRESIDENT;
        $this->save();
    }

    /**
     * Définit comme rapporteur
     */
    public function definirRapporteur(): void
    {
        $this->role = self::ROLE_RAPPORTEUR;
        $this->save();
    }
}
