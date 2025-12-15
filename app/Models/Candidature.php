<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Candidature
 * 
 * Représente une candidature de stage/mémoire.
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
     * Retourne l'entreprise associée
     */
    public function getEntreprise(): ?Entreprise
    {
        if ($this->entreprise_id === null) {
            return null;
        }
        return Entreprise::find((int) $this->entreprise_id);
    }

    /**
     * Valide la candidature par la scolarité
     */
    public function validerScolarite(): void
    {
        $this->validee_scolarite = true;
        $this->date_valid_scolarite = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Valide la candidature par la communication
     */
    public function validerCommunication(): void
    {
        $this->validee_communication = true;
        $this->date_valid_communication = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Vérifie si la candidature est complètement validée
     */
    public function estValidee(): bool
    {
        return (bool) $this->validee_scolarite && (bool) $this->validee_communication;
    }

    /**
     * Retourne les candidatures en attente de validation scolarité
     *
     * @return self[]
     */
    public static function enAttenteScolarite(): array
    {
        return self::where(['validee_scolarite' => false]);
    }

    /**
     * Retourne les candidatures en attente de validation communication
     *
     * @return self[]
     */
    public static function enAttenteCommunication(): array
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
        $sql = "SELECT c.*, de.etudiant_id, e.nom_etu, e.prenom_etu
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

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
