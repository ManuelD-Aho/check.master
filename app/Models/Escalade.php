<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Escalade
 * 
 * Représente une escalade suite à un blocage workflow ou dépassement de délai.
 * Table: escalades
 */
class Escalade extends Model
{
    protected string $table = 'escalades';
    protected string $primaryKey = 'id_escalade';

    protected array $fillable = [
        'dossier_id',
        'type_escalade',
        'niveau_escalade',
        'description',
        'statut',
        'cree_par',
        'assignee_a',
    ];

    /**
     * Types d'escalade
     */
    public const TYPE_COMMISSION_BLOCAGE = 'commission_blocage';
    public const TYPE_DELAI_DEPASSE = 'delai_depasse';
    public const TYPE_AVIS_ABSENT = 'avis_absent';
    public const TYPE_JURY_INCOMPLET = 'jury_incomplet';
    public const TYPE_RECLAMATION = 'reclamation';
    public const TYPE_AUTRE = 'autre';

    /**
     * Statuts d'escalade
     */
    public const STATUT_OUVERTE = 'Ouverte';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_RESOLUE = 'Resolue';
    public const STATUT_FERMEE = 'Fermee';

    // ===== RELATIONS =====

    /**
     * Retourne le dossier étudiant associé
     */
    public function dossier(): ?DossierEtudiant
    {
        return $this->belongsTo(DossierEtudiant::class, 'dossier_id', 'id_dossier');
    }

    /**
     * Retourne l'utilisateur créateur
     */
    public function createur(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par', 'id_utilisateur');
    }

    /**
     * Retourne l'utilisateur assigné
     */
    public function assigneA(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'assignee_a', 'id_utilisateur');
    }

    /**
     * Retourne les actions de cette escalade
     * @return EscaladeAction[]
     */
    public function actions(): array
    {
        return $this->hasMany(EscaladeAction::class, 'escalade_id', 'id_escalade');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les escalades ouvertes
     * @return self[]
     */
    public static function ouvertes(): array
    {
        return self::where(['statut' => self::STATUT_OUVERTE]);
    }

    /**
     * Retourne les escalades en cours
     * @return self[]
     */
    public static function enCours(): array
    {
        return self::where(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Retourne les escalades assignées à un utilisateur
     * @return self[]
     */
    public static function assigneesA(int $utilisateurId): array
    {
        $sql = "SELECT * FROM escalades 
                WHERE assignee_a = :id 
                AND statut IN ('Ouverte', 'En_cours')
                ORDER BY created_at DESC";

        $stmt = self::raw($sql, ['id' => $utilisateurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les escalades d'un dossier
     * @return self[]
     */
    public static function pourDossier(int $dossierId): array
    {
        $sql = "SELECT * FROM escalades 
                WHERE dossier_id = :id 
                ORDER BY created_at DESC";

        $stmt = self::raw($sql, ['id' => $dossierId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée une nouvelle escalade
     */
    public static function creer(
        int $dossierId,
        string $type,
        string $description,
        int $creePar,
        ?int $assigneeA = null,
        int $niveau = 1
    ): self {
        $escalade = new self([
            'dossier_id' => $dossierId,
            'type_escalade' => $type,
            'niveau_escalade' => $niveau,
            'description' => $description,
            'statut' => self::STATUT_OUVERTE,
            'cree_par' => $creePar,
            'assignee_a' => $assigneeA,
        ]);
        $escalade->save();
        return $escalade;
    }

    /**
     * Prend en charge l'escalade
     */
    public function prendreEnCharge(int $utilisateurId): void
    {
        $this->statut = self::STATUT_EN_COURS;
        $this->assignee_a = $utilisateurId;
        $this->save();

        // Enregistrer l'action
        EscaladeAction::enregistrer(
            $this->getId(),
            $utilisateurId,
            'prise_en_charge',
            'Escalade prise en charge'
        );
    }

    /**
     * Résoud l'escalade
     */
    public function resoudre(int $utilisateurId, string $resolution): void
    {
        $this->statut = self::STATUT_RESOLUE;
        $this->save();

        EscaladeAction::enregistrer(
            $this->getId(),
            $utilisateurId,
            'resolution',
            $resolution
        );
    }

    /**
     * Ferme l'escalade
     */
    public function fermer(int $utilisateurId, string $motif): void
    {
        $this->statut = self::STATUT_FERMEE;
        $this->save();

        EscaladeAction::enregistrer(
            $this->getId(),
            $utilisateurId,
            'fermeture',
            $motif
        );
    }

    /**
     * Escalade au niveau supérieur
     */
    public function escaladerNiveauSuperieur(int $utilisateurId, ?int $nouvelAssigne = null): void
    {
        $this->niveau_escalade = ($this->niveau_escalade ?? 1) + 1;
        if ($nouvelAssigne !== null) {
            $this->assignee_a = $nouvelAssigne;
        }
        $this->save();

        EscaladeAction::enregistrer(
            $this->getId(),
            $utilisateurId,
            'escalade_superieure',
            "Escalade au niveau {$this->niveau_escalade}"
        );
    }

    /**
     * Vérifie si l'escalade est active
     */
    public function estActive(): bool
    {
        return in_array($this->statut, [self::STATUT_OUVERTE, self::STATUT_EN_COURS], true);
    }

    /**
     * Compte les escalades actives
     */
    public static function nombreActives(): int
    {
        $sql = "SELECT COUNT(*) FROM escalades WHERE statut IN ('Ouverte', 'En_cours')";
        $stmt = self::raw($sql);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne les statistiques par type
     */
    public static function statistiquesParType(): array
    {
        $sql = "SELECT type_escalade, COUNT(*) as total,
                SUM(CASE WHEN statut IN ('Ouverte', 'En_cours') THEN 1 ELSE 0 END) as actives
                FROM escalades
                GROUP BY type_escalade";

        $stmt = self::raw($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
