<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Avoir
 * 
 * Table d'association générique polymorphique entre entités.
 * Utilisée pour créer des relations dynamiques entre différents types d'entités.
 * Table: avoir
 */
class Avoir extends Model
{
    protected string $table = 'avoir';
    protected string $primaryKey = 'id_avoir';
    protected array $fillable = [
        'entite_source_type',
        'entite_source_id',
        'entite_cible_type',
        'entite_cible_id',
        'type_relation',
        'date_debut',
        'date_fin',
        'metadata_json',
    ];

    /**
     * Types de relations prédéfinis
     */
    public const RELATION_APPARTIENT = 'appartient';
    public const RELATION_SUPERVISE = 'supervise';
    public const RELATION_ASSOCIE = 'associe';
    public const RELATION_POSSEDE = 'possede';

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les relations d'une entité source
     * @return self[]
     */
    public static function pourEntiteSource(string $type, int $id): array
    {
        return self::where([
            'entite_source_type' => $type,
            'entite_source_id' => $id,
        ]);
    }

    /**
     * Retourne les relations vers une entité cible
     * @return self[]
     */
    public static function pourEntiteCible(string $type, int $id): array
    {
        return self::where([
            'entite_cible_type' => $type,
            'entite_cible_id' => $id,
        ]);
    }

    /**
     * Trouve une relation spécifique
     */
    public static function trouverRelation(
        string $sourceType,
        int $sourceId,
        string $cibleType,
        int $cibleId
    ): ?self {
        return self::firstWhere([
            'entite_source_type' => $sourceType,
            'entite_source_id' => $sourceId,
            'entite_cible_type' => $cibleType,
            'entite_cible_id' => $cibleId,
        ]);
    }

    /**
     * Retourne les relations actives (non expirées)
     * @return self[]
     */
    public static function actives(): array
    {
        $sql = "SELECT * FROM avoir 
                WHERE (date_fin IS NULL OR date_fin >= CURRENT_DATE)
                AND (date_debut IS NULL OR date_debut <= CURRENT_DATE)";
        $stmt = self::raw($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée une nouvelle association
     */
    public static function creer(
        string $sourceType,
        int $sourceId,
        string $cibleType,
        int $cibleId,
        string $typeRelation = self::RELATION_ASSOCIE,
        ?string $dateDebut = null,
        ?string $dateFin = null,
        ?array $metadata = null
    ): self {
        $avoir = new self([
            'entite_source_type' => $sourceType,
            'entite_source_id' => $sourceId,
            'entite_cible_type' => $cibleType,
            'entite_cible_id' => $cibleId,
            'type_relation' => $typeRelation,
            'date_debut' => $dateDebut ?? date('Y-m-d'),
            'date_fin' => $dateFin,
            'metadata_json' => $metadata !== null ? json_encode($metadata) : null,
        ]);
        $avoir->save();
        return $avoir;
    }

    /**
     * Vérifie si la relation est active
     */
    public function estActive(): bool
    {
        $now = date('Y-m-d');

        if ($this->date_debut !== null && $this->date_debut > $now) {
            return false;
        }

        if ($this->date_fin !== null && $this->date_fin < $now) {
            return false;
        }

        return true;
    }

    /**
     * Termine la relation
     */
    public function terminer(?string $dateFin = null): void
    {
        $this->date_fin = $dateFin ?? date('Y-m-d');
        $this->save();
    }

    /**
     * Retourne les métadonnées décodées
     */
    public function getMetadata(): array
    {
        if (empty($this->metadata_json)) {
            return [];
        }
        return json_decode($this->metadata_json, true) ?? [];
    }

    /**
     * Définit les métadonnées
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata_json = json_encode($metadata);
    }

    /**
     * Vérifie si une relation existe
     */
    public static function existe(
        string $sourceType,
        int $sourceId,
        string $cibleType,
        int $cibleId
    ): bool {
        return self::trouverRelation($sourceType, $sourceId, $cibleType, $cibleId) !== null;
    }
}
