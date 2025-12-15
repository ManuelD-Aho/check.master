<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Security\ServiceAuthentification;
use App\Services\Security\ServicePermissions;
use App\Models\SessionActive;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;
use Src\Support\CSRF;

/**
 * Contrôleur d'Authentification
 * 
 * Gère la connexion, déconnexion, et gestion des sessions.
 * 
 * @see Constitution IV - Controllers ≤50 lignes
 */
class AuthController
{
    private ServiceAuthentification $authService;

    public function __construct()
    {
        $this->authService = new ServiceAuthentification();
    }

    /**
     * GET / - Affiche la page de connexion
     */
    public function showLogin(): Response
    {
        // Si déjà connecté, rediriger vers dashboard
        if (Auth::check()) {
            return Response::redirect('/dashboard');
        }

        // Inclure la vue de connexion
        ob_start();
        include dirname(__DIR__, 2) . '/public/page_connexion.php';
        $content = ob_get_clean();

        return Response::html($content);
    }

    /**
     * POST / - Traite la connexion
     */
    public function login(): Response
    {
        $email = trim(Request::post('email', ''));
        $password = Request::post('password', '');

        // Validation basique
        if ($email === '' || $password === '') {
            return $this->redirectLoginWithError('Veuillez remplir tous les champs.');
        }

        // Authentification
        $result = $this->authService->authentifier($email, $password);

        if (!$result['success']) {
            return $this->redirectLoginWithError($result['error'] ?? 'Erreur de connexion');
        }

        // Définir le cookie de session
        $this->setSessionCookie($result['token']);

        // Redirection après login
        $redirectUrl = $this->getRedirectAfterLogin();
        return Response::redirect($redirectUrl);
    }

    /**
     * GET /logout - Déconnexion
     */
    public function logout(): Response
    {
        $token = Request::cookie('session_token');

        if ($token !== null) {
            $this->authService->supprimerSession($token);
        }

        // Supprimer le cookie
        $this->clearSessionCookie();

        // Réinitialiser Auth
        Auth::logout();

        return Response::redirect('/');
    }

    /**
     * GET /admin/sessions - Liste les sessions d'un utilisateur (admin)
     */
    public function listSessions(): JsonResponse
    {
        $userId = Auth::id();
        if ($userId === null || !ServicePermissions::estAdministrateur($userId)) {
            return JsonResponse::forbidden();
        }

        $targetUserId = (int) Request::query('user_id', 0);
        if ($targetUserId <= 0) {
            return JsonResponse::error('ID utilisateur invalide', 'INVALID_USER');
        }

        $sessions = SessionActive::getSessionsUtilisateur($targetUserId);

        $data = array_map(fn($s) => [
            'id' => $s->getId(),
            'ip' => $s->ip_adresse,
            'user_agent' => $s->user_agent,
            'derniere_activite' => $s->derniere_activite,
            'expire_a' => $s->expire_a,
        ], $sessions);

        return JsonResponse::success($data);
    }

    /**
     * POST /admin/sessions/kill - Force la déconnexion (admin)
     */
    public function forceLogout(): JsonResponse
    {
        $userId = Auth::id();
        if ($userId === null || !ServicePermissions::estAdministrateur($userId)) {
            return JsonResponse::forbidden();
        }

        $sessionId = (int) Request::post('session_id', 0);
        if ($sessionId <= 0) {
            return JsonResponse::error('ID session invalide', 'INVALID_SESSION');
        }

        $result = $this->authService->forcerDeconnexion($sessionId, $userId);

        if (!$result) {
            return JsonResponse::error('Session non trouvée', 'SESSION_NOT_FOUND', 404);
        }

        return JsonResponse::success(null, 'Session terminée avec succès');
    }

    /**
     * Redirige vers login avec message d'erreur
     */
    private function redirectLoginWithError(string $message): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_error'] = $message;
        return Response::redirect('/');
    }

    /**
     * Définit le cookie de session
     */
    private function setSessionCookie(string $token): void
    {
        $expire = time() + (8 * 3600); // 8 heures
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        setcookie('session_token', $token, [
            'expires' => $expire,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Supprime le cookie de session
     */
    private function clearSessionCookie(): void
    {
        setcookie('session_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
        ]);
    }

    /**
     * Récupère l'URL de redirection après login
     */
    private function getRedirectAfterLogin(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $url = $_SESSION['redirect_after_login'] ?? '/dashboard';
        unset($_SESSION['redirect_after_login']);

        return $url;
    }
}
