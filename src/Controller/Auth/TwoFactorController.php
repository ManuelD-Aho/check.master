<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class TwoFactorController extends AbstractController
{
    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
    }

    public function showSetup(Request $request): ResponseInterface
    {
        if ($this->getUser() === null && !isset($_SESSION['pending_2fa_user_id'])) {
            return $this->redirect('/login');
        }

        $template = isset($_SESSION['pending_2fa_user_id']) ? 'auth/2fa-verify' : 'auth/2fa-setup';

        return $this->render($template, [
            'csrf_token' => $this->getCsrfToken(),
            'flashes' => $this->getFlashes(),
            'request' => $request,
        ]);
    }

    public function enable(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        if ($user === null || $user->getIdUtilisateur() === null) {
            return $this->redirect('/login');
        }

        $data = $request->getParsedBody();

        if (is_array($data)) {
            $csrfToken = (string)($data['_csrf_token'] ?? '');
            if (!$this->validateCsrf($csrfToken)) {
                $this->addFlash('error', 'Token CSRF invalide');
                return $this->redirect('/compte/2fa');
            }
        }

        $payload = $this->authenticationService->enable2FA($user->getIdUtilisateur());

        if ($payload === []) {
            $this->addFlash('error', 'Impossible d\'activer la double authentification');
            return $this->redirect('/compte/2fa');
        }

        return $this->render('auth/2fa-setup', [
            'csrf_token' => $this->getCsrfToken(),
            'flashes' => $this->getFlashes(),
            'qr_uri' => $payload['qr_uri'] ?? null,
            'recovery_codes' => $payload['recovery_codes'] ?? [],
            'request' => $request,
        ]);
    }

    public function verify(Request $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect('/login/2fa');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/login/2fa');
        }

        $code = (string)($data['code'] ?? '');
        $pendingUserId = isset($_SESSION['pending_2fa_user_id']) ? (int)$_SESSION['pending_2fa_user_id'] : 0;

        if ($code === '' || $pendingUserId <= 0) {
            $this->addFlash('error', 'Code invalide');
            return $this->redirect('/login/2fa');
        }

        $valid = $this->authenticationService->verify2FA($pendingUserId, $code);

        if (!$valid) {
            $this->addFlash('error', 'Code incorrect');
            return $this->redirect('/login/2fa');
        }

        $_SESSION['user_id'] = $pendingUserId;
        if (isset($_SESSION['pending_2fa_token'])) {
            $_SESSION['auth_token'] = $_SESSION['pending_2fa_token'];
        }

        unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_token']);

        return $this->redirect('/admin');
    }

    public function disable(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        if ($user === null || $user->getIdUtilisateur() === null) {
            return $this->redirect('/login');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect('/compte/2fa');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/compte/2fa');
        }

        $code = (string)($data['code'] ?? '');
        if ($code === '') {
            $this->addFlash('error', 'Code requis');
            return $this->redirect('/compte/2fa');
        }

        $success = $this->authenticationService->disable2FA($user->getIdUtilisateur(), $code);

        if (!$success) {
            $this->addFlash('error', 'Impossible de desactiver la double authentification');
            return $this->redirect('/compte/2fa');
        }

        $this->addFlash('success', 'Double authentification desactivee');

        return $this->redirect('/compte/2fa');
    }
}
