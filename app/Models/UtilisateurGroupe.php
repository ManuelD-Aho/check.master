<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle UtilisateurGroupe
 * 
 * Table pivot pour la relation utilisateurs-groupes.
 * Table: utilisateurs_groupes
 */
class UtilisateurGroupe extends Model
{
    protected string $table = 'utilisateurs_groupes';
    protected string $primaryKey = 'utilisateur_id'; // Clé composite, gérée spécialement
    protected array $fillable = [
        'utilisateur_id',
        'groupe_id',
        'attribue_par',
        'attribue_le',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne l'utilisateur
     */
    public function utilisateur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne le groupe
     */
    public function groupe(): ?Groupe
    {
        return $this->belongsTo(Groupe::class, 'groupe_id', 'id_groupe');
    }

    /**
     * Retourne l'utilisateur qui a attribué
     */
    public function attribuePar(): ?Utilisateur
    {
        if ($this->attribue_par === null) {
            return null;
        }
        return Utilisateur::find((int) $this->attribue_par);
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve une association spécifique
     */
    public static function trouver(int $utilisateurId, int $groupeId): ?self
    {
        return self::firstWhere([
            'utilisateur_id' => $utilisateurId,
            'groupe_id' => $groupeId,
        ]);
    }

    /**
     * Retourne les groupes d'un utilisateur
     * @return Groupe[]
     */
    public static function groupesPourUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT g.* FROM groupes g
                INNER JOIN utilisateurs_groupes ug ON g.id_groupe = ug.groupe_id
                WHERE ug.utilisateur_id = :id AND g.actif = 1
                ORDER BY g.niveau_hierarchique DESC";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Groupe($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les utilisateurs d'un groupe
     * @return Utilisateur[]
     */
    public static function utilisateursPourGroupe(int $groupeId): array
    {
        $sql = "SELECT u.* FROM utilisateurs u
                INNER JOIN utilisateurs_groupes ug ON u.id_utilisateur = ug.utilisateur_id
                WHERE ug.groupe_id = :id AND u.statut_utilisateur = 'Actif'
                ORDER BY u.nom_utilisateur";

        $stmt = self::raw($sql, ['id' => $groupeId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Utilisateur($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Vérifie si un utilisateur appartient à un groupe
     */
    public static function appartient(int $utilisateurId, int $groupeId): bool
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs_groupes 
                WHERE utilisateur_id = :uid AND groupe_id = :gid";
        $stmt = self::raw($sql, ['uid' => $utilisateurId, 'gid' => $groupeId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Ajoute un utilisateur à un groupe
     */
    public static function ajouter(int $utilisateurId, int $groupeId, ?int $attribueParId = null): bool
    {
        if (self::appartient($utilisateurId, $groupeId)) {
            return false;
        }

        $model = new self([
            'utilisateur_id' => $utilisateurId,
            'groupe_id' => $groupeId,
            'attribue_par' => $attribueParId,
            'attribue_le' => date('Y-m-d H:i:s'),
        ]);
        return $model->save();
    }

    /**
     * Retire un utilisateur d'un groupe
     */
    public static function retirer(int $utilisateurId, int $groupeId): bool
    {
        $sql = "DELETE FROM utilisateurs_groupes 
                WHERE utilisateur_id = :uid AND groupe_id = :gid";
        $stmt = self::raw($sql, ['uid' => $utilisateurId, 'gid' => $groupeId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Remplace tous les groupes d'un utilisateur
     */
    public static function remplacerGroupes(int $utilisateurId, array $groupeIds, ?int $attribueParId = null): void
    {
        // Supprimer les anciennes associations
        $sql = "DELETE FROM utilisateurs_groupes WHERE utilisateur_id = :id";
        self::raw($sql, ['id' => $utilisateurId]);

        // Ajouter les nouvelles associations
        foreach ($groupeIds as $groupeId) {
            self::ajouter($utilisateurId, (int) $groupeId, $attribueParId);
        }
    }
}
