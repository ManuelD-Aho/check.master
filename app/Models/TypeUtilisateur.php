<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle TypeUtilisateur
 * 
 * Représente un type d'utilisateur du système.
 * Table: type_utilisateur
 */
class TypeUtilisateur extends Model
{
    protected string $table = 'type_utilisateur';
    protected string $primaryKey = 'id_type_utilisateur';
    protected array $fillable = [
        'lib_type_utilisateur',
        'description',
    ];

    /**
     * Types prédéfinis
     */
    public const TYPE_ETUDIANT = 'Etudiant';
    public const TYPE_ENSEIGNANT = 'Enseignant';
    public const TYPE_PERSONNEL = 'Personnel';
    public const TYPE_ADMINISTRATEUR = 'Administrateur';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un type par son libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_type_utilisateur' => $libelle]);
    }

    /**
     * Retourne tous les types
     * @return self[]
     */
    public static function tous(): array
    {
        $sql = "SELECT * FROM type_utilisateur ORDER BY lib_type_utilisateur ASC";
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
     * Retourne les utilisateurs de ce type
     * @return Utilisateur[]
     */
    public function utilisateurs(): array
    {
        return $this->hasMany(Utilisateur::class, 'id_type_utilisateur', 'id_type_utilisateur');
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée un nouveau type
     */
    public static function creer(string $libelle, ?string $description = null): self
    {
        $type = new self([
            'lib_type_utilisateur' => $libelle,
            'description' => $description,
        ]);
        $type->save();
        return $type;
    }

    /**
     * Retourne le type étudiant
     */
    public static function etudiant(): ?self
    {
        return self::findByLibelle(self::TYPE_ETUDIANT);
    }

    /**
     * Retourne le type enseignant
     */
    public static function enseignant(): ?self
    {
        return self::findByLibelle(self::TYPE_ENSEIGNANT);
    }

    /**
     * Retourne le type administrateur
     */
    public static function administrateur(): ?self
    {
        return self::findByLibelle(self::TYPE_ADMINISTRATEUR);
    }
}
