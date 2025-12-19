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

    // ===== RELATIONS =====

    /**
     * Retourne le grade
     */
    public function grade(): ?Grade
    {
        if ($this->grade_id === null) {
            return null;
        }
        return $this->belongsTo(Grade::class, 'grade_id', 'id_grade');
    }

    /**
     * Retourne la fonction
     */
    public function fonction(): ?Fonction
    {
        if ($this->fonction_id === null) {
            return null;
        }
        return $this->belongsTo(Fonction::class, 'fonction_id', 'id_fonction');
    }

    /**
     * Retourne la spécialité
     */
    public function specialite(): ?Specialite
    {
        if ($this->specialite_id === null) {
            return null;
        }
        return $this->belongsTo(Specialite::class, 'specialite_id', 'id_specialite');
    }

    /**
     * Retourne les votes de commission
     * @return CommissionVote[]
     */
    public function votesCommission(): array
    {
        return $this->hasMany(CommissionVote::class, 'membre_id', 'id_enseignant');
    }

    /**
     * Retourne les annotations de rapport
     * @return RapportAnnotation[]
     */
    public function annotationsRapport(): array
    {
        return $this->hasMany(RapportAnnotation::class, 'auteur_id', 'id_enseignant');
    }

    /**
     * Retourne les participations au jury
     * @return JuryMembre[]
     */
    public function participationsJury(): array
    {
        return $this->hasMany(JuryMembre::class, 'enseignant_id', 'id_enseignant');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un enseignant par son email
     */
    public static function findByEmail(string $email): ?self
    {
        return self::firstWhere(['email_ens' => $email]);
    }

    /**
     * Retourne tous les enseignants actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les enseignants par grade
     * @return self[]
     */
    public static function parGrade(int $gradeId): array
    {
        return self::where(['grade_id' => $gradeId, 'actif' => true]);
    }

    /**
     * Retourne les enseignants par spécialité
     * @return self[]
     */
    public static function parSpecialite(int $specialiteId): array
    {
        return self::where(['specialite_id' => $specialiteId, 'actif' => true]);
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

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si l'enseignant est actif
     */
    public function estActif(): bool
    {
        return (bool) $this->actif;
    }

    // ===== MÉTHODES HELPER =====

    /**
     * Retourne le nom complet
     */
    public function getNomComplet(): string
    {
        return trim($this->prenom_ens . ' ' . $this->nom_ens);
    }

    /**
     * Retourne le nom formel avec grade
     */
    public function getNomFormelAvecGrade(): string
    {
        $grade = $this->grade();
        $prefix = $grade ? $grade->lib_grade . ' ' : '';
        return $prefix . strtoupper($this->nom_ens ?? '') . ' ' . $this->prenom_ens;
    }

    /**
     * Retourne le titre complet
     */
    public function getTitreComplet(): string
    {
        $grade = $this->grade();
        $fonction = $this->fonction();

        $parts = [];
        if ($grade) {
            $parts[] = $grade->lib_grade;
        }
        $parts[] = $this->getNomComplet();
        if ($fonction) {
            $parts[] = '(' . $fonction->lib_fonction . ')';
        }

        return implode(' ', $parts);
    }

    // ===== MÉTHODES COMMISSION =====

    /**
     * Vérifie si l'enseignant est membre de commission
     */
    public function estMembreCommission(): bool
    {
        // Vérifier via le groupe utilisateur
        $sql = "SELECT COUNT(*) FROM utilisateurs u
                INNER JOIN utilisateurs_groupes ug ON u.id_utilisateur = ug.utilisateur_id
                INNER JOIN groupes g ON g.id_groupe = ug.groupe_id
                WHERE u.login_utilisateur = :email 
                AND g.nom_groupe LIKE '%Commission%'";

        $stmt = self::raw($sql, ['email' => $this->email_ens]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Retourne les sessions de commission où l'enseignant a voté
     */
    public function getSessionsCommissionParticipees(): array
    {
        $sql = "SELECT DISTINCT sc.* FROM sessions_commission sc
                INNER JOIN votes_commission vc ON vc.session_id = sc.id_session
                WHERE vc.membre_id = :id
                ORDER BY sc.date_session DESC";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ===== MÉTHODES JURY =====

    /**
     * Retourne les soutenances où l'enseignant est juré
     */
    public function getSoutenancesJury(): array
    {
        $sql = "SELECT s.*, jm.role_jury, jm.statut_acceptation
                FROM soutenances s
                INNER JOIN jury_membres jm ON jm.dossier_id = s.dossier_id
                WHERE jm.enseignant_id = :id
                ORDER BY s.date_soutenance DESC";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie la disponibilité pour une date
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

        // Si l'enseignant a moins de 3 soutenances ce jour, il est disponible
        return (int) $stmt->fetchColumn() < 3;
    }

    /**
     * Compte les soutenances sur une période
     */
    public function nombreSoutenances(\DateTime $debut, \DateTime $fin): int
    {
        $sql = "SELECT COUNT(*) FROM soutenances s
                INNER JOIN jury_membres jm ON jm.dossier_id = s.dossier_id
                WHERE jm.enseignant_id = :id 
                AND jm.statut_acceptation = 'Accepte'
                AND s.date_soutenance BETWEEN :debut AND :fin
                AND s.statut NOT IN ('Annulee', 'Reportee')";

        $stmt = self::raw($sql, [
            'id' => $this->getId(),
            'debut' => $debut->format('Y-m-d H:i:s'),
            'fin' => $fin->format('Y-m-d H:i:s'),
        ]);

        return (int) $stmt->fetchColumn();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Active l'enseignant
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive l'enseignant
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Statistiques par grade
     */
    public static function statistiquesParGrade(): array
    {
        $sql = "SELECT g.lib_grade, COUNT(e.id_enseignant) as total
                FROM grades g
                LEFT JOIN enseignants e ON e.grade_id = g.id_grade AND e.actif = 1
                GROUP BY g.id_grade, g.lib_grade
                ORDER BY g.niveau_hierarchique DESC";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques par spécialité
     */
    public static function statistiquesParSpecialite(): array
    {
        $sql = "SELECT s.lib_specialite, COUNT(e.id_enseignant) as total
                FROM specialites s
                LEFT JOIN enseignants e ON e.specialite_id = s.id_specialite AND e.actif = 1
                GROUP BY s.id_specialite, s.lib_specialite
                ORDER BY total DESC";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
