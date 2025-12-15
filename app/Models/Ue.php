<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Ue (Unité d'Enseignement)
 * 
 * Représente une unité d'enseignement.
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

    /**
     * Trouve une UE par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_ue' => $code]);
    }

    /**
     * Retourne les ECUE de cette UE
     */
    public function getEcues(): array
    {
        $sql = "SELECT * FROM ecue WHERE ue_id = :id ORDER BY code_ecue";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Retourne le niveau d'étude
     */
    public function getNiveau(): ?NiveauEtude
    {
        if ($this->niveau_id === null) {
            return null;
        }
        return NiveauEtude::find((int) $this->niveau_id);
    }

    /**
     * Retourne le semestre
     */
    public function getSemestre(): ?Semestre
    {
        if ($this->semestre_id === null) {
            return null;
        }
        return Semestre::find((int) $this->semestre_id);
    }

    /**
     * Retourne les UE par niveau
     *
     * @return self[]
     */
    public static function parNiveau(int $niveauId): array
    {
        return self::where(['niveau_id' => $niveauId]);
    }

    /**
     * Retourne les UE par semestre
     *
     * @return self[]
     */
    public static function parSemestre(int $semestreId): array
    {
        return self::where(['semestre_id' => $semestreId]);
    }

    /**
     * Calcule le total des crédits des ECUE
     */
    public function totalCreditsEcue(): int
    {
        $sql = "SELECT COALESCE(SUM(credits), 0) FROM ecue WHERE ue_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
