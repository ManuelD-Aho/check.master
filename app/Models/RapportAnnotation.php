<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle RapportAnnotation
 * 
 * Représente une annotation sur un rapport.
 * Table: annotations_rapport
 */
class RapportAnnotation extends Model
{
    protected string $table = 'annotations_rapport';
    protected string $primaryKey = 'id_annotation';
    protected array $fillable = [
        'rapport_id',
        'auteur_id',
        'page_numero',
        'position_json',
        'contenu',
        'type_annotation',
    ];

    /**
     * Types d'annotations
     */
    public const TYPE_COMMENTAIRE = 'Commentaire';
    public const TYPE_CORRECTION = 'Correction';
    public const TYPE_SUGGESTION = 'Suggestion';

    /**
     * Retourne le rapport
     */
    public function getRapport(): ?RapportEtudiant
    {
        if ($this->rapport_id === null) {
            return null;
        }
        return RapportEtudiant::find((int) $this->rapport_id);
    }

    /**
     * Retourne l'auteur (enseignant)
     */
    public function getAuteur(): ?Enseignant
    {
        if ($this->auteur_id === null) {
            return null;
        }
        return Enseignant::find((int) $this->auteur_id);
    }

    /**
     * Retourne la position décodée
     */
    public function getPosition(): array
    {
        if (empty($this->position_json)) {
            return [];
        }
        return json_decode($this->position_json, true) ?? [];
    }

    /**
     * Crée une annotation
     */
    public static function ajouter(
        int $rapportId,
        int $auteurId,
        string $contenu,
        string $type = self::TYPE_COMMENTAIRE,
        ?int $pageNumero = null,
        ?array $position = null
    ): self {
        $annotation = new self([
            'rapport_id' => $rapportId,
            'auteur_id' => $auteurId,
            'contenu' => $contenu,
            'type_annotation' => $type,
            'page_numero' => $pageNumero,
            'position_json' => $position ? json_encode($position) : null,
        ]);
        $annotation->save();
        return $annotation;
    }

    /**
     * Retourne les annotations d'un rapport
     *
     * @return self[]
     */
    public static function pourRapport(int $rapportId): array
    {
        return self::where(['rapport_id' => $rapportId]);
    }

    /**
     * Retourne les annotations d'un auteur
     *
     * @return self[]
     */
    public static function parAuteur(int $auteurId): array
    {
        return self::where(['auteur_id' => $auteurId]);
    }
}
