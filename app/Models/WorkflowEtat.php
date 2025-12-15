<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowEtat
 * 
 * Représente un état dans le workflow étudiant.
 * Table: workflow_etats
 */
class WorkflowEtat extends Model
{
    protected string $table = 'workflow_etats';
    protected string $primaryKey = 'id_etat';
    protected array $fillable = [
        'code_etat',
        'nom_etat',
        'phase',
        'delai_max_jours',
        'ordre_affichage',
        'couleur_hex',
        'description',
    ];

    /**
     * Codes des états du workflow
     */
    public const INSCRIT = 'inscrit';
    public const CANDIDATURE_SOUMISE = 'candidature_soumise';
    public const VERIFICATION_SCOLARITE = 'verification_scolarite';
    public const FILTRE_COMMUNICATION = 'filtre_communication';
    public const EN_ATTENTE_COMMISSION = 'en_attente_commission';
    public const EN_EVALUATION_COMMISSION = 'en_evaluation_commission';
    public const RAPPORT_VALIDE = 'rapport_valide';
    public const ATTENTE_AVIS_ENCADREUR = 'attente_avis_encadreur';
    public const PRET_POUR_JURY = 'pret_pour_jury';
    public const JURY_EN_CONSTITUTION = 'jury_en_constitution';
    public const SOUTENANCE_PLANIFIEE = 'soutenance_planifiee';
    public const SOUTENANCE_EN_COURS = 'soutenance_en_cours';
    public const SOUTENANCE_TERMINEE = 'soutenance_terminee';
    public const DIPLOME_DELIVRE = 'diplome_delivre';
    public const ABANDON = 'abandon';
    public const ESCALADE_DOYEN = 'escalade_doyen';

    /**
     * Phases du workflow
     */
    public const PHASE_INSCRIPTION = 'inscription';
    public const PHASE_CANDIDATURE = 'candidature';
    public const PHASE_VALIDATION = 'validation';
    public const PHASE_SOUTENANCE = 'soutenance';
    public const PHASE_FINALE = 'finale';

    /**
     * Trouve un état par son code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_etat' => $code]);
    }

    /**
     * Retourne les transitions sortantes de cet état
     */
    public function getTransitionsSortantes(): array
    {
        $sql = "SELECT wt.*, we.code_etat as cible_code, we.nom_etat as cible_nom
                FROM workflow_transitions wt
                INNER JOIN workflow_etats we ON we.id_etat = wt.etat_cible_id
                WHERE wt.etat_source_id = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Retourne les transitions entrantes vers cet état
     */
    public function getTransitionsEntrantes(): array
    {
        $sql = "SELECT wt.*, we.code_etat as source_code, we.nom_etat as source_nom
                FROM workflow_transitions wt
                INNER JOIN workflow_etats we ON we.id_etat = wt.etat_source_id
                WHERE wt.etat_cible_id = :id";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Vérifie si une transition vers un autre état est possible
     */
    public function peutTransitionnerVers(string $codeEtatCible): bool
    {
        $transitions = $this->getTransitionsSortantes();
        foreach ($transitions as $transition) {
            if ($transition->cible_code === $codeEtatCible) {
                return true;
            }
        }
        return false;
    }

    /**
     * Vérifie si c'est un état terminal
     */
    public function estTerminal(): bool
    {
        $terminaux = [self::DIPLOME_DELIVRE, self::ABANDON];
        return in_array($this->code_etat, $terminaux, true);
    }

    /**
     * Retourne tous les états triés par ordre d'affichage
     *
     * @return self[]
     */
    public static function tousTriees(): array
    {
        $sql = "SELECT * FROM workflow_etats ORDER BY ordre_affichage ASC";
        $stmt = self::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les états d'une phase
     *
     * @return self[]
     */
    public static function parPhase(string $phase): array
    {
        return self::where(['phase' => $phase]);
    }

    /**
     * Compte les dossiers dans cet état
     */
    public function nombreDossiers(): int
    {
        $sql = "SELECT COUNT(*) FROM dossiers_etudiants WHERE etat_actuel_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }
}
