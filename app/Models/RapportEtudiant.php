<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RapportEtudiant
 * 
 * Représente un rapport de stage/mémoire.
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
     * Statuts du rapport
     */
    public const STATUT_BROUILLON = 'Brouillon';
    public const STATUT_SOUMIS = 'Soumis';
    public const STATUT_EN_EVALUATION = 'En_evaluation';
    public const STATUT_VALIDE = 'Valide';
    public const STATUT_REJETE = 'Rejete';

    /**
     * Retourne le dossier associé
     */
    public function getDossier(): ?DossierEtudiant
    {
        if ($this->dossier_id === null) {
            return null;
        }
        return DossierEtudiant::find((int) $this->dossier_id);
    }

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
     * Retourne les annotations du rapport
     */
    public function getAnnotations(): array
    {
        $sql = "SELECT a.*, e.nom_ens, e.prenom_ens
                FROM annotations_rapport a
                INNER JOIN enseignants e ON e.id_enseignant = a.auteur_id
                WHERE a.rapport_id = :id
                ORDER BY a.created_at DESC";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Incrémente la version et crée une nouvelle entrée
     */
    public function nouvelleVersion(): self
    {
        $nouveau = new self([
            'dossier_id' => $this->dossier_id,
            'titre' => $this->titre,
            'contenu_html' => $this->contenu_html,
            'version' => ($this->version ?? 1) + 1,
            'statut' => self::STATUT_BROUILLON,
        ]);
        $nouveau->save();
        return $nouveau;
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
        $hash = $this->calculerHash();
        return $hash !== null && $hash === $this->hash_fichier;
    }

    /**
     * Retourne le dernier rapport d'un dossier
     */
    public static function dernierPourDossier(int $dossierId): ?self
    {
        $sql = "SELECT * FROM rapports_etudiants 
                WHERE dossier_id = :id 
                ORDER BY version DESC LIMIT 1";

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
     * Retourne les rapports soumis (en attente d'évaluation)
     *
     * @return self[]
     */
    public static function soumis(): array
    {
        return self::where(['statut' => self::STATUT_SOUMIS]);
    }
}
