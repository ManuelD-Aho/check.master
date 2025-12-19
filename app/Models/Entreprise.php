<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Entreprise
 * 
 * Représente une entreprise partenaire pour les stages.
 * Table: entreprises
 */
class Entreprise extends Model
{
    protected string $table = 'entreprises';
    protected string $primaryKey = 'id_entreprise';
    protected array $fillable = [
        'nom_entreprise',
        'secteur_activite',
        'adresse',
        'telephone',
        'email',
        'site_web',
        'actif',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne les candidatures liées à cette entreprise
     * @return Candidature[]
     */
    public function candidatures(): array
    {
        return $this->hasMany(Candidature::class, 'entreprise_id', 'id_entreprise');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par nom
     */
    public static function findByNom(string $nom): ?self
    {
        return self::firstWhere(['nom_entreprise' => $nom]);
    }

    /**
     * Trouve par email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email' => $email]);
    }

    /**
     * Retourne toutes les entreprises actives
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les entreprises par secteur
     * @return self[]
     */
    public static function parSecteur(string $secteur): array
    {
        return self::where(['secteur_activite' => $secteur, 'actif' => true]);
    }

    /**
     * Recherche d'entreprises
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM entreprises 
                WHERE actif = 1 AND (
                    nom_entreprise LIKE :terme OR 
                    secteur_activite LIKE :terme OR
                    adresse LIKE :terme
                )
                ORDER BY nom_entreprise
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

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si l'entreprise est active
     */
    public function estActive(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Retourne les secteurs d'activité distincts
     */
    public static function getSecteurs(): array
    {
        $sql = "SELECT DISTINCT secteur_activite FROM entreprises 
                WHERE secteur_activite IS NOT NULL AND actif = 1 
                ORDER BY secteur_activite";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Compte les candidatures pour cette entreprise
     */
    public function nombreCandidatures(): int
    {
        $sql = "SELECT COUNT(*) FROM candidatures WHERE entreprise_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Active l'entreprise
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive l'entreprise
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Statistiques par secteur
     */
    public static function statistiquesParSecteur(): array
    {
        $sql = "SELECT secteur_activite, COUNT(*) as total
                FROM entreprises
                WHERE actif = 1 AND secteur_activite IS NOT NULL
                GROUP BY secteur_activite
                ORDER BY total DESC";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
