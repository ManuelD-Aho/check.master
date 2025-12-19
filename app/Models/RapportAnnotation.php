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

    // ===== RELATIONS =====

    /**
     * Retourne le rapport
     */
    public function rapport(): ?RapportEtudiant
    {
        return $this->belongsTo(RapportEtudiant::class, 'rapport_id', 'id_rapport');
    }

    /**
     * Retourne l'auteur (enseignant)
     */
    public function auteur(): ?Enseignant
    {
        return $this->belongsTo(Enseignant::class, 'auteur_id', 'id_enseignant');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Retourne les annotations d'un rapport
     * @return self[]
     */
    public static function pourRapport(int $rapportId): array
    {
        $sql = "SELECT * FROM annotations_rapport 
                WHERE rapport_id = :id 
                ORDER BY page_numero, created_at";

        $stmt = self::raw($sql, ['id' => $rapportId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les annotations par auteur
     * @return self[]
     */
    public static function parAuteur(int $auteurId): array
    {
        return self::where(['auteur_id' => $auteurId]);
    }

    /**
     * Retourne les annotations par type
     * @return self[]
     */
    public static function parType(string $type): array
    {
        return self::where(['type_annotation' => $type]);
    }

    // ===== MÉTHODES MÉTIER =====

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
     * Définit la position
     */
    public function setPosition(array $position): void
    {
        $this->position_json = json_encode($position);
    }

    /**
     * Compte les annotations par type pour un rapport
     */
    public static function statistiquesParType(int $rapportId): array
    {
        $sql = "SELECT type_annotation, COUNT(*) as total
                FROM annotations_rapport
                WHERE rapport_id = :id
                GROUP BY type_annotation";

        $stmt = self::raw($sql, ['id' => $rapportId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
