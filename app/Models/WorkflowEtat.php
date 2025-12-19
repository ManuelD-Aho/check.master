<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle WorkflowEtat
 * 
 * Représente un état du workflow de suivi des dossiers.
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
     * Codes des états du workflow (14 états)
     */
    public const ETAT_INSCRIT = 'INSCRIT';
    public const ETAT_CANDIDATURE_SOUMISE = 'CANDIDATURE_SOUMISE';
    public const ETAT_VERIFICATION_SCOLARITE = 'VERIFICATION_SCOLARITE';
    public const ETAT_FILTRE_COMMUNICATION = 'FILTRE_COMMUNICATION';
    public const ETAT_EN_ATTENTE_COMMISSION = 'EN_ATTENTE_COMMISSION';
    public const ETAT_EN_EVALUATION_COMMISSION = 'EN_EVALUATION_COMMISSION';
    public const ETAT_RAPPORT_VALIDE = 'RAPPORT_VALIDE';
    public const ETAT_ATTENTE_AVIS_ENCADREUR = 'ATTENTE_AVIS_ENCADREUR';
    public const ETAT_PRET_POUR_JURY = 'PRET_POUR_JURY';
    public const ETAT_JURY_EN_CONSTITUTION = 'JURY_EN_CONSTITUTION';
    public const ETAT_SOUTENANCE_PLANIFIEE = 'SOUTENANCE_PLANIFIEE';
    public const ETAT_SOUTENANCE_EN_COURS = 'SOUTENANCE_EN_COURS';
    public const ETAT_SOUTENANCE_TERMINEE = 'SOUTENANCE_TERMINEE';
    public const ETAT_DIPLOME_DELIVRE = 'DIPLOME_DELIVRE';

    /**
     * Phases du workflow
     */
    public const PHASE_INSCRIPTION = 'inscription';
    public const PHASE_CANDIDATURE = 'candidature';
    public const PHASE_COMMISSION = 'commission';
    public const PHASE_PRE_SOUTENANCE = 'pre_soutenance';
    public const PHASE_SOUTENANCE = 'soutenance';
    public const PHASE_POST_SOUTENANCE = 'post_soutenance';
    public const PHASE_CLOTURE = 'cloture';

    // ===== RELATIONS =====

    /**
     * Retourne les transitions sortantes
     * @return WorkflowTransition[]
     */
    public function transitionsSortantes(): array
    {
        return $this->hasMany(WorkflowTransition::class, 'etat_source_id', 'id_etat');
    }

    /**
     * Retourne les transitions entrantes
     * @return WorkflowTransition[]
     */
    public function transitionsEntrantes(): array
    {
        return $this->hasMany(WorkflowTransition::class, 'etat_cible_id', 'id_etat');
    }

    /**
     * Retourne les dossiers dans cet état
     * @return DossierEtudiant[]
     */
    public function dossiers(): array
    {
        return $this->hasMany(DossierEtudiant::class, 'etat_actuel_id', 'id_etat');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::firstWhere(['code_etat' => $code]);
    }

    /**
     * Retourne tous les états ordonnés
     * @return self[]
     */
    public static function ordonnes(): array
    {
        $sql = "SELECT * FROM workflow_etats ORDER BY ordre_affichage";
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
     * @return self[]
     */
    public static function parPhase(string $phase): array
    {
        $sql = "SELECT * FROM workflow_etats WHERE phase = :phase ORDER BY ordre_affichage";
        $stmt = self::raw($sql, ['phase' => $phase]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne l'état initial du workflow
     */
    public static function initial(): ?self
    {
        return self::findByCode(self::ETAT_INSCRIT);
    }

    /**
     * Retourne l'état final du workflow
     */
    public static function final(): ?self
    {
        return self::findByCode(self::ETAT_DIPLOME_DELIVRE);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si c'est l'état initial
     */
    public function estInitial(): bool
    {
        return $this->code_etat === self::ETAT_INSCRIT;
    }

    /**
     * Vérifie si c'est un état terminal
     */
    public function estTerminal(): bool
    {
        return $this->code_etat === self::ETAT_DIPLOME_DELIVRE;
    }

    /**
     * Vérifie si cet état permet la rédaction du rapport
     */
    public function permetRedactionRapport(): bool
    {
        $etatsPermis = [
            self::ETAT_CANDIDATURE_SOUMISE,
            self::ETAT_VERIFICATION_SCOLARITE,
            self::ETAT_FILTRE_COMMUNICATION,
            self::ETAT_EN_ATTENTE_COMMISSION,
            self::ETAT_EN_EVALUATION_COMMISSION,
        ];
        return in_array($this->code_etat, $etatsPermis, true);
    }

    // ===== MÉTHODES DE TRANSITION =====

    /**
     * Retourne les états cibles possibles
     * @return self[]
     */
    public function getEtatsCiblesPossibles(): array
    {
        $sql = "SELECT we.* FROM workflow_etats we
                INNER JOIN workflow_transitions wt ON wt.etat_cible_id = we.id_etat
                WHERE wt.etat_source_id = :id
                ORDER BY we.ordre_affichage";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Vérifie si une transition vers un état est possible
     */
    public function peutTransitionnerVers(string $codeEtatCible): bool
    {
        $etatsCibles = $this->getEtatsCiblesPossibles();
        foreach ($etatsCibles as $etat) {
            if ($etat->code_etat === $codeEtatCible) {
                return true;
            }
        }
        return false;
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Compte les dossiers dans cet état
     */
    public function nombreDossiers(): int
    {
        return DossierEtudiant::count(['etat_actuel_id' => $this->getId()]);
    }

    /**
     * Retourne les statistiques des dossiers par état
     */
    public static function statistiques(): array
    {
        $sql = "SELECT we.code_etat, we.nom_etat, we.couleur_hex, 
                       COUNT(de.id_dossier) as total
                FROM workflow_etats we
                LEFT JOIN dossiers_etudiants de ON de.etat_actuel_id = we.id_etat
                GROUP BY we.id_etat, we.code_etat, we.nom_etat, we.couleur_hex
                ORDER BY we.ordre_affichage";

        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
