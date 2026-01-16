<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Exoneration
 * 
 * Représente une exonération de droits (totale ou partielle).
 * Table: exonerations
 */
class Exoneration extends Model
{
    protected string $table = 'exonerations';
    protected string $primaryKey = 'id_exoneration';
    protected array $fillable = [
        'etudiant_id',
        'annee_acad_id',
        'montant',
        'montant_exonere',
        'pourcentage',
        'pourcentage_exonere',
        'type',
        'motif',
        'justificatif',
        'statut',
        'demandee_par',
        'demandee_le',
        'approuvee_par',
        'approuvee_le',
        'refusee_par',
        'refusee_le',
        'motif_refus',
        'commentaire_decision',
        'date_attribution',
        'approuve_par',
        'annulee_par',
        'annulee_le',
        'motif_annulation',
    ];

    /**
     * Types d'exonération courants
     */
    public const MOTIF_BOURSE = 'Boursier';
    public const MOTIF_MERITE = 'Mérite';
    public const MOTIF_SOCIAL = 'Social';
    public const MOTIF_HANDICAP = 'Handicap';
    public const MOTIF_AUTRE = 'Autre';

    // ===== RELATIONS =====

    /**
     * Retourne l'étudiant
     */
    public function etudiant(): ?Etudiant
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id', 'id_etudiant');
    }

    /**
     * Retourne l'année académique
     */
    public function anneeAcademique(): ?AnneeAcademique
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_acad_id', 'id_annee_acad');
    }

    /**
     * Retourne l'utilisateur qui a approuvé
     */
    public function approuvePar(): ?Utilisateur
    {
        return $this->belongsTo(Utilisateur::class, 'approuve_par', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les exonérations d'un étudiant
     * @return self[]
     */
    public static function pourEtudiant(int $etudiantId, ?int $anneeAcadId = null): array
    {
        $conditions = ['etudiant_id' => $etudiantId];
        if ($anneeAcadId !== null) {
            $conditions['annee_acad_id'] = $anneeAcadId;
        }
        return self::where($conditions);
    }

    /**
     * Retourne les exonérations d'une année
     * @return self[]
     */
    public static function pourAnnee(int $anneeAcadId): array
    {
        return self::where(['annee_acad_id' => $anneeAcadId]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Attribue une exonération par montant
     */
    public static function attribuerMontant(
        int $etudiantId,
        int $anneeAcadId,
        float $montant,
        string $motif,
        int $approuvePar
    ): self {
        $exoneration = new self([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
            'montant_exonere' => $montant,
            'motif' => $motif,
            'date_attribution' => date('Y-m-d'),
            'approuve_par' => $approuvePar,
        ]);
        $exoneration->save();
        return $exoneration;
    }

    /**
     * Attribue une exonération par pourcentage
     */
    public static function attribuerPourcentage(
        int $etudiantId,
        int $anneeAcadId,
        float $pourcentage,
        string $motif,
        int $approuvePar
    ): self {
        $exoneration = new self([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
            'pourcentage_exonere' => $pourcentage,
            'motif' => $motif,
            'date_attribution' => date('Y-m-d'),
            'approuve_par' => $approuvePar,
        ]);
        $exoneration->save();
        return $exoneration;
    }

    /**
     * Calcule le montant total exonéré pour un étudiant
     */
    public static function totalExonere(int $etudiantId, int $anneeAcadId): float
    {
        $sql = "SELECT COALESCE(SUM(montant_exonere), 0) 
                FROM exonerations 
                WHERE etudiant_id = :etudiant AND annee_acad_id = :annee";

        $stmt = self::raw($sql, [
            'etudiant' => $etudiantId,
            'annee' => $anneeAcadId,
        ]);

        return (float) $stmt->fetchColumn();
    }

    /**
     * Vérifie si l'étudiant a une exonération
     */
    public static function aExoneration(int $etudiantId, int $anneeAcadId): bool
    {
        return self::count([
            'etudiant_id' => $etudiantId,
            'annee_acad_id' => $anneeAcadId,
        ]) > 0;
    }

    /**
     * Vérifie si c'est une exonération totale (100%)
     */
    public function estTotale(): bool
    {
        return $this->pourcentage_exonere !== null && (float) $this->pourcentage_exonere >= 100;
    }
}
