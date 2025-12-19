<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Evaluer
 * 
 * Enregistre les évaluations polymorphiques faites par les enseignants.
 * Peut évaluer différents types d'entités (rapports, soutenances, etc.).
 * Table: evaluer
 */
class Evaluer extends Model
{
    protected string $table = 'evaluer';
    protected string $primaryKey = 'id_evaluation';
    protected array $fillable = [
        'enseignant_id',
        'entite_type',
        'entite_id',
        'note',
        'appreciation',
        'criteres_json',
        'date_evaluation',
        'statut',
    ];

    /**
     * Types d'entités évaluables
     */
    public const ENTITE_RAPPORT = 'rapport';
    public const ENTITE_SOUTENANCE = 'soutenance';
    public const ENTITE_STAGE = 'stage';

    /**
     * Statuts d'évaluation
     */
    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_FINALISE = 'finalise';

    // ===== RELATIONS =====

    /**
     * Retourne l'enseignant évaluateur
     */
    public function enseignant(): ?Enseignant
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id', 'id_enseignant');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les évaluations d'un enseignant
     * @return self[]
     */
    public static function parEnseignant(int $enseignantId): array
    {
        $sql = "SELECT * FROM evaluer 
                WHERE enseignant_id = :id 
                ORDER BY date_evaluation DESC";
        $stmt = self::raw($sql, ['id' => $enseignantId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les évaluations pour une entité
     * @return self[]
     */
    public static function pourEntite(string $type, int $id): array
    {
        return self::where([
            'entite_type' => $type,
            'entite_id' => $id,
        ]);
    }

    /**
     * Trouve une évaluation spécifique
     */
    public static function trouverEvaluation(
        int $enseignantId,
        string $entiteType,
        int $entiteId
    ): ?self {
        return self::firstWhere([
            'enseignant_id' => $enseignantId,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
        ]);
    }

    /**
     * Retourne les évaluations finalisées pour une entité
     * @return self[]
     */
    public static function finaliseesPourEntite(string $type, int $id): array
    {
        return self::where([
            'entite_type' => $type,
            'entite_id' => $id,
            'statut' => self::STATUT_FINALISE,
        ]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée ou met à jour une évaluation
     */
    public static function evaluer(
        int $enseignantId,
        string $entiteType,
        int $entiteId,
        float $note,
        ?string $appreciation = null,
        ?array $criteres = null
    ): self {
        // Chercher une évaluation existante
        $existante = self::trouverEvaluation($enseignantId, $entiteType, $entiteId);

        if ($existante !== null) {
            // Mise à jour
            $existante->note = $note;
            $existante->appreciation = $appreciation;
            $existante->criteres_json = $criteres !== null ? json_encode($criteres) : null;
            $existante->date_evaluation = date('Y-m-d H:i:s');
            $existante->save();
            return $existante;
        }

        // Nouvelle évaluation
        $evaluation = new self([
            'enseignant_id' => $enseignantId,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'note' => $note,
            'appreciation' => $appreciation,
            'criteres_json' => $criteres !== null ? json_encode($criteres) : null,
            'date_evaluation' => date('Y-m-d H:i:s'),
            'statut' => self::STATUT_BROUILLON,
        ]);
        $evaluation->save();
        return $evaluation;
    }

    /**
     * Finalise l'évaluation
     */
    public function finaliser(): void
    {
        $this->statut = self::STATUT_FINALISE;
        $this->save();
    }

    /**
     * Vérifie si l'évaluation est finalisée
     */
    public function estFinalisee(): bool
    {
        return $this->statut === self::STATUT_FINALISE;
    }

    /**
     * Vérifie si l'évaluation est un brouillon
     */
    public function estBrouillon(): bool
    {
        return $this->statut === self::STATUT_BROUILLON;
    }

    /**
     * Retourne les critères décodés
     */
    public function getCriteres(): array
    {
        if (empty($this->criteres_json)) {
            return [];
        }
        return json_decode($this->criteres_json, true) ?? [];
    }

    /**
     * Calcule la moyenne des évaluations pour une entité
     */
    public static function moyenneEntite(string $type, int $id): ?float
    {
        $sql = "SELECT AVG(note) as moyenne 
                FROM evaluer 
                WHERE entite_type = :type 
                AND entite_id = :id 
                AND statut = :statut";

        $stmt = self::raw($sql, [
            'type' => $type,
            'id' => $id,
            'statut' => self::STATUT_FINALISE,
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['moyenne'] !== null ? round((float) $result['moyenne'], 2) : null;
    }

    /**
     * Compte le nombre d'évaluations pour une entité
     */
    public static function nombreEvaluations(string $type, int $id): int
    {
        $sql = "SELECT COUNT(*) FROM evaluer 
                WHERE entite_type = :type AND entite_id = :id";
        $stmt = self::raw($sql, ['type' => $type, 'id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un enseignant a évalué une entité
     */
    public static function aEvalue(int $enseignantId, string $type, int $id): bool
    {
        return self::trouverEvaluation($enseignantId, $type, $id) !== null;
    }
}
