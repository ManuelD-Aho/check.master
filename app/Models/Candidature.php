<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Candidature
 * 
 * Représente une candidature de soutenance.
 * Table: candidatures
 */
class Candidature extends Model
{
    protected string $table = 'candidatures';
    protected string $primaryKey = 'id_candidature';
    protected array $fillable = [
        'dossier_id',
        'theme',
        'entreprise_id',
        'maitre_stage_nom',
        'maitre_stage_email',
        'maitre_stage_tel',
        'date_debut_stage',
        'date_fin_stage',
        'date_soumission',
        'validee_scolarite',
        'date_valid_scolarite',
        'validee_communication',
        'date_valid_communication',
    ];

    // ===== RELATIONS =====

    /**
     * Retourne le dossier étudiant
     */
    public function dossier(): ?DossierEtudiant
    {
        return $this->belongsTo(DossierEtudiant::class, 'dossier_id', 'id_dossier');
    }

    /**
     * Retourne l'entreprise de stage
     */
    public function entreprise(): ?Entreprise
    {
        if ($this->entreprise_id === null) {
            return null;
        }
        return $this->belongsTo(Entreprise::class, 'entreprise_id', 'id_entreprise');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve la candidature d'un dossier
     */
    public static function pourDossier(int $dossierId): ?self
    {
        return self::firstWhere(['dossier_id' => $dossierId]);
    }

    /**
     * Retourne les candidatures non validées par scolarité
     * @return self[]
     */
    public static function attenteValidationScolarite(): array
    {
        return self::where(['validee_scolarite' => false]);
    }

    /**
     * Retourne les candidatures en attente de validation communication
     * @return self[]
     */
    public static function attenteValidationCommunication(): array
    {
        return self::where([
            'validee_scolarite' => true,
            'validee_communication' => false,
        ]);
    }

    /**
     * Recherche par thème
     */
    public static function rechercherParTheme(string $terme, int $limit = 50): array
    {
        $sql = "SELECT c.*, e.nom_etu, e.prenom_etu 
                FROM candidatures c
                INNER JOIN dossiers_etudiants de ON de.id_dossier = c.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                WHERE c.theme LIKE :terme
                ORDER BY c.date_soumission DESC
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('terme', "%{$terme}%", \PDO::PARAM_STR);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si validée par la scolarité
     */
    public function estValideeScolarite(): bool
    {
        return (bool) $this->validee_scolarite;
    }

    /**
     * Vérifie si validée par la communication
     */
    public function estValideeCommunication(): bool
    {
        return (bool) $this->validee_communication;
    }

    /**
     * Vérifie si la candidature est complètement validée
     */
    public function estCompleteValidee(): bool
    {
        return $this->estValideeScolarite() && $this->estValideeCommunication();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Valide par la scolarité
     */
    public function validerScolarite(): void
    {
        $this->validee_scolarite = true;
        $this->date_valid_scolarite = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Rejette par la scolarité
     */
    public function rejeterScolarite(): void
    {
        $this->validee_scolarite = false;
        $this->date_valid_scolarite = null;
        $this->save();
    }

    /**
     * Valide par la communication
     */
    public function validerCommunication(): void
    {
        $this->validee_communication = true;
        $this->date_valid_communication = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Rejette par la communication
     */
    public function rejeterCommunication(): void
    {
        $this->validee_communication = false;
        $this->date_valid_communication = null;
        $this->save();
    }

    /**
     * Calcule la durée du stage en jours
     */
    public function getDureeStageJours(): ?int
    {
        if (empty($this->date_debut_stage) || empty($this->date_fin_stage)) {
            return null;
        }
        $debut = new \DateTime($this->date_debut_stage);
        $fin = new \DateTime($this->date_fin_stage);
        return $debut->diff($fin)->days;
    }

    /**
     * Retourne les informations du maître de stage
     */
    public function getInfosMaitreStage(): array
    {
        return [
            'nom' => $this->maitre_stage_nom,
            'email' => $this->maitre_stage_email,
            'telephone' => $this->maitre_stage_tel,
        ];
    }

    /**
     * Statistiques par entreprise
     */
    public static function statistiquesParEntreprise(): array
    {
        $sql = "SELECT e.nom_entreprise, COUNT(c.id_candidature) as total
                FROM entreprises e
                INNER JOIN candidatures c ON c.entreprise_id = e.id_entreprise
                GROUP BY e.id_entreprise, e.nom_entreprise
                ORDER BY total DESC
                LIMIT 20";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
