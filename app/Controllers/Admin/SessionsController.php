<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAuthentification;
use App\Services\Security\ServiceAudit;
use App\Models\SessionActive;
use App\Models\Utilisateur;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur de Gestion des Sessions (Admin)
 * 
 * Permet aux administrateurs de voir et gérer les sessions actives.
 * 
 * @see PRD RF-010 - Sessions multi-appareils
 * @see Constitution IV - Controllers ≤50 lignes
 */
class SessionsController
{
    private ServiceAuthentification $authService;

    public function __construct()
    {
        $this->authService = new ServiceAuthentification();
    }

    /**
     * GET /admin/sessions - Page de gestion des sessions
     */
    public function index(): Response
    {
        // Vérifier permissions admin
        $userId = Auth::id();
        if ($userId === null || !ServicePermissions::estAdministrateur($userId)) {
            return Response::redirect('/dashboard');
        }

        // Récupérer toutes les sessions actives
        $sessions = $this->getAllActiveSessions();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/sessions_list.php';
        $content = ob_get_clean();

        return Response::html($content);
    }

    /**
     * GET /api/admin/sessions - API JSON des sessions
     */
    public function list(): JsonResponse
    {
        $userId = Auth::id();
        if ($userId === null || !ServicePermissions::estAdministrateur($userId)) {
            return JsonResponse::forbidden();
        }

        $sessions = $this->getAllActiveSessions();

        return JsonResponse::success($sessions);
    }

    /**
     * POST /api/admin/sessions/{id}/kill - Force la déconnexion
     */
    public function kill(int $id): JsonResponse
    {
        $adminId = Auth::id();
        if ($adminId === null || !ServicePermissions::estAdministrateur($adminId)) {
            return JsonResponse::forbidden();
        }

        $result = $this->authService->forcerDeconnexion($id, $adminId);

        if (!$result) {
            return JsonResponse::error('Session non trouvée', 'SESSION_NOT_FOUND', 404);
        }

        return JsonResponse::success(null, 'Session terminée avec succès');
    }

    /**
     * Récupère toutes les sessions actives avec infos utilisateur
     */
    private function getAllActiveSessions(): array
    {
        $sql = "SELECT s.*, u.nom_utilisateur, u.login_utilisateur 
                FROM sessions_actives s
                JOIN utilisateurs u ON s.utilisateur_id = u.id_utilisateur
                WHERE s.expire_a > NOW()
                ORDER BY s.derniere_activite DESC";

        try {
            $stmt = \App\Orm\Model::raw($sql);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                return [
                    'id' => (int) $row['id_session'],
                    'utilisateur_id' => (int) $row['utilisateur_id'],
                    'nom_utilisateur' => $row['nom_utilisateur'],
                    'login' => $row['login_utilisateur'],
                    'ip_adresse' => $row['ip_adresse'],
                    'user_agent' => $this->parseUserAgent($row['user_agent']),
                    'derniere_activite' => $row['derniere_activite'],
                    'expire_a' => $row['expire_a'],
                    'temps_relatif' => $this->tempsRelatif($row['derniere_activite']),
                ];
            }, $rows);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Parse le User-Agent pour un affichage lisible
     */
    private function parseUserAgent(string $ua): string
    {
        $browser = 'Inconnu';
        $os = 'Inconnu';

        // Détection navigateur
        if (str_contains($ua, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($ua, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($ua, 'Safari')) {
            $browser = 'Safari';
        } elseif (str_contains($ua, 'Edge')) {
            $browser = 'Edge';
        }

        // Détection OS
        if (str_contains($ua, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($ua, 'Mac')) {
            $os = 'macOS';
        } elseif (str_contains($ua, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($ua, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($ua, 'iOS') || str_contains($ua, 'iPhone')) {
            $os = 'iOS';
        }

        return "{$browser} / {$os}";
    }

    /**
     * Calcule le temps relatif depuis une date
     */
    private function tempsRelatif(string $datetime): string
    {
        $diff = time() - strtotime($datetime);

        if ($diff < 60) {
            return 'à l\'instant';
        } elseif ($diff < 3600) {
            $min = floor($diff / 60);
            return "il y a {$min} min";
        } elseif ($diff < 86400) {
            $h = floor($diff / 3600);
            return "il y a {$h}h";
        } else {
            $j = floor($diff / 86400);
            return "il y a {$j}j";
        }
    }
}
