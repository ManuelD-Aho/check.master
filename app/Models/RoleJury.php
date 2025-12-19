<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RoleJury
 * 
 * Représente un rôle possible dans un jury de soutenance.
 * Table: roles_jury
 */
class RoleJury extends Model
{
    protected string $table = 'roles_jury';
    protected string $primaryKey = 'id_role';
    protected array $fillable = [
        'code_role',
        'libelle_role',
        'ordre_affichage',
    ];

    /**
     * Codes de rôles prédéfinis
     */
    public const ROLE_PRESIDENT = 'PRESIDENT';
    public const ROLE_RAPPORTEUR = 'RAPPORTEUR';
    public const ROLE_EXAMINATEUR = 'EXAMINATEUR';
    public const ROLE_ENCADREUR = 'ENCADREUR';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un rôle par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_role' => $code]);
    }

    /**
     * Retourne tous les rôles triés par ordre d'affichage
     * @return self[]
     */
    public static function triesParOrdre(): array
    {
        $sql = "SELECT * FROM roles_jury ORDER BY ordre_affichage ASC";
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
     * Crée un nouveau rôle
     */
    public static function creer(
        string $code,
        string $libelle,
        int $ordre
    ): self {
        $role = new self([
            'code_role' => $code,
            'libelle_role' => $libelle,
            'ordre_affichage' => $ordre,
        ]);
        $role->save();
        return $role;
    }

    /**
     * Vérifie si c'est le rôle de président
     */
    public function estPresident(): bool
    {
        return $this->code_role === self::ROLE_PRESIDENT;
    }

    /**
     * Vérifie si c'est le rôle d'encadreur
     */
    public function estEncadreur(): bool
    {
        return $this->code_role === self::ROLE_ENCADREUR;
    }

    /**
     * Retourne le rôle de président
     */
    public static function president(): ?self
    {
        return self::findByCode(self::ROLE_PRESIDENT);
    }
}
