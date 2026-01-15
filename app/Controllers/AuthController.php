<?php

declare(strict_types=1);

namespace App\Controllers;

use Src\Http\Request;
use Src\Http\Response;
use Src\Support\Auth;
use App\Services\Security\ServiceAuthentification;
use App\Services\Security\ServiceAudit;
use App\Services\Core\ServiceSession;

/**
 * Contrôleur d'Authentification
 * 
 * Gère la connexion, déconnexion et récupération de mot de passe.
 * Délègue la logique métier aux services.
 */
class AuthController
{
    private ServiceAuthentification $authService;

    public function __construct()
    {
        $this->authService = new ServiceAuthentification();
    }

    public function login(): Response
    {
        if (Auth::check()) {
            return Response::redirect('/dashboard');
        }
        if (Request::method() === 'POST') {
            return $this->processLogin();
        }
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/connexion.php';
        return Response::html((string) ob_get_clean());
    }

    private function processLogin(): Response
    {
        $email = trim(Request::post('email', ''));
        $password = Request::post('password', '');
        
        if (empty($email) || empty($password)) {
            ServiceSession::setFlashError('Veuillez remplir tous les champs.');
            return Response::redirect('/connexion');
        }
        
        $result = $this->authService->authentifier($email, $password);
        
        if (!$result['success']) {
            ServiceSession::setFlashError($result['error'] ?? 'Identifiants incorrects');
            return Response::redirect('/connexion');
        }
        
        ServiceSession::setCookie($result['token']);
        Auth::setUser($result['user'], $result['token']);
        
        if ($result['user']->doitChangerMotDePasse()) {
            return Response::redirect('/change-password');
        }
        
        return Response::redirect(ServiceSession::getRedirectAfterLogin());
    }

    public function logout(): Response
    {
        $token = ServiceSession::getToken();
        if ($token !== null) {
            $this->authService->supprimerSession($token);
        }
        Auth::logout();
        ServiceSession::destroy();
        return Response::redirect('/connexion');
    }

    public function forgotPassword(): Response
    {
        if (Request::method() === 'POST') {
            ServiceSession::setFlashSuccess('Si cet email existe, vous recevrez un lien.');
            return Response::redirect('/forgot-password');
        }
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/forgot_password.php';
        return Response::html((string) ob_get_clean());
    }

    public function changePassword(): Response
    {
        if (!Auth::check()) {
            return Response::redirect('/connexion');
        }
        if (Request::method() === 'POST') {
            ServiceSession::setFlashSuccess('Mot de passe modifié avec succès.');
            return Response::redirect('/dashboard');
        }
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/change_password.php';
        return Response::html((string) ob_get_clean());
    }
}
