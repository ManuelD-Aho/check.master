<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Etudiant
 * 
 * Représente un étudiant dans le système.
 * Table: etudiants
 */
class Etudiant extends Model
{
    protected string $table = 'etudiants';
    protected string $primaryKey = 'id_etudiant';
    protected array $fillable = [
        'num_etu',
        'nom_etu',
        'prenom_etu',
        'email_etu',
        'telephone_etu',
        'date_naiss_etu',
        'lieu_naiss_etu',
        'genre_etu',
        'promotion_etu',
        'actif',
    ];

    /**
     * Genres possibles
     */
    public const GENRE_HOMME = 'Homme';
    public const GENRE_FEMME = 'Femme';
    public const GENRE_AUTRE = 'Autre';

    /**
     * Trouve un étudiant par son numéro
     */
    public static function findByNumero(string $numero): ?self
    {
        return self::firstWhere(['num_etu' => $numero]);
    }

    /**
     * Trouve un étudiant par son email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email_etu' => $email]);
    }

    /**
     * Retourne le nom complet
     */
    public function getNomComplet(): string
    {
        return trim($this->prenom_etu . ' ' . $this->nom_etu);
    }

    /**
     * Vérifie si l'étudiant est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    /**
     * Retourne le dossier de l'étudiant pour une année académique
     */
    public function getDossier(?int $anneeAcadId = null): ?object
    {
        $conditions = ['etudiant_id' => $this->getId()];

        if ($anneeAcadId !== null) {
            $conditions['annee_acad_id'] = $anneeAcadId;
        }

        $sql = "SELECT * FROM dossiers_etudiants 
                WHERE etudiant_id = :id " .
            ($anneeAcadId ? "AND annee_acad_id = :annee" : "") .
            " ORDER BY created_at DESC LIMIT 1";

        $params = ['id' => $this->getId()];
        if ($anneeAcadId) {
            $params['annee'] = $anneeAcadId;
        }

        $stmt = self::raw($sql, $params);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne les paiements de l'étudiant
     */
    public function getPaiements(?int $anneeAcadId = null): array
    {
        $sql = "SELECT * FROM paiements WHERE etudiant_id = :id";
        $params = ['id' => $this->getId()];

        if ($anneeAcadId !== null) {
            $sql .= " AND annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $sql .= " ORDER BY date_paiement DESC";

        $stmt = self::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Calcule le total payé pour une année
     */
    public function totalPaye(?int $anneeAcadId = null): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE etudiant_id = :id";
        $params = ['id' => $this->getId()];

        if ($anneeAcadId !== null) {
            $sql .= " AND annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $stmt = self::raw($sql, $params);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Retourne les pénalités non payées
     */
    public function getPenalitesImpayees(): array
    {
        $sql = "SELECT * FROM penalites 
                WHERE etudiant_id = :id AND payee = 0 
                ORDER BY date_application DESC";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Retourne tous les étudiants actifs
     *
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Recherche d'étudiants
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM etudiants 
                WHERE actif = 1 AND (
                    nom_etu LIKE :terme OR 
                    prenom_etu LIKE :terme OR 
                    num_etu LIKE :terme OR 
                    email_etu LIKE :terme
                )
                ORDER BY nom_etu, prenom_etu
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
     * Retourne l'âge de l'étudiant
     */
    public function getAge(): ?int
    {
        if (empty($this->date_naiss_etu)) {
            return null;
        }

        $birthDate = new \DateTime($this->date_naiss_etu);
        $today = new \DateTime();

        return $birthDate->diff($today)->y;
    }

    /**
     * Retourne les étudiants d'une promotion
     *
     * @return self[]
     */
    public static function parPromotion(string $promotion): array
    {
        return self::where(['promotion_etu' => $promotion, 'actif' => true]);
    }
}
