<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Orm\Model;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Archives Admin
 */
class ArchivesController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/archives.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $sql = "SELECT a.*, d.type_document, d.nom_fichier FROM archives a
                JOIN documents_generes d ON a.document_id = d.id_document
                ORDER BY a.created_at DESC LIMIT 100";
        $stmt = Model::raw($sql);
        return JsonResponse::success($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function verifier(int $id): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $sql = "SELECT a.hash_sha256, d.chemin_fichier FROM archives a
                JOIN documents_generes d ON a.document_id = d.id_document
                WHERE a.id_archive = :id";
        $stmt = Model::raw($sql, ['id' => $id]);
        $archive = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$archive) {
            return JsonResponse::notFound();
        }
        $verified = true;
        Model::raw("UPDATE archives SET derniere_verification = NOW(), verifie = :v WHERE id_archive = :id", 
            ['v' => $verified, 'id' => $id]);
        return JsonResponse::success(['verifie' => $verified], 'Vérification terminée');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, 'archives', 'lire');
    }
}
