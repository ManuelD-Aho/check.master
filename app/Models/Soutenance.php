<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Soutenance
 * 
 * Représente une soutenance de stage/mémoire.
 * Table: soutenances
 */
class Soutenance extends Model
{
    protected string $table = 'soutenances';
    protected string $primaryKey = 'id_soutenance';
    protected array $fillable = [
        'dossier_id',
        'date_soutenance',
        'heure_debut',
        'heure_fin',
        'lieu',
        'salle_id',
        'duree_minutes',
        'statut',
        'note_finale',
        'mention',
        'notes_finalisees',
        'finalisee_le',
        'finalisee_par',
        'pv_genere',
        'pv_chemin',
        'chemin_pv',
        'planifiee_par',
        'demarree_le',
        'demarree_par',
        'terminee_le',
        'terminee_par',
        'motif_report',
        'reportee_par',
        'reportee_le',
        'motif_annulation',
        'rappel_j7_envoye',
        'rappel_j1_envoye',
        'rappel_jour_j_envoye',
        'corrections_demandees',
        'corrections_liste',
        'corrections_delai',
        'corrections_demandees_par',
        'corrections_validees',
        'corrections_validees_le',
        'corrections_validees_par',
    ];

    /**
     * Statuts de soutenance
     */
    public const STATUT_PLANIFIEE = 'Planifiee';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_TERMINEE = 'Terminee';
    public const STATUT_ANNULEE = 'Annulee';
    public const STATUT_REPORTEE = 'Reportee';

    /**
     * Mentions
     */
    public const MENTION_PASSABLE = 'Passable';
    public const MENTION_ASSEZ_BIEN = 'Assez Bien';
    public const MENTION_BIEN = 'Bien';
    public const MENTION_TRES_BIEN = 'Très Bien';
    public const MENTION_EXCELLENT = 'Excellent';

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
     * Trouve la soutenance d'un dossier
     */
    public static function findByDossier(int $dossierId): ?self
    {
        return self::firstWhere(['dossier_id' => $dossierId]);
    }

    /**
     * Retourne la salle
     */
    public function getSalle(): ?object
    {
        if ($this->salle_id === null) {
            return null;
        }

        $sql = "SELECT * FROM salles WHERE id_salle = :id";
        $stmt = self::raw($sql, ['id' => $this->salle_id]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne les membres du jury
     */
    public function getJury(): array
    {
        $sql = "SELECT jm.*, e.nom_ens, e.prenom_ens, e.email_ens, g.lib_grade
                FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                LEFT JOIN grades g ON g.id_grade = e.grade_id
                WHERE jm.dossier_id = :dossier_id";

        $stmt = self::raw($sql, ['dossier_id' => $this->dossier_id]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Démarre la soutenance
     */
    public function demarrer(): void
    {
        $this->statut = self::STATUT_EN_COURS;
        $this->save();
    }

    /**
     * Termine la soutenance
     */
    public function terminer(): void
    {
        $this->statut = self::STATUT_TERMINEE;
        $this->save();
    }

    /**
     * Annule la soutenance
     */
    public function annuler(): void
    {
        $this->statut = self::STATUT_ANNULEE;
        $this->save();
    }

    /**
     * Reporte la soutenance
     */
    public function reporter(\DateTime $nouvelleDateHeure, ?string $nouveauLieu = null): void
    {
        $this->statut = self::STATUT_REPORTEE;
        $this->date_soutenance = $nouvelleDateHeure->format('Y-m-d H:i:s');
        if ($nouveauLieu) {
            $this->lieu = $nouveauLieu;
        }
        $this->save();
    }

    /**
     * Calcule la mention à partir de la note
     */
    public static function calculerMention(float $note): string
    {
        return match (true) {
            $note >= 18 => self::MENTION_EXCELLENT,
            $note >= 16 => self::MENTION_TRES_BIEN,
            $note >= 14 => self::MENTION_BIEN,
            $note >= 12 => self::MENTION_ASSEZ_BIEN,
            default => self::MENTION_PASSABLE,
        };
    }

    /**
     * Retourne les soutenances d'une date
     *
     * @return self[]
     */
    public static function parDate(string $date): array
    {
        return self::where(['date_soutenance' => $date]);
    }

    /**
     * Retourne les soutenances planifiées
     *
     * @return self[]
     */
    public static function planifiees(): array
    {
        return self::where(['statut' => self::STATUT_PLANIFIEE]);
    }

    /**
     * Retourne les soutenances du jour
     */
    public static function aujourdhui(): array
    {
        return self::parDate(date('Y-m-d'));
    }

    /**
     * Marque le PV comme généré
     */
    public function marquerPvGenere(string $chemin): void
    {
        $this->pv_genere = true;
        $this->pv_chemin = $chemin;
        $this->save();
    }
}
