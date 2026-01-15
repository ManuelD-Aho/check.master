<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle GroupeUtilisateur
 * 
 * Représente un groupe utilisateur (rôle principal).
 * Table: groupe_utilisateur
 */
class GroupeUtilisateur extends Model
{
    protected string $table = 'groupe_utilisateur';
    protected string $primaryKey = 'id_GU';
    protected array $fillable = [
        'lib_GU',
        'description',
        'niveau_hierarchique',
    ];

    /**
     * Identifiants des groupes principaux (selon docs)
     */
    public const GROUPE_ADMIN = 5;
    public const GROUPE_SECRETAIRE = 6;
    public const GROUPE_COMMUNICATION = 7;
    public const GROUPE_SCOLARITE = 8;
    public const GROUPE_RESP_FILIERE = 9;
    public const GROUPE_RESP_NIVEAU = 10;
    public const GROUPE_COMMISSION = 11;
    public const GROUPE_ENSEIGNANT = 12;
    public const GROUPE_ETUDIANT = 13;

    // ===== RELATIONS =====

    /**
     * Retourne les utilisateurs de ce groupe
     * @return Utilisateur[]
     */
    public function utilisateurs(): array
    {
        return $this->hasMany(Utilisateur::class, 'id_GU', 'id_GU');
    }

    /**
     * Retourne les permissions rattachées à ce groupe
     * @return Rattacher[]
     */
    public function rattachements(): array
    {
        return $this->hasMany(Rattacher::class, 'id_GU', 'id_GU');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un groupe par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_GU' => $libelle]);
    }

    /**
     * Retourne tous les groupes ordonnés par niveau
     * @return self[]
     */
    public static function ordonnes(): array
    {
        $sql = "SELECT id_GU, lib_GU, description, niveau_hierarchique 
                FROM groupe_utilisateur ORDER BY niveau_hierarchique DESC, lib_GU";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES HELPER =====

    /**
     * Retourne le libellé du groupe
     */
    public function getLibelle(): string
    {
        return $this->lib_GU ?? '';
    }

    /**
     * Retourne le niveau hiérarchique
     */
    public function getNiveauHierarchique(): int
    {
        return (int) ($this->niveau_hierarchique ?? 0);
    }

    /**
     * Compte les utilisateurs actifs dans ce groupe
     */
    public function compterUtilisateursActifs(): int
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs 
                WHERE id_GU = :id AND statut_utilisateur = 'Actif'";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
