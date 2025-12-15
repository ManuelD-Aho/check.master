<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Groupe
 * 
 * Représente un groupe de permissions (rôle).
 * Table: groupes
 */
class Groupe extends Model
{
    protected string $table = 'groupes';
    protected string $primaryKey = 'id_groupe';
    protected array $fillable = [
        'nom_groupe',
        'description',
        'niveau_hierarchique',
        'actif',
    ];

    /**
     * Retourne les permissions du groupe
     *
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return Permission::where(['groupe_id' => $this->getId()]);
    }

    /**
     * Retourne les utilisateurs de ce groupe
     *
     * @return Utilisateur[]
     */
    public function getUtilisateurs(): array
    {
        $sql = "SELECT u.* FROM utilisateurs u
                INNER JOIN utilisateurs_groupes ug ON ug.utilisateur_id = u.id_utilisateur
                WHERE ug.groupe_id = :groupe_id";

        $stmt = self::raw($sql, ['groupe_id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Utilisateur($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Vérifie si le groupe a une permission sur une ressource
     */
    public function aPermission(string $codeRessource, string $action): bool
    {
        $sql = "SELECT p.* FROM permissions p
                INNER JOIN ressources r ON r.id_ressource = p.ressource_id
                WHERE p.groupe_id = :groupe_id AND r.code_ressource = :code";

        $stmt = self::raw($sql, [
            'groupe_id' => $this->getId(),
            'code' => $codeRessource,
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        return match ($action) {
            'lire' => (bool) ($row['peut_lire'] ?? false),
            'creer' => (bool) ($row['peut_creer'] ?? false),
            'modifier' => (bool) ($row['peut_modifier'] ?? false),
            'supprimer' => (bool) ($row['peut_supprimer'] ?? false),
            'exporter' => (bool) ($row['peut_exporter'] ?? false),
            'valider' => (bool) ($row['peut_valider'] ?? false),
            default => false,
        };
    }

    /**
     * Vérifie si le groupe est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    /**
     * Retourne tous les groupes actifs
     *
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Trouve un groupe par son nom
     */
    public static function findByNom(string $nom): ?self
    {
        return self::firstWhere(['nom_groupe' => $nom]);
    }

    /**
     * Retourne les groupes triés par niveau hiérarchique
     *
     * @return self[]
     */
    public static function parHierarchie(): array
    {
        $sql = "SELECT * FROM groupes WHERE actif = 1 ORDER BY niveau_hierarchique ASC";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Compte le nombre d'utilisateurs dans ce groupe
     */
    public function compterUtilisateurs(): int
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs_groupes WHERE groupe_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
