<?php

declare(strict_types=1);

namespace App\Controllers\Rapport;

use App\Services\Rapport\ServiceAnnotations;
use App\Models\AnnotationRapport;
use Src\Http\Response;
use Src\Http\Request;
use App\Orm\Model;

/**
 * Contrôleur des annotations
 * 
 * Annotations sur les rapports étudiants.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class AnnotationsController
{
    /**
     * Liste les annotations d'un rapport
     */
    public function parRapport(int $rapportId): Response
    {
        $sql = "SELECT a.*, u.login_utilisateur as auteur_email,
                       COALESCE(e.nom_etu, ens.nom_ens, pa.nom_pa) as auteur_nom,
                       COALESCE(e.prenom_etu, ens.prenom_ens, pa.prenom_pa) as auteur_prenom
                FROM annotations_rapports a
                LEFT JOIN utilisateurs u ON u.id_utilisateur = a.cree_par
                LEFT JOIN etudiants e ON e.utilisateur_id = a.cree_par
                LEFT JOIN enseignants ens ON ens.utilisateur_id = a.cree_par
                LEFT JOIN personnel_admin pa ON pa.utilisateur_id = a.cree_par
                WHERE a.rapport_id = :rapport
                ORDER BY a.page, a.position_y, a.created_at";

        $stmt = Model::raw($sql, ['rapport' => $rapportId]);
        $annotations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return Response::json([
            'success' => true,
            'data' => $annotations,
        ]);
    }

    /**
     * Crée une annotation
     */
    public function store(): Response
    {
        $rapportId = (int) Request::post('rapport_id');
        $page = (int) Request::post('page');
        $positionX = Request::post('position_x');
        $positionY = Request::post('position_y');
        $texte = Request::post('texte');
        $type = Request::post('type') ?? 'commentaire';
        $utilisateurId = Request::session('utilisateur_id') ?? 0;

        if (empty($rapportId) || empty($texte)) {
            return Response::json(['error' => 'Le rapport et le texte sont requis'], 422);
        }

        $annotation = new AnnotationRapport([
            'rapport_id' => $rapportId,
            'page' => $page,
            'position_x' => $positionX,
            'position_y' => $positionY,
            'texte' => $texte,
            'type' => $type,
            'cree_par' => (int) $utilisateurId,
        ]);
        $annotation->save();

        return Response::json([
            'success' => true,
            'message' => 'Annotation créée',
            'data' => ['id' => $annotation->getId()],
        ], 201);
    }

    /**
     * Met à jour une annotation
     */
    public function update(int $id): Response
    {
        $annotation = AnnotationRapport::find($id);
        if ($annotation === null) {
            return Response::json(['error' => 'Annotation non trouvée'], 404);
        }

        $utilisateurId = Request::session('utilisateur_id');

        // Vérifier que l'utilisateur est l'auteur
        if ((int) $annotation->cree_par !== (int) $utilisateurId) {
            return Response::json(['error' => 'Non autorisé'], 403);
        }

        $texte = Request::post('texte');
        if ($texte) {
            $annotation->texte = $texte;
        }

        $resolue = Request::post('resolue');
        if ($resolue !== null) {
            $annotation->resolue = $resolue === '1' || $resolue === true;
            if ($annotation->resolue) {
                $annotation->resolue_le = date('Y-m-d H:i:s');
                $annotation->resolue_par = $utilisateurId;
            }
        }

        $annotation->modifie_le = date('Y-m-d H:i:s');
        $annotation->save();

        return Response::json([
            'success' => true,
            'message' => 'Annotation mise à jour',
            'data' => $annotation->toArray(),
        ]);
    }

    /**
     * Supprime une annotation
     */
    public function destroy(int $id): Response
    {
        $annotation = AnnotationRapport::find($id);
        if ($annotation === null) {
            return Response::json(['error' => 'Annotation non trouvée'], 404);
        }

        $utilisateurId = Request::session('utilisateur_id');

        // Vérifier que l'utilisateur est l'auteur
        if ((int) $annotation->cree_par !== (int) $utilisateurId) {
            return Response::json(['error' => 'Non autorisé'], 403);
        }

        $annotation->delete();

        return Response::json([
            'success' => true,
            'message' => 'Annotation supprimée',
        ]);
    }
}
