<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RapportEtudiant
 * 
 * Représente un rapport de mémoire étudiant.
 * Table: rapports_etudiants
 */
class RapportEtudiant extends Model
{
    protected string $table = 'rapports_etudiants';
    protected string $primaryKey = 'id_rapport';
    protected array $fillable = [
        'dossier_id',
        'titre',
        'contenu_html',
        'version',
        'statut',
        'date_depot',
        'chemin_fichier',
        'hash_fichier',
    ];

    /**
     * Statuts possibles
     */
    public const STATUT_BROUILLON = 'Brouillon';
    public const STATUT_SOUMIS = 'Soumis';
    public const STATUT_EN_EVALUATION = 'En_evaluation';
    public const STATUT_VALIDE = 'Valide';
    public const STATUT_REJETE = 'Rejete';

    // ===== RELATIONS =====

    /**
     * Retourne le dossier étudiant
     */
    public function dossier(): ?DossierEtudiant
    {
        return $this->belongsTo(DossierEtudiant::class, 'dossier_id', 'id_dossier');
    }

    /**
     * Retourne les annotations du rapport
     * @return AnnotationRapport[]
     */
    public function annotations(): array
    {
        return $this->hasMany(AnnotationRapport::class, 'rapport_id', 'id_rapport');
    }

    /**
     * Retourne les votes de commission
     * @return CommissionVote[]
     */
    public function votesCommission(): array
    {
        return $this->hasMany(CommissionVote::class, 'rapport_id', 'id_rapport');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve le rapport actuel d'un dossier (dernière version)
     */
    public static function actuelPourDossier(int $dossierId): ?self
    {
        $sql = "SELECT * FROM rapports_etudiants 
                WHERE dossier_id = :id 
                ORDER BY version DESC 
                LIMIT 1";

        $stmt = self::raw($sql, ['id' => $dossierId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $model = new self($row);
        $model->exists = true;
        return $model;
    }

    /**
     * Retourne toutes les versions d'un dossier
     * @return self[]
     */
    public static function versionsParDossier(int $dossierId): array
    {
        $sql = "SELECT * FROM rapports_etudiants 
                WHERE dossier_id = :id 
                ORDER BY version DESC";

        $stmt = self::raw($sql, ['id' => $dossierId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les rapports par statut
     * @return self[]
     */
    public static function parStatut(string $statut): array
    {
        return self::where(['statut' => $statut]);
    }

    /**
     * Retourne les rapports en attente d'évaluation
     * @return self[]
     */
    public static function enAttenteEvaluation(): array
    {
        return self::where(['statut' => self::STATUT_SOUMIS]);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le rapport est en brouillon
     */
    public function estBrouillon(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }

    /**
     * Vérifie si le rapport est soumis
     */
    public function estSoumis(): bool
    {
        return $this->statut === self::STATUT_SOUMIS;
    }

    /**
     * Vérifie si le rapport est en évaluation
     */
    public function estEnEvaluation(): bool
    {
        return $this->statut === self::STATUT_EN_EVALUATION;
    }

    /**
     * Vérifie si le rapport est validé
     */
    public function estValide(): bool
    {
        return $this->statut === self::STATUT_VALIDE;
    }

    /**
     * Vérifie si le rapport est rejeté
     */
    public function estRejete(): bool
    {
        return $this->statut === self::STATUT_REJETE;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Soumet le rapport
     */
    public function soumettre(): void
    {
        $this->statut = self::STATUT_SOUMIS;
        $this->date_depot = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Passe en évaluation
     */
    public function passerEnEvaluation(): void
    {
        $this->statut = self::STATUT_EN_EVALUATION;
        $this->save();
    }

    /**
     * Valide le rapport
     */
    public function valider(): void
    {
        $this->statut = self::STATUT_VALIDE;
        $this->save();
    }

    /**
     * Rejette le rapport
     */
    public function rejeter(): void
    {
        $this->statut = self::STATUT_REJETE;
        $this->save();
    }

    /**
     * Crée une nouvelle version du rapport
     */
    public function creerNouvelleVersion(): self
    {
        $nouvelleVersion = new self([
            'dossier_id' => $this->dossier_id,
            'titre' => $this->titre,
            'contenu_html' => $this->contenu_html,
            'version' => ($this->version ?? 0) + 1,
            'statut' => self::STATUT_BROUILLON,
        ]);
        $nouvelleVersion->save();
        return $nouvelleVersion;
    }

    /**
     * Calcule le hash SHA256 du fichier
     */
    public function calculerHash(): ?string
    {
        if (empty($this->chemin_fichier) || !file_exists($this->chemin_fichier)) {
            return null;
        }
        return hash_file('sha256', $this->chemin_fichier);
    }

    /**
     * Vérifie l'intégrité du fichier
     */
    public function verifierIntegrite(): bool
    {
        $hashActuel = $this->calculerHash();
        if ($hashActuel === null) {
            return false;
        }
        return $hashActuel === $this->hash_fichier;
    }

    /**
     * Compte les annotations du rapport
     */
    public function nombreAnnotations(): int
    {
        return AnnotationRapport::count(['rapport_id' => $this->getId()]);
    }

    /**
     * Retourne le résultat des votes (si évalué)
     */
    public function getResultatVotes(): array
    {
        $sql = "SELECT decision, COUNT(*) as total
                FROM votes_commission
                WHERE rapport_id = :id
                GROUP BY decision";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
