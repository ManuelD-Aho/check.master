<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle StatutJury
 * 
 * Représente un statut possible pour un membre de jury.
 * Table: statut_jury
 */
class StatutJury extends Model
{
    protected string $table = 'statut_jury';
    protected string $primaryKey = 'id_statut';
    protected array $fillable = [
        'lib_statut',
        'description',
    ];

    /**
     * Statuts prédéfinis
     */
    public const STATUT_INVITE = 'Invité';
    public const STATUT_ACCEPTE = 'Accepté';
    public const STATUT_REFUSE = 'Refusé';
    public const STATUT_ABSENT = 'Absent';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un statut par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_statut' => $libelle]);
    }

    /**
     * Retourne tous les statuts
     * @return self[]
     */
    public static function tous(): array
    {
        $sql = "SELECT * FROM statut_jury ORDER BY lib_statut ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau statut
     */
    public static function creer(string $libelle, ?string $description = null): self
    {
        $statut = new self([
            'lib_statut' => $libelle,
            'description' => $description,
        ]);
        $statut->save();
        return $statut;
    }

    /**
     * Retourne le statut invité
     */
    public static function invite(): ?self
    {
        return self::findByLibelle(self::STATUT_INVITE);
    }

    /**
     * Retourne le statut accepté
     */
    public static function accepte(): ?self
    {
        return self::findByLibelle(self::STATUT_ACCEPTE);
    }
}
