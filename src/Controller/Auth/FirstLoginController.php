<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class FirstLoginController extends AbstractController
{
    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
    }

    public function showForm(Request $request): ResponseInterface
    {
        if ($this->getUser() === null) {
            return $this->redirect('/login');
        }

        return $this->render('auth/first-login', [
            'csrf_token' => $this->getCsrfToken(),
            'flashes' => $this->getFlashes(),
            'request' => $request,
        ]);
    }

    public function updatePassword(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        if ($user === null || $user->getIdUtilisateur() === null) {
            return $this->redirect('/login');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect('/premiere-connexion');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/premiere-connexion');
        }

        $current = (string)($data['current_password'] ?? '');
        $newPassword = (string)($data['new_password'] ?? '');
        $confirm = (string)($data['new_password_confirm'] ?? '');

        if ($current === '' || $newPassword === '') {
            $this->addFlash('error', 'Informations manquantes');
            return $this->redirect('/premiere-connexion');
        }

        if ($confirm !== '' && $confirm !== $newPassword) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas');
            return $this->redirect('/premiere-connexion');
        }

        $success = $this->authenticationService->changePassword(
            $user->getIdUtilisateur(),
            $current,
            $newPassword
        );

        if (!$success) {
            $this->addFlash('error', 'Impossible de modifier le mot de passe');
            return $this->redirect('/premiere-connexion');
        }

        $this->addFlash('success', 'Mot de passe mis a jour');

        return $this->redirect('/admin');
    }
}
