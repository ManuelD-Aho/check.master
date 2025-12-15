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

    /**
     * Trouve une entreprise par son nom
     */
    public static function findByNom(string $nom): ?self
    {
        return self::firstWhere(['nom_entreprise' => $nom]);
    }

    /**
     * Vérifie si l'entreprise est active
     */
    public function estActive(): bool
    {
        return (bool) $this->actif;
    }

    /**
     * Retourne les candidatures associées à cette entreprise
     */
    public function getCandidatures(): array
    {
        $sql = "SELECT * FROM candidatures WHERE entreprise_id = :id ORDER BY date_soumission DESC";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Compte le nombre de stagiaires accueillis
     */
    public function nombreStagiaires(): int
    {
        $sql = "SELECT COUNT(DISTINCT c.dossier_id) FROM candidatures c
                WHERE c.entreprise_id = :id AND c.validee_scolarite = 1";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne toutes les entreprises actives
     *
     * @return self[]
     */
    public static function actives(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Recherche d'entreprises
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM entreprises 
                WHERE actif = 1 AND (
                    nom_entreprise LIKE :terme OR 
                    secteur_activite LIKE :terme
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

    /**
     * Retourne les entreprises par secteur
     *
     * @return self[]
     */
    public static function parSecteur(string $secteur): array
    {
        return self::where(['secteur_activite' => $secteur, 'actif' => true]);
    }
}
