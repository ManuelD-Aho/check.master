<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Backup Admin
 */
class BackupController
{
    public function index(): Response
    {
        if (!$this->checkAccess()) {
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/backup.php';
        return Response::html((string) ob_get_clean());
    }

    public function list(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $backupDir = dirname(__DIR__, 3) . '/storage/backups';
        $files = is_dir($backupDir) ? scandir($backupDir) : [];
        $backups = array_filter($files, function ($f) {
            return preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{6}\.(sql|zip)$/', $f);
        });
        return JsonResponse::success(array_values($backups));
    }

    public function creer(): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        ServiceAudit::log('backup_cree', 'backup', null, ['filename' => $filename]);
        return JsonResponse::success(['filename' => $filename], 'Backup créé');
    }

    public function restaurer(string $filename): JsonResponse
    {
        if (!$this->checkAccess()) {
            return JsonResponse::forbidden();
        }
        // Security: validate filename format AND check for path traversal
        if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{6}\.(sql|zip)$/', $filename) 
            || str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            return JsonResponse::error('Nom de fichier invalide', 'INVALID_FILENAME', 400);
        }
        ServiceAudit::log('backup_restaure', 'backup', null, ['filename' => $filename]);
        return JsonResponse::success(null, 'Restauration lancée');
    }

    private function checkAccess(): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::estAdministrateur($userId);
    }
}
