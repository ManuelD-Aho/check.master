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

    // ===== RELATIONS =====

    /**
     * Retourne les dossiers de l'étudiant
     * @return DossierEtudiant[]
     */
    public function dossiers(): array
    {
        return $this->hasMany(DossierEtudiant::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne les paiements de l'étudiant
     * @return Paiement[]
     */
    public function paiements(): array
    {
        return $this->hasMany(Paiement::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne les pénalités de l'étudiant
     * @return Penalite[]
     */
    public function penalites(): array
    {
        return $this->hasMany(Penalite::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne les exonérations de l'étudiant
     * @return Exoneration[]
     */
    public function exonerations(): array
    {
        return $this->hasMany(Exoneration::class, 'etudiant_id', 'id_etudiant');
    }

    // ===== MÉTHODES DE RECHERCHE =====

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
     * Retourne tous les étudiants actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['actif' => true]);
    }

    /**
     * Retourne les étudiants d'une promotion
     * @return self[]
     */
    public static function parPromotion(string $promotion): array
    {
        return self::where(['promotion_etu' => $promotion, 'actif' => true]);
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

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si l'étudiant est actif
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
        return trim($this->prenom_etu . ' ' . $this->nom_etu);
    }

    /**
     * Retourne le nom formel (NOM Prénom)
     */
    public function getNomFormel(): string
    {
        return trim(strtoupper($this->nom_etu ?? '') . ' ' . $this->prenom_etu);
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

    // ===== MÉTHODES DOSSIER =====

    /**
     * Retourne le dossier de l'étudiant pour une année académique
     */
    public function getDossier(?int $anneeAcadId = null): ?DossierEtudiant
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
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new DossierEtudiant($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne le dossier de l'année académique active
     */
    public function getDossierActif(): ?DossierEtudiant
    {
        $anneeActive = AnneeAcademique::active();
        if ($anneeActive === null) {
            return null;
        }
        return $this->getDossier($anneeActive->getId());
    }

    // ===== MÉTHODES FINANCIÈRES =====

    /**
     * Retourne les paiements pour une année
     * @return Paiement[]
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
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Paiement($row);
            $model->exists = true;
            return $model;
        }, $rows);
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
     * @return Penalite[]
     */
    public function getPenalitesImpayees(): array
    {
        $sql = "SELECT * FROM penalites 
                WHERE etudiant_id = :id AND payee = 0 
                ORDER BY date_application DESC";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new Penalite($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Calcule le total des pénalités impayées
     */
    public function totalPenalitesImpayees(): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) FROM penalites 
                WHERE etudiant_id = :id AND payee = 0";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Retourne le total des exonérations pour une année
     */
    public function totalExonerations(?int $anneeAcadId = null): float
    {
        $sql = "SELECT COALESCE(SUM(montant_exonere), 0) FROM exonerations WHERE etudiant_id = :id";
        $params = ['id' => $this->getId()];

        if ($anneeAcadId !== null) {
            $sql .= " AND annee_acad_id = :annee";
            $params['annee'] = $anneeAcadId;
        }

        $stmt = self::raw($sql, $params);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Vérifie si l'étudiant est à jour financièrement
     */
    public function estAJourFinancierement(?int $anneeAcadId = null): bool
    {
        return $this->totalPenalitesImpayees() === 0.0;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Active l'étudiant
     */
    public function activer(): void
    {
        $this->actif = true;
        $this->save();
    }

    /**
     * Désactive l'étudiant
     */
    public function desactiver(): void
    {
        $this->actif = false;
        $this->save();
    }

    /**
     * Retourne les promotions distinctes
     */
    public static function getPromotions(): array
    {
        $sql = "SELECT DISTINCT promotion_etu FROM etudiants 
                WHERE promotion_etu IS NOT NULL 
                ORDER BY promotion_etu DESC";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Compte les étudiants par promotion
     */
    public static function statistiquesParPromotion(): array
    {
        $sql = "SELECT promotion_etu, COUNT(*) as total, 
                       SUM(CASE WHEN actif = 1 THEN 1 ELSE 0 END) as actifs
                FROM etudiants 
                WHERE promotion_etu IS NOT NULL 
                GROUP BY promotion_etu 
                ORDER BY promotion_etu DESC";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
