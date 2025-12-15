<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;
use Src\Exceptions\WorkflowException;

/**
 * Modèle DossierEtudiant
 * 
 * Représente le dossier d'un étudiant pour une année académique.
 * Table: dossiers_etudiants
 */
class DossierEtudiant extends Model
{
    protected string $table = 'dossiers_etudiants';
    protected string $primaryKey = 'id_dossier';
    protected array $fillable = [
        'etudiant_id',
        'annee_acad_id',
        'etat_actuel_id',
        'date_entree_etat',
        'date_limite_etat',
    ];

    /**
     * Retourne l'étudiant associé
     */
    public function getEtudiant(): ?Etudiant
    {
        if ($this->etudiant_id === null) {
            return null;
        }
        return Etudiant::find((int) $this->etudiant_id);
    }

    /**
     * Retourne l'année académique
     */
    public function getAnneeAcademique(): ?AnneeAcademique
    {
        if ($this->annee_acad_id === null) {
            return null;
        }
        return AnneeAcademique::find((int) $this->annee_acad_id);
    }

    /**
     * Retourne l'état actuel du workflow
     */
    public function getEtatActuel(): ?WorkflowEtat
    {
        if ($this->etat_actuel_id === null) {
            return null;
        }
        return WorkflowEtat::find((int) $this->etat_actuel_id);
    }

    /**
     * Effectue une transition vers un nouvel état
     *
     * @throws WorkflowException
     */
    public function transitionner(
        string $codeEtatCible,
        ?int $utilisateurId = null,
        ?string $commentaire = null
    ): void {
        $etatActuel = $this->getEtatActuel();

        if ($etatActuel === null) {
            throw new WorkflowException('État actuel non défini');
        }

        $etatCible = WorkflowEtat::findByCode($codeEtatCible);

        if ($etatCible === null) {
            throw WorkflowException::invalidTransition(
                $etatActuel->code_etat,
                $codeEtatCible,
                'État cible inexistant'
            );
        }

        if (!$etatActuel->peutTransitionnerVers($codeEtatCible)) {
            throw WorkflowException::invalidTransition(
                $etatActuel->code_etat,
                $codeEtatCible
            );
        }

        // Trouver la transition
        $transition = WorkflowTransition::trouverTransition(
            $etatActuel->getId(),
            $etatCible->getId()
        );

        // Enregistrer dans l'historique
        WorkflowHistorique::enregistrer(
            $this->getId(),
            $etatActuel->getId(),
            $etatCible->getId(),
            $transition?->getId(),
            $utilisateurId,
            $commentaire,
            $this->toArray()
        );

        // Calculer la nouvelle date limite si délai défini
        $dateLimite = null;
        if ($etatCible->delai_max_jours) {
            $dateLimite = date('Y-m-d H:i:s', time() + ($etatCible->delai_max_jours * 86400));
        }

        // Mettre à jour le dossier
        $this->etat_actuel_id = $etatCible->getId();
        $this->date_entree_etat = date('Y-m-d H:i:s');
        $this->date_limite_etat = $dateLimite;
        $this->save();
    }

    /**
     * Vérifie si le délai est dépassé
     */
    public function delaiDepasse(): bool
    {
        if ($this->date_limite_etat === null) {
            return false;
        }
        return strtotime($this->date_limite_etat) < time();
    }

    /**
     * Calcule le pourcentage du délai écoulé
     */
    public function pourcentageDelai(): int
    {
        if ($this->date_limite_etat === null || $this->date_entree_etat === null) {
            return 0;
        }

        $debut = strtotime($this->date_entree_etat);
        $fin = strtotime($this->date_limite_etat);
        $now = time();

        if ($debut >= $fin) {
            return 100;
        }

        $total = $fin - $debut;
        $ecoule = $now - $debut;

        return min(100, max(0, (int) round(($ecoule / $total) * 100)));
    }

    /**
     * Retourne la candidature du dossier
     */
    public function getCandidature(): ?object
    {
        $sql = "SELECT * FROM candidatures WHERE dossier_id = :id LIMIT 1";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne le rapport du dossier
     */
    public function getRapport(): ?object
    {
        $sql = "SELECT * FROM rapports_etudiants WHERE dossier_id = :id ORDER BY version DESC LIMIT 1";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne la soutenance du dossier
     */
    public function getSoutenance(): ?object
    {
        $sql = "SELECT * FROM soutenances WHERE dossier_id = :id LIMIT 1";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Retourne les membres du jury
     */
    public function getJury(): array
    {
        $sql = "SELECT jm.*, e.nom_ens, e.prenom_ens, e.email_ens
                FROM jury_membres jm
                INNER JOIN enseignants e ON e.id_enseignant = jm.enseignant_id
                WHERE jm.dossier_id = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Retourne l'historique du workflow
     */
    public function getHistorique(): array
    {
        return WorkflowHistorique::pourDossier($this->getId());
    }

    /**
     * Trouve un dossier par étudiant et année
     */
    public static function trouver(int $etudiantId, int $anneeAcadId): ?self
    {
        return self::firstWhere([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
        ]);
    }

    /**
     * Retourne les dossiers dans un état donné
     *
     * @return self[]
     */
    public static function parEtat(int $etatId): array
    {
        return self::where(['etat_actuel_id' => $etatId]);
    }

    /**
     * Retourne les dossiers avec délai dépassé
     */
    public static function delaisDepasses(): array
    {
        $sql = "SELECT * FROM dossiers_etudiants 
                WHERE date_limite_etat IS NOT NULL AND date_limite_etat < NOW()";

        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }
}
