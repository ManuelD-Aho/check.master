<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle GroupeUtilisateur
 * 
 * Représente un groupe d'utilisateurs pour la gestion des droits.
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
     * Groupes prédéfinis
     */
    public const GROUPE_ADMIN = 'Administrateur';
    public const GROUPE_SCOLARITE = 'Scolarité';
    public const GROUPE_COMMUNICATION = 'Communication';
    public const GROUPE_COMMISSION = 'Commission';
    public const GROUPE_ENSEIGNANT = 'Enseignant';
    public const GROUPE_ETUDIANT = 'Etudiant';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un groupe par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_GU' => $libelle]);
    }

    /**
     * Retourne tous les groupes triés par niveau hiérarchique
     * @return self[]
     */
    public static function triesParNiveau(): array
    {
        $sql = "SELECT * FROM groupe_utilisateur ORDER BY niveau_hierarchique ASC, lib_GU ASC";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== RELATIONS =====

    /**
     * Retourne tous les utilisateurs de ce groupe (rôle principal)
     * @return Utilisateur[]
     */
    public function utilisateurs(): array
    {
        return Utilisateur::where(['id_GU' => $this->getId()]);
    }

    /**
     * Retourne les permissions de ce groupe
     * @return Rattacher[]
     */
    public function permissions(): array
    {
        return Rattacher::pourGroupe($this->getId());
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau groupe
     */
    public static function creer(
        string $libelle,
        ?string $description = null,
        int $niveau = 0
    ): self {
        $groupe = new self([
            'lib_GU' => $libelle,
            'description' => $description,
            'niveau_hierarchique' => $niveau,
        ]);
        $groupe->save();
        return $groupe;
    }

    /**
     * Vérifie si le groupe a une permission sur un traitement
     */
    public function aPermission(int $traitementId, int $actionId): bool
    {
        return Rattacher::aPermission($this->getId(), $traitementId, $actionId);
    }

    /**
     * Vérifie si ce groupe a un niveau supérieur à un autre
     */
    public function estSuperieurA(self $autre): bool
    {
        return (int) $this->niveau_hierarchique < (int) $autre->niveau_hierarchique;
    }

    /**
     * Compte les utilisateurs dans ce groupe
     */
    public function nombreUtilisateurs(): int
    {
        return Utilisateur::count(['id_GU' => $this->getId()]);
    }

    /**
     * Retourne le groupe administrateur
     */
    public static function administrateur(): ?self
    {
        return self::findByLibelle(self::GROUPE_ADMIN);
    }

    /**
     * Retourne le groupe scolarité
     */
    public static function scolarite(): ?self
    {
        return self::findByLibelle(self::GROUPE_SCOLARITE);
    }
}
