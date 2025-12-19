<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle DecisionJury
 * 
 * Représente la décision finale du jury pour une soutenance.
 * Table: decisions_jury
 */
class DecisionJury extends Model
{
    protected string $table = 'decisions_jury';
    protected string $primaryKey = 'id_decision';
    protected array $fillable = [
        'soutenance_id',
        'decision',
        'delai_corrections',
        'commentaires',
    ];

    /**
     * Types de décisions
     */
    public const DECISION_ADMIS = 'Admis';
    public const DECISION_AJOURNE = 'Ajourné';
    public const DECISION_CORRECTIONS_MINEURES = 'Corrections_mineures';
    public const DECISION_CORRECTIONS_MAJEURES = 'Corrections_majeures';

    /**
     * Délais par défaut (en jours)
     */
    public const DELAI_CORRECTIONS_MINEURES = 7;
    public const DELAI_CORRECTIONS_MAJEURES = 30;

    // ===== RELATIONS =====

    /**
     * Retourne la soutenance
     */
    public function soutenance(): ?Soutenance
    {
        return $this->belongsTo(Soutenance::class, 'soutenance_id', 'id_soutenance');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve la décision pour une soutenance
     */
    public static function pourSoutenance(int $soutenanceId): ?self
    {
        return self::firstWhere(['soutenance_id' => $soutenanceId]);
    }

    /**
     * Retourne les décisions par type
     * @return self[]
     */
    public static function parDecision(string $decision): array
    {
        return self::where(['decision' => $decision]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Enregistre une décision
     */
    public static function enregistrer(
        int $soutenanceId,
        string $decision,
        ?int $delaiCorrections = null,
        ?string $commentaires = null
    ): self {
        // Déterminer le délai automatique si nécessaire
        if ($delaiCorrections === null) {
            $delaiCorrections = match ($decision) {
                self::DECISION_CORRECTIONS_MINEURES => self::DELAI_CORRECTIONS_MINEURES,
                self::DECISION_CORRECTIONS_MAJEURES => self::DELAI_CORRECTIONS_MAJEURES,
                default => null,
            };
        }

        $decisionJury = new self([
            'soutenance_id' => $soutenanceId,
            'decision' => $decision,
            'delai_corrections' => $delaiCorrections,
            'commentaires' => $commentaires,
        ]);
        $decisionJury->save();
        return $decisionJury;
    }

    /**
     * Vérifie si le candidat est admis
     */
    public function estAdmis(): bool
    {
        return $this->decision === self::DECISION_ADMIS;
    }

    /**
     * Vérifie si le candidat est ajourné
     */
    public function estAjourne(): bool
    {
        return $this->decision === self::DECISION_AJOURNE;
    }

    /**
     * Vérifie si des corrections sont requises
     */
    public function correctionsRequises(): bool
    {
        return in_array($this->decision, [
            self::DECISION_CORRECTIONS_MINEURES,
            self::DECISION_CORRECTIONS_MAJEURES,
        ], true);
    }

    /**
     * Calcule la date limite des corrections
     */
    public function dateLimiteCorrections(): ?string
    {
        if (!$this->correctionsRequises() || $this->delai_corrections === null) {
            return null;
        }

        // Récupérer la date de soutenance
        $soutenance = $this->soutenance();
        if ($soutenance === null) {
            return null;
        }

        $dateSoutenance = strtotime($soutenance->date_soutenance);
        return date('Y-m-d', $dateSoutenance + ($this->delai_corrections * 86400));
    }

    /**
     * Retourne le libellé de la décision
     */
    public function getLibelle(): string
    {
        return match ($this->decision) {
            self::DECISION_ADMIS => 'Admis',
            self::DECISION_AJOURNE => 'Ajourné',
            self::DECISION_CORRECTIONS_MINEURES => 'Corrections mineures requises',
            self::DECISION_CORRECTIONS_MAJEURES => 'Corrections majeures requises',
            default => (string) $this->decision,
        };
    }
}
