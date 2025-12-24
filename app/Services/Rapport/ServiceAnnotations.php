<?php

declare(strict_types=1);

namespace App\Services\Rapport;

use App\Models\AnnotationRapport;
use App\Models\RapportEtudiant;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\NotFoundException;

/**
 * Service Annotations
 * 
 * Gestion des annotations sur les rapports (commentaires par page).
 */
class ServiceAnnotations
{
    /**
     * Types d'annotations
     */
    public const TYPE_COMMENTAIRE = 'commentaire';
    public const TYPE_CORRECTION = 'correction';
    public const TYPE_SUGGESTION = 'suggestion';
    public const TYPE_ERREUR = 'erreur';

    /**
     * Ajoute une annotation sur un rapport
     */
    public static function ajouter(
        int $rapportId,
        int $auteurId,
        int $page,
        string $contenu,
        string $type = self::TYPE_COMMENTAIRE,
        ?array $position = null
    ): AnnotationRapport {
        $rapport = RapportEtudiant::find($rapportId);
        if ($rapport === null) {
            throw new NotFoundException('Rapport non trouvé');
        }

        $annotation = new AnnotationRapport([
            'rapport_id' => $rapportId,
            'auteur_id' => $auteurId,
            'page' => $page,
            'type_annotation' => $type,
            'contenu' => $contenu,
            'position_json' => $position ? json_encode($position) : null,
        ]);
        $annotation->save();

        ServiceAudit::log('annotation_rapport', 'annotation', $annotation->getId(), [
            'rapport_id' => $rapportId,
            'page' => $page,
            'type' => $type,
        ]);

        return $annotation;
    }

    /**
     * Modifie une annotation
     */
    public static function modifier(
        int $annotationId,
        string $contenu,
        int $utilisateurId
    ): bool {
        $annotation = AnnotationRapport::find($annotationId);
        if ($annotation === null) {
            throw new NotFoundException('Annotation non trouvée');
        }

        // Vérifier que l'utilisateur est l'auteur
        if ((int) $annotation->auteur_id !== $utilisateurId) {
            return false;
        }

        $annotation->contenu = $contenu;
        $annotation->modifie_le = date('Y-m-d H:i:s');
        $result = $annotation->save();

        if ($result) {
            ServiceAudit::log('modification_annotation', 'annotation', $annotationId);
        }

        return $result;
    }

    /**
     * Supprime une annotation
     */
    public static function supprimer(int $annotationId, int $utilisateurId): bool
    {
        $annotation = AnnotationRapport::find($annotationId);
        if ($annotation === null) {
            throw new NotFoundException('Annotation non trouvée');
        }

        // Vérifier que l'utilisateur est l'auteur
        if ((int) $annotation->auteur_id !== $utilisateurId) {
            return false;
        }

        ServiceAudit::log('suppression_annotation', 'annotation', $annotationId);

        return $annotation->delete();
    }

    /**
     * Retourne les annotations d'un rapport
     */
    public static function getAnnotationsRapport(int $rapportId): array
    {
        $sql = "SELECT a.*, u.nom_utilisateur as auteur_nom
                FROM annotations_rapports a
                LEFT JOIN utilisateurs u ON u.id_utilisateur = a.auteur_id
                WHERE a.rapport_id = :id
                ORDER BY a.page, a.created_at";

        $stmt = \App\Orm\Model::raw($sql, ['id' => $rapportId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les annotations d'une page spécifique
     */
    public static function getAnnotationsPage(int $rapportId, int $page): array
    {
        $sql = "SELECT a.*, u.nom_utilisateur as auteur_nom
                FROM annotations_rapports a
                LEFT JOIN utilisateurs u ON u.id_utilisateur = a.auteur_id
                WHERE a.rapport_id = :id AND a.page = :page
                ORDER BY a.created_at";

        $stmt = \App\Orm\Model::raw($sql, ['id' => $rapportId, 'page' => $page]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Compte les annotations par type pour un rapport
     */
    public static function compterParType(int $rapportId): array
    {
        $sql = "SELECT type_annotation, COUNT(*) as total
                FROM annotations_rapports
                WHERE rapport_id = :id
                GROUP BY type_annotation";

        $stmt = \App\Orm\Model::raw($sql, ['id' => $rapportId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Marque une annotation comme résolue
     */
    public static function marquerResolue(int $annotationId, int $utilisateurId): bool
    {
        $annotation = AnnotationRapport::find($annotationId);
        if ($annotation === null) {
            throw new NotFoundException('Annotation non trouvée');
        }

        $annotation->resolue = true;
        $annotation->resolue_par = $utilisateurId;
        $annotation->resolue_le = date('Y-m-d H:i:s');

        return $annotation->save();
    }
}
