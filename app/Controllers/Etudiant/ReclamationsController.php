<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Services\Security\ServiceAudit;
use App\Models\Etudiant;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;
use App\Orm\Model;

/**
 * Contrôleur Réclamations Étudiant
 */
class ReclamationsController
{
    public function index(): Response
    {
        $user = Auth::user();
        if ($user === null) {
            return Response::redirect('/');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/etudiant/reclamations.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        if ($etudiant === null) {
            return JsonResponse::notFound();
        }
        $sql = "SELECT * FROM reclamations WHERE etudiant_id = :id ORDER BY created_at DESC";
        $stmt = Model::raw($sql, ['id' => $etudiant->getId()]);
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function store(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return JsonResponse::unauthorized();
        }
        $data = Request::all();
        if (empty($data['objet']) || empty($data['message'])) {
            return JsonResponse::validationError(['objet' => ['Objet requis']]);
        }
        $etudiant = Etudiant::findByEmail($user->login_utilisateur);
        if ($etudiant === null) {
            return JsonResponse::notFound();
        }
        ServiceAudit::log('reclamation_deposee', 'reclamation', null, $data);
        return JsonResponse::success(null, 'Réclamation déposée');
    }
}
