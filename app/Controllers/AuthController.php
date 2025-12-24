<?php

declare(strict_types=1);

namespace App\Controllers;

use Src\Http\Request;
use Src\Http\Response;
use Src\Support\Auth;

/**
 * Contrôleur d'Authentification
 * 
 * Gère la connexion, déconnexion et récupération de mot de passe.
 */
class AuthController
{
    /**
     * GET|POST /connexion - Affiche le formulaire de connexion ou traite la soumission
     */
    public function login(): Response
    {
        // Si déjà connecté, rediriger vers le dashboard
        if (Auth::check()) {
            return Response::redirect('/dashboard');
        }

        // Si POST, traiter la connexion
        if (Request::method() === 'POST') {
            return $this->processLogin();
        }

        // Afficher le formulaire de connexion
        return $this->renderLoginForm();
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    private function processLogin(): Response
    {
        $email = trim(Request::post('email', ''));
        $password = Request::post('password', '');

        // Validation basique
        if (empty($email) || empty($password)) {
            $this->setFlashError('Veuillez remplir tous les champs.');
            return Response::redirect('/connexion');
        }

        // TODO: Implémenter la véritable authentification avec ServiceAuthentification
        // Pour l'instant, on redirige avec un message d'erreur
        $this->setFlashError('Authentification non implémentée. Veuillez réessayer plus tard.');
        return Response::redirect('/connexion');
    }

    /**
     * GET /logout - Déconnexion
     */
    public function logout(): Response
    {
        Auth::logout();
        
        // Détruire la session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        return Response::redirect('/connexion');
    }

    /**
     * GET|POST /forgot-password - Mot de passe oublié
     */
    public function forgotPassword(): Response
    {
        if (Request::method() === 'POST') {
            $email = trim(Request::post('email', ''));
            
            if (!empty($email)) {
                // TODO: Implémenter l'envoi d'email de réinitialisation
                $this->setFlashSuccess('Si cet email existe, vous recevrez un lien de réinitialisation.');
            }
            
            return Response::redirect('/forgot-password');
        }

        // Afficher le formulaire de mot de passe oublié
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mot de passe oublié - CheckMaster</title>
            <style>
                body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1a365d 0%, #2b4c7e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                .container { background: white; padding: 2rem; border-radius: 1rem; max-width: 400px; width: 100%; }
                h1 { color: #1a365d; margin-bottom: 1rem; }
                input { width: 100%; padding: 0.75rem; margin: 0.5rem 0; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
                button { width: 100%; padding: 0.75rem; background: #1a365d; color: white; border: none; border-radius: 0.5rem; cursor: pointer; }
                a { color: #38b2ac; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Mot de passe oublié</h1>
                <form method="POST">
                    <input type="email" name="email" placeholder="Votre adresse email" required>
                    <button type="submit">Envoyer le lien de réinitialisation</button>
                </form>
                <p style="margin-top: 1rem;"><a href="/connexion">Retour à la connexion</a></p>
            </div>
        </body>
        </html>
        <?php
        return Response::html(ob_get_clean());
    }

    /**
     * GET|POST /change-password - Changement de mot de passe
     */
    public function changePassword(): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return Response::redirect('/connexion');
        }

        if (Request::method() === 'POST') {
            // TODO: Implémenter le changement de mot de passe
            $this->setFlashSuccess('Mot de passe modifié avec succès.');
            return Response::redirect('/dashboard');
        }

        // Afficher le formulaire de changement de mot de passe
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/change_password.php';
        return Response::html(ob_get_clean());
    }

    /**
     * Affiche le formulaire de connexion
     */
    private function renderLoginForm(): Response
    {
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/connexion.php';
        return Response::html(ob_get_clean());
    }

    /**
     * Définit un message flash d'erreur
     */
    private function setFlashError(string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_error'] = $message;
    }

    /**
     * Définit un message flash de succès
     */
    private function setFlashSuccess(string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_success'] = $message;
    }
}
