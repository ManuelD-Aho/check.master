<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Reclamation
 * 
 * Représente une réclamation étudiante.
 * Table: reclamations
 * 
 * @see PRD 08 - Administration
 */
class Reclamation extends Model
{
    protected string $table = 'reclamations';
    protected string $primaryKey = 'id_reclamation';
    protected array $fillable = [
        'etudiant_id',
        'type_reclamation',
        'sujet',
        'description',
        'entite_concernee_id',
        'statut',
        'resolution',
        'motif_rejet',
        'prise_en_charge_par',
        'prise_en_charge_le',
        'resolue_par',
        'resolue_le',
    ];

    /**
     * Types de réclamation
     */
    public const TYPE_NOTE = 'Note';
    public const TYPE_PAIEMENT = 'Paiement';
    public const TYPE_INSCRIPTION = 'Inscription';
    public const TYPE_SOUTENANCE = 'Soutenance';
    public const TYPE_CANDIDATURE = 'Candidature';
    public const TYPE_AUTRE = 'Autre';

    /**
     * Statuts
     */
    public const STATUT_EN_ATTENTE = 'En_attente';
    public const STATUT_EN_COURS = 'En_cours';
    public const STATUT_RESOLUE = 'Resolue';
    public const STATUT_REJETEE = 'Rejetee';

    // ===== RELATIONS =====

    /**
     * Retourne l'étudiant associé
     */
    public function etudiant(): ?Etudiant
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne l'utilisateur qui a pris en charge
     */
    public function priseEnChargePar(): ?Utilisateur
    {
        if ($this->prise_en_charge_par === null) {
            return null;
        }
        return $this->belongsTo(Utilisateur::class, 'prise_en_charge_par', 'id_utilisateur');
    }

    /**
     * Retourne l'utilisateur qui a résolu
     */
    public function resoluePar(): ?Utilisateur
    {
        if ($this->resolue_par === null) {
            return null;
        }
        return $this->belongsTo(Utilisateur::class, 'resolue_par', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les réclamations en attente
     * @return self[]
     */
    public static function enAttente(): array
    {
        return self::where(['statut' => self::STATUT_EN_ATTENTE]);
    }

    /**
     * Retourne les réclamations en cours
     * @return self[]
     */
    public static function enCours(): array
    {
        return self::where(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Retourne les réclamations d'un étudiant
     * @return self[]
     */
    public static function pourEtudiant(int $etudiantId): array
    {
        $sql = "SELECT * FROM reclamations WHERE etudiant_id = :id ORDER BY created_at DESC";
        $stmt = self::raw($sql, ['id' => $etudiantId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les réclamations assignées à un utilisateur
     * @return self[]
     */
    public static function assigneesA(int $utilisateurId): array
    {
        $sql = "SELECT * FROM reclamations 
                WHERE prise_en_charge_par = :id 
                AND statut = :statut
                ORDER BY prise_en_charge_le DESC";

        $stmt = self::raw($sql, ['id' => $utilisateurId, 'statut' => self::STATUT_EN_COURS]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si la réclamation est en attente
     */
    public function estEnAttente(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    /**
     * Vérifie si la réclamation est en cours
     */
    public function estEnCours(): bool
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    /**
     * Vérifie si la réclamation est résolue
     */
    public function estResolue(): bool
    {
        return $this->statut === self::STATUT_RESOLUE;
    }

    /**
     * Vérifie si la réclamation est rejetée
     */
    public function estRejetee(): bool
    {
        return $this->statut === self::STATUT_REJETEE;
    }

    /**
     * Vérifie si la réclamation est traitée (résolue ou rejetée)
     */
    public function estTraitee(): bool
    {
        return $this->estResolue() || $this->estRejetee();
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Compte les réclamations en attente
     */
    public static function nombreEnAttente(): int
    {
        return self::count(['statut' => self::STATUT_EN_ATTENTE]);
    }

    /**
     * Compte les réclamations en cours
     */
    public static function nombreEnCours(): int
    {
        return self::count(['statut' => self::STATUT_EN_COURS]);
    }

    /**
     * Retourne les types de réclamation disponibles
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_NOTE,
            self::TYPE_PAIEMENT,
            self::TYPE_INSCRIPTION,
            self::TYPE_SOUTENANCE,
            self::TYPE_CANDIDATURE,
            self::TYPE_AUTRE,
        ];
    }

    /**
     * Retourne les statuts disponibles
     */
    public static function getStatuts(): array
    {
        return [
            self::STATUT_EN_ATTENTE,
            self::STATUT_EN_COURS,
            self::STATUT_RESOLUE,
            self::STATUT_REJETEE,
        ];
    }

    /**
     * Statistiques par statut
     */
    public static function statistiquesParStatut(): array
    {
        $sql = "SELECT statut, COUNT(*) as total FROM reclamations GROUP BY statut";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques par type
     */
    public static function statistiquesParType(): array
    {
        $sql = "SELECT type_reclamation, COUNT(*) as total FROM reclamations GROUP BY type_reclamation";
        $stmt = self::raw($sql, []);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
