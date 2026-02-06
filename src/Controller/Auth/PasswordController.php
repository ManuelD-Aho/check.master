<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PasswordController extends AbstractController
{
    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
    }

    public function showForgotForm(Request $request): ResponseInterface
    {
        return $this->render('auth/forgot', [
            'csrf_token' => $this->getCsrfToken(),
            'flashes' => $this->getFlashes(),
            'request' => $request,
        ]);
    }

    public function sendResetLink(Request $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect('/mot-de-passe/oublie');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/mot-de-passe/oublie');
        }

        $email = trim((string)($data['email'] ?? ''));

        if ($email === '') {
            $this->addFlash('error', 'Email requis');
            return $this->redirect('/mot-de-passe/oublie');
        }

        $token = $this->authenticationService->initiatePasswordReset($email);

        if ($token === null) {
            $this->addFlash('error', 'Impossible de traiter la demande');
            return $this->redirect('/mot-de-passe/oublie');
        }

        $_SESSION['last_password_reset_token'] = $token;

        $this->addFlash('success', 'Lien de reinitialisation envoye');

        return $this->redirect('/mot-de-passe/oublie');
    }

    public function showResetForm(Request $request): ResponseInterface
    {
        $token = (string)$request->getAttribute('token', '');

        return $this->render('auth/reset', [
            'csrf_token' => $this->getCsrfToken(),
            'token' => $token,
            'flashes' => $this->getFlashes(),
            'request' => $request,
        ]);
    }

    public function resetPassword(Request $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect('/mot-de-passe/oublie');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/mot-de-passe/oublie');
        }

        $token = (string)($data['token'] ?? $request->getAttribute('token', ''));
        $password = (string)($data['password'] ?? '');
        $confirm = (string)($data['password_confirm'] ?? '');

        if ($token === '' || $password === '') {
            $this->addFlash('error', 'Informations manquantes');
            return $this->redirect('/mot-de-passe/reinitialiser/' . $token);
        }

        if ($confirm !== '' && $confirm !== $password) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas');
            return $this->redirect('/mot-de-passe/reinitialiser/' . $token);
        }

        $success = $this->authenticationService->completePasswordReset($token, $password);

        if (!$success) {
            $this->addFlash('error', 'Token invalide ou expire');
            return $this->redirect('/mot-de-passe/reinitialiser/' . $token);
        }

        $this->addFlash('success', 'Mot de passe mis a jour');

        return $this->redirect('/login');
    }

    public function showChangeForm(Request $request): ResponseInterface
    {
        if ($this->getUser() === null) {
            return $this->redirect('/login');
        }

        return $this->render('auth/change-password', [
            'csrf_token' => $this->getCsrfToken(),
            'flashes' => $this->getFlashes(),
            'request' => $request,
        ]);
    }

    public function changePassword(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        if ($user === null || $user->getIdUtilisateur() === null) {
            return $this->redirect('/login');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect($this->resolveBackUrl($request, '/mot-de-passe/changer'));
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect($this->resolveBackUrl($request, '/mot-de-passe/changer'));
        }

        $current = (string)($data['current_password'] ?? '');
        $newPassword = (string)($data['new_password'] ?? '');
        $confirm = (string)($data['new_password_confirm'] ?? '');

        if ($current === '' || $newPassword === '') {
            $this->addFlash('error', 'Informations manquantes');
            return $this->redirect($this->resolveBackUrl($request, '/mot-de-passe/changer'));
        }

        if ($confirm !== '' && $confirm !== $newPassword) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas');
            return $this->redirect($this->resolveBackUrl($request, '/mot-de-passe/changer'));
        }

        $success = $this->authenticationService->changePassword(
            $user->getIdUtilisateur(),
            $current,
            $newPassword
        );

        if (!$success) {
            $this->addFlash('error', 'Impossible de modifier le mot de passe');
            return $this->redirect($this->resolveBackUrl($request, '/mot-de-passe/changer'));
        }

        $this->addFlash('success', 'Mot de passe mis a jour');

        return $this->redirect($this->resolveBackUrl($request, '/mot-de-passe/changer'));
    }

    private function resolveBackUrl(Request $request, string $fallback): string
    {
        $referer = $request->getHeaderLine('Referer');

        if ($referer !== '') {
            return $referer;
        }

        return $fallback;
    }
}
