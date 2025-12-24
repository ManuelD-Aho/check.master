<?php

declare(strict_types=1);

namespace App\Controllers\Scolarite;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Notes Scolarité
 */
class NotesController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/scolarite/notes.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $sql = "SELECT n.*, s.date_soutenance, e.nom_etu, e.prenom_etu 
                FROM notes_soutenance n
                JOIN soutenances s ON n.soutenance_id = s.id_soutenance
                JOIN dossiers_etudiants d ON s.dossier_id = d.id_dossier
                JOIN etudiants e ON d.etudiant_id = e.id_etudiant
                ORDER BY s.date_soutenance DESC";
        $stmt = Model::raw($sql);
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function export(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        return JsonResponse::success(['message' => 'Export en cours...']);
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'notes', 'lire');
    }
}
