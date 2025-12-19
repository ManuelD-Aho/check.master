<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle AnnotationRapport
 * 
 * Représente une annotation sur un rapport d'étudiant.
 * Alternative à RapportAnnotation - préféré selon la convention de nommage PascalCase
 * Table: annotations_rapport
 */
class AnnotationRapport extends Model
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
     * Types d'annotations possibles
     */
    public const TYPE_COMMENTAIRE = 'Commentaire';
    public const TYPE_CORRECTION = 'Correction';
    public const TYPE_SUGGESTION = 'Suggestion';

    /**
     * Liste des types valides
     */
    public const TYPES_VALIDES = [
        self::TYPE_COMMENTAIRE,
        self::TYPE_CORRECTION,
        self::TYPE_SUGGESTION,
    ];

    // ===== RELATIONS =====

    /**
     * Retourne le rapport annoté
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
                ORDER BY page_numero ASC, created_at ASC";

        $stmt = self::raw($sql, ['id' => $rapportId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les annotations d'un rapport par page
     * @return self[]
     */
    public static function pourRapportPage(int $rapportId, int $page): array
    {
        return self::where([
            'rapport_id' => $rapportId,
            'page_numero' => $page,
        ]);
    }

    /**
     * Retourne les annotations par auteur
     * @return self[]
     */
    public static function parAuteur(int $auteurId): array
    {
        $sql = "SELECT * FROM annotations_rapport 
                WHERE auteur_id = :id 
                ORDER BY created_at DESC";

        $stmt = self::raw($sql, ['id' => $auteurId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    /**
     * Retourne les annotations par type
     * @return self[]
     */
    public static function parType(int $rapportId, string $type): array
    {
        return self::where([
            'rapport_id' => $rapportId,
            'type_annotation' => $type,
        ]);
    }

    // ===== MÉTHODES MÉTIER =====

    /**
     * Crée une nouvelle annotation
     */
    public static function creer(
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
            'position_json' => $position !== null ? json_encode($position) : null,
        ]);
        $annotation->save();
        return $annotation;
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
     * Définit la position
     */
    public function setPosition(array $position): void
    {
        $this->position_json = json_encode($position);
    }

    /**
     * Vérifie si l'annotation est une correction
     */
    public function estCorrection(): bool
    {
        return $this->type_annotation === self::TYPE_CORRECTION;
    }

    /**
     * Vérifie si l'annotation est un commentaire
     */
    public function estCommentaire(): bool
    {
        return $this->type_annotation === self::TYPE_COMMENTAIRE;
    }

    /**
     * Vérifie si l'annotation est une suggestion
     */
    public function estSuggestion(): bool
    {
        return $this->type_annotation === self::TYPE_SUGGESTION;
    }

    /**
     * Compte les annotations par type pour un rapport
     * @return array<string, int>
     */
    public static function compterParType(int $rapportId): array
    {
        $sql = "SELECT type_annotation, COUNT(*) as total
                FROM annotations_rapport
                WHERE rapport_id = :id
                GROUP BY type_annotation";

        $stmt = self::raw($sql, ['id' => $rapportId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[$row['type_annotation']] = (int) $row['total'];
        }
        return $result;
    }

    /**
     * Compte le nombre total d'annotations pour un rapport
     */
    public static function nombreAnnotations(int $rapportId): int
    {
        $sql = "SELECT COUNT(*) FROM annotations_rapport WHERE rapport_id = :id";
        $stmt = self::raw($sql, ['id' => $rapportId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retourne un résumé des annotations
     */
    public function getResume(int $longueur = 100): string
    {
        $contenu = strip_tags($this->contenu ?? '');
        if (strlen($contenu) <= $longueur) {
            return $contenu;
        }
        return substr($contenu, 0, $longueur) . '...';
    }

    /**
     * Supprime toutes les annotations d'un rapport
     */
    public static function supprimerPourRapport(int $rapportId): int
    {
        $sql = "DELETE FROM annotations_rapport WHERE rapport_id = :id";
        $stmt = self::raw($sql, ['id' => $rapportId]);
        return $stmt->rowCount();
    }
}
