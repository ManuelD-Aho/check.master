<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Réclamations Scolarité
 */
class ReclamationsController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/reclamations.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $sql = "SELECT r.*, e.nom_etu, e.prenom_etu FROM reclamations r
                LEFT JOIN etudiants e ON r.etudiant_id = e.id_etudiant
                ORDER BY r.created_at DESC";
        $stmt = Model::raw($sql);
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function traiter(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $reponse = Request::post('reponse', '');
        $sql = "UPDATE reclamations SET statut = 'Traitee', reponse = :rep, traite_par = :par WHERE id_reclamation = :id";
        Model::raw($sql, ['rep' => $reponse, 'par' => Auth::id(), 'id' => $id]);
        ServiceAudit::log('reclamation_traitee', 'reclamation', $id);
        return JsonResponse::success(null, 'Réclamation traitée');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'reclamations', 'modifier');
    }
}
