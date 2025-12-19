<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle AnneeAcademique
 * 
 * Représente une année académique.
 * Table: annee_academique
 */
class AnneeAcademique extends Model
{
    protected string $table = 'annee_academique';
    protected string $primaryKey = 'id_annee_acad';
    protected array $fillable = [
        'lib_annee_acad',
        'date_debut',
        'date_fin',
        'est_active',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne les semestres de cette année
     * @return Semestre[]
     */
    public function semestres(): array
    {
        return $this->hasMany(Semestre::class, 'annee_acad_id', 'id_annee_acad');
    }

    /**
     * Retourne les dossiers étudiants de cette année
     * @return DossierEtudiant[]
     */
    public function dossiersEtudiants(): array
    {
        return $this->hasMany(DossierEtudiant::class, 'annee_acad_id', 'id_annee_acad');
    }

    /**
     * Retourne les paiements de cette année
     * @return Paiement[]
     */
    public function paiements(): array
    {
        return $this->hasMany(Paiement::class, 'annee_acad_id', 'id_annee_acad');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par libellé
     */
    public static function findByLibelle(string $libelle): ?self
    {
        return self::firstWhere(['lib_annee_acad' => $libelle]);
    }

    /**
     * Retourne l'année académique active
     */
    public static function active(): ?self
    {
        return self::firstWhere(['est_active' => true]);
    }

    /**
     * Retourne toutes les années ordonnées
     * @return self[]
     */
    public static function ordonnees(): array
    {
        $sql = "SELECT * FROM annee_academique ORDER BY date_debut DESC";
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
     * Vérifie si l'année est active
     */
    public function estActive(): bool
    {
        return (bool) $this->est_active;
    }

    /**
     * Vérifie si on est actuellement dans cette année
     */
    public function estEnCours(): bool
    {
        $now = time();
        $debut = strtotime($this->date_debut);
        $fin = strtotime($this->date_fin);
        return $now >= $debut && $now <= $fin;
    }

    /**
     * Vérifie si l'année est passée
     */
    public function estPassee(): bool
    {
        return strtotime($this->date_fin) < time();
    }

    /**
     * Vérifie si l'année est future
     */
    public function estFuture(): bool
    {
        return strtotime($this->date_debut) > time();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Active cette année (et désactive les autres)
     */
    public function activer(): void
    {
        // Désactiver toutes les autres années
        $sql = "UPDATE annee_academique SET est_active = 0";
        self::raw($sql, []);

        // Activer celle-ci
        $this->est_active = true;
        $this->save();
    }

    /**
     * Désactive cette année
     */
    public function desactiver(): void
    {
        $this->est_active = false;
        $this->save();
    }

    /**
     * Retourne la durée en jours
     */
    public function getDureeJours(): int
    {
        $debut = new \DateTime($this->date_debut);
        $fin = new \DateTime($this->date_fin);
        return $debut->diff($fin)->days;
    }

    /**
     * Compte les dossiers étudiants
     */
    public function nombreDossiers(): int
    {
        return DossierEtudiant::count(['annee_acad_id' => $this->getId()]);
    }

    /**
     * Compte les dossiers par état
     */
    public function statistiquesDossiersParEtat(): array
    {
        $sql = "SELECT we.code_etat, we.nom_etat, COUNT(de.id_dossier) as total
                FROM workflow_etats we
                LEFT JOIN dossiers_etudiants de ON de.etat_actuel_id = we.id_etat 
                    AND de.annee_acad_id = :id
                GROUP BY we.id_etat, we.code_etat, we.nom_etat
                ORDER BY we.ordre_affichage";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Total des paiements de l'année
     */
    public function totalPaiements(): float
    {
        $sql = "SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE annee_acad_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (float) $stmt->fetchColumn();
    }

    /**
     * Génère le libellé pour l'année suivante
     */
    public function genererLibelleSuivante(): string
    {
        // Parse le libellé actuel (format: 2024-2025)
        if (preg_match('/(\d{4})-(\d{4})/', $this->lib_annee_acad, $matches)) {
            $debut = (int) $matches[1] + 1;
            $fin = (int) $matches[2] + 1;
            return "{$debut}-{$fin}";
        }
        return '';
    }
}
