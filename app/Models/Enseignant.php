<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Enseignant
 * 
 * Représente un enseignant dans le système.
 * Table: enseignants
 */
class Enseignant extends Model
{
    protected string $table = 'enseignants';
    protected string $primaryKey = 'id_enseignant';
    protected array $fillable = [
        'nom_ens',
        'prenom_ens',
        'email_ens',
        'telephone_ens',
        'grade_id',
        'fonction_id',
        'specialite_id',
        'actif',
    ];

    /**
     * Trouve un enseignant par son email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email_ens' => $email]);
    }

    /**
     * Retourne le nom complet
     */
    public function getNomComplet(): string
    {
        return trim($this->prenom_ens . ' ' . $this->nom_ens);
    }

    /**
     * Retourne le nom complet avec titre (grade)
     */
    public function getNomCompletAvecTitre(): string
    {
        $grade = $this->getGrade();
        $titre = $grade ? $grade->lib_grade . ' ' : '';
        return $titre . $this->getNomComplet();
    }

    /**
     * Vérifie si l'enseignant est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    /**
     * Retourne le grade de l'enseignant
     */
    public function getGrade(): ?object
    {
        if ($this->grade_id === null) {
            return null;
        }

        $sql = "SELECT * FROM grades WHERE id_grade = :id";
        $stmt = self::raw($sql, ['id' => $this->grade_id]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne la spécialité de l'enseignant
     */
    public function getSpecialite(): ?object
    {
        if ($this->specialite_id === null) {
            return null;
        }

        $sql = "SELECT * FROM specialites WHERE id_specialite = :id";
        $stmt = self::raw($sql, ['id' => $this->specialite_id]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne la fonction de l'enseignant
     */
    public function getFonction(): ?object
    {
        if ($this->fonction_id === null) {
            return null;
        }

        $sql = "SELECT * FROM fonctions WHERE id_fonction = :id";
        $stmt = self::raw($sql, ['id' => $this->fonction_id]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne les jurys où l'enseignant est membre
     */
    public function getJurys(?string $statut = null): array
    {
        $sql = "SELECT jm.*, de.*, s.date_soutenance
                FROM jury_membres jm
                INNER JOIN dossiers_etudiants de ON de.id_dossier = jm.dossier_id
                LEFT JOIN soutenances s ON s.dossier_id = de.id_dossier
                WHERE jm.enseignant_id = :id";

        $params = ['id' => $this->getId()];

        if ($statut !== null) {
            $sql .= " AND jm.statut_acceptation = :statut";
            $params['statut'] = $statut;
        }

        $sql .= " ORDER BY s.date_soutenance DESC";

        $stmt = self::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Compte les invitations en attente
     */
    public function invitationsEnAttente(): int
    {
        $sql = "SELECT COUNT(*) FROM jury_membres 
                WHERE enseignant_id = :id AND statut_acceptation = 'Invite'";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne tous les enseignants actifs
     *
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Recherche d'enseignants
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM enseignants 
                WHERE actif = 1 AND (
                    nom_ens LIKE :terme OR 
                    prenom_ens LIKE :terme OR 
                    email_ens LIKE :terme
                )
                ORDER BY nom_ens, prenom_ens
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
     * Retourne les enseignants par spécialité
     *
     * @return self[]
     */
    public static function parSpecialite(int $specialiteId): array
    {
        return self::where(['specialite_id' => $specialiteId, 'actif' => true]);
    }

    /**
     * Retourne les enseignants par grade
     *
     * @return self[]
     */
    public static function parGrade(int $gradeId): array
    {
        return self::where(['grade_id' => $gradeId, 'actif' => true]);
    }

    /**
     * Vérifie si l'enseignant est disponible à une date
     */
    public function estDisponible(\DateTime $date): bool
    {
        $sql = "SELECT COUNT(*) FROM soutenances s
                INNER JOIN jury_membres jm ON jm.dossier_id = s.dossier_id
                WHERE jm.enseignant_id = :id 
                AND jm.statut_acceptation = 'Accepte'
                AND DATE(s.date_soutenance) = :date
                AND s.statut NOT IN ('Annulee', 'Reportee')";

        $stmt = self::raw($sql, [
            'id' => $this->getId(),
            'date' => $date->format('Y-m-d'),
        ]);

        return (int) $stmt->fetchColumn() === 0;
    }
}
