<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ue (Unité d'Enseignement)
 * 
 * Représente une Unité d'Enseignement.
 * Table: ue
 */
class Ue extends Model
{
    protected string $table = 'ue';
    protected string $primaryKey = 'id_ue';
    protected array $fillable = [
        'code_ue',
        'lib_ue',
        'credits',
        'niveau_id',
        'semestre_id',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne le niveau d'étude
     */
    public function niveau(): ?NiveauEtude
    {
        if ($this->niveau_id === null) {
            return null;
        }
        return $this->belongsTo(NiveauEtude::class, 'niveau_id', 'id_niveau');
    }

    /**
     * Retourne le semestre
     */
    public function semestre(): ?Semestre
    {
        if ($this->semestre_id === null) {
            return null;
        }
        return $this->belongsTo(Semestre::class, 'semestre_id', 'id_semestre');
    }

    /**
     * Retourne les ECUE de cette UE
     * @return Ecue[]
     */
    public function ecues(): array
    {
        return $this->hasMany(Ecue::class, 'ue_id', 'id_ue');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_ue' => $code]);
    }

    /**
     * Retourne les UE par niveau
     * @return self[]
     */
    public static function parNiveau(int $niveauId): array
    {
        return self::where(['niveau_id' => $niveauId]);
    }

    /**
     * Retourne les UE par semestre
     * @return self[]
     */
    public static function parSemestre(int $semestreId): array
    {
        return self::where(['semestre_id' => $semestreId]);
    }

    /**
     * Recherche d'UE
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM ue 
                WHERE code_ue LIKE :terme OR lib_ue LIKE :terme
                ORDER BY code_ue
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('terme', "%{$terme}%", \PDO::PARAM_STR);
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
     * Retourne le libellé complet (code + libellé)
     */
    public function getLibelleComplet(): string
    {
        return $this->code_ue . ' - ' . $this->lib_ue;
    }

    /**
     * Compte les ECUE de cette UE
     */
    public function nombreEcues(): int
    {
        return Ecue::count(['ue_id' => $this->getId()]);
    }

    /**
     * Total des crédits des ECUE
     */
    public function totalCreditsEcues(): int
    {
        $sql = "SELECT COALESCE(SUM(credits), 0) FROM ecue WHERE ue_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
