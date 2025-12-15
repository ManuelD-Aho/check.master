<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle GroupeUtilisateur
 * 
 * Table pivot entre utilisateurs et groupes.
 * Table: utilisateurs_groupes
 */
class GroupeUtilisateur extends Model
{
    protected string $table = 'utilisateurs_groupes';
    protected string $primaryKey = 'utilisateur_id'; // Composite key
    protected array $fillable = [
        'utilisateur_id',
        'groupe_id',
        'attribue_par',
        'attribue_le',
    ];

    /**
     * Assigne un utilisateur à un groupe
     */
    public static function assigner(int $utilisateurId, int $groupeId, ?int $attribuePar = null): bool
    {
        // Vérifier si l'assignation existe déjà
        if (self::existe($utilisateurId, $groupeId)) {
            return true;
        }

        $pivot = new self([
            'utilisateur_id' => $utilisateurId,
            'groupe_id' => $groupeId,
            'attribue_par' => $attribuePar,
            'attribue_le' => date('Y-m-d H:i:s'),
        ]);

        return $pivot->save();
    }

    /**
     * Retire un utilisateur d'un groupe
     */
    public static function retirer(int $utilisateurId, int $groupeId): bool
    {
        $sql = "DELETE FROM utilisateurs_groupes 
                WHERE utilisateur_id = :user_id AND groupe_id = :groupe_id";

        $stmt = self::raw($sql, [
            'user_id' => $utilisateurId,
            'groupe_id' => $groupeId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Vérifie si une assignation existe
     */
    public static function existe(int $utilisateurId, int $groupeId): bool
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs_groupes 
                WHERE utilisateur_id = :user_id AND groupe_id = :groupe_id";

        $stmt = self::raw($sql, [
            'user_id' => $utilisateurId,
            'groupe_id' => $groupeId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Retourne tous les groupes d'un utilisateur
     *
     * @return Groupe[]
     */
    public static function groupesUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT g.* FROM groupes g
                INNER JOIN utilisateurs_groupes ug ON ug.groupe_id = g.id_groupe
                WHERE ug.utilisateur_id = :user_id AND g.actif = 1";

        $stmt = self::raw($sql, ['user_id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Groupe($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Synchronise les groupes d'un utilisateur
     *
     * @param int $utilisateurId
     * @param int[] $groupeIds
     * @param int|null $attribuePar
     */
    public static function synchroniser(int $utilisateurId, array $groupeIds, ?int $attribuePar = null): void
    {
        // Supprimer toutes les assignations actuelles
        $sql = "DELETE FROM utilisateurs_groupes WHERE utilisateur_id = :user_id";
        self::raw($sql, ['user_id' => $utilisateurId]);

        // Ajouter les nouvelles assignations
        foreach ($groupeIds as $groupeId) {
            self::assigner($utilisateurId, $groupeId, $attribuePar);
        }
    }
}
