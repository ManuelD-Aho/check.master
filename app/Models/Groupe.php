<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Groupe
 * 
 * Représente un groupe d'utilisateurs (pour permissions alternatives).
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

    // ===== RELATIONS =====

    /**
     * Retourne les associations utilisateurs-groupes
     * @return UtilisateurGroupe[]
     */
    public function utilisateursGroupes(): array
    {
        return $this->hasMany(UtilisateurGroupe::class, 'groupe_id', 'id_groupe');
    }

    /**
     * Retourne les permissions du groupe
     * @return Permission[]
     */
    public function permissions(): array
    {
        return $this->hasMany(Permission::class, 'groupe_id', 'id_groupe');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un groupe par son nom
     */
    public static function findByNom(string $nom): ?self
    {
        return self::firstWhere(['nom_groupe' => $nom]);
    }

    /**
     * Retourne tous les groupes actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les groupes ordonnés par niveau hiérarchique
     * @return self[]
     */
    public static function parNiveauHierarchique(): array
    {
        $sql = "SELECT * FROM groupes WHERE actif = 1 ORDER BY niveau_hierarchique DESC, nom_groupe";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le groupe est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les utilisateurs du groupe
     * @return Utilisateur[]
     */
    public function getUtilisateurs(): array
    {
        $sql = "SELECT u.* FROM utilisateurs u
                INNER JOIN utilisateurs_groupes ug ON u.id_utilisateur = ug.utilisateur_id
                WHERE ug.groupe_id = :id AND u.statut_utilisateur = 'Actif'";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Utilisateur($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Compte les utilisateurs du groupe
     */
    public function compterUtilisateurs(): int
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs_groupes WHERE groupe_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ajoute un utilisateur au groupe
     */
    public function ajouterUtilisateur(int $utilisateurId, ?int $attribueParId = null): bool
    {
        // Vérifier si déjà membre
        $sql = "SELECT COUNT(*) FROM utilisateurs_groupes 
                WHERE utilisateur_id = :uid AND groupe_id = :gid";
        $stmt = self::raw($sql, ['uid' => $utilisateurId, 'gid' => $this->getId()]);

        if ((int) $stmt->fetchColumn() > 0) {
            return false; // Déjà membre
        }

        $ug = new UtilisateurGroupe([
            'utilisateur_id' => $utilisateurId,
            'groupe_id' => $this->getId(),
            'attribue_par' => $attribueParId,
            'attribue_le' => date('Y-m-d H:i:s'),
        ]);
        return $ug->save();
    }

    /**
     * Retire un utilisateur du groupe
     */
    public function retirerUtilisateur(int $utilisateurId): bool
    {
        $sql = "DELETE FROM utilisateurs_groupes 
                WHERE utilisateur_id = :uid AND groupe_id = :gid";
        $stmt = self::raw($sql, ['uid' => $utilisateurId, 'gid' => $this->getId()]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Vérifie si un utilisateur est membre du groupe
     */
    public function contientUtilisateur(int $utilisateurId): bool
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs_groupes 
                WHERE utilisateur_id = :uid AND groupe_id = :gid";
        $stmt = self::raw($sql, ['uid' => $utilisateurId, 'gid' => $this->getId()]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Active le groupe
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive le groupe
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }
}
