<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginController extends AbstractController
{
    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
    }

    public function showLoginForm(Request $request): ResponseInterface
    {
        return $this->render('auth/login', [
            'csrf_token' => $this->getCsrfToken(),
            'flashes' => $this->getFlashes(),
            'request' => $request,
        ]);
    }

    public function login(Request $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Requete invalide');
            return $this->redirect('/login');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/login');
        }

        $login = trim((string)($data['login'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($login === '' || $password === '') {
            $this->addFlash('error', 'Identifiants manquants');
            return $this->redirect('/login');
        }

        $result = $this->authenticationService->authenticate($login, $password);

        if (!is_array($result) || !isset($result['user'])) {
            $this->addFlash('error', 'Identifiants invalides');
            return $this->redirect('/login');
        }

        $user = $result['user'];
        $token = is_string($result['token'] ?? null) ? $result['token'] : null;
        $requires2fa = (bool)($result['requires_2fa'] ?? false);

        if ($requires2fa) {
            $_SESSION['pending_2fa_user_id'] = $user->getIdUtilisateur();
            if ($token !== null) {
                $_SESSION['pending_2fa_token'] = $token;
            }
            return $this->redirect('/login/2fa');
        }

        $_SESSION['user_id'] = $user->getIdUtilisateur();
        if ($token !== null) {
            $_SESSION['auth_token'] = $token;
        }

        return $this->redirect('/admin');
    }

    public function logout(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        if ($user !== null && $user->getIdUtilisateur() !== null) {
            $this->authenticationService->logout($user->getIdUtilisateur());
        }

        unset($_SESSION['user_id'], $_SESSION['auth_token'], $_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_token']);

        return $this->redirect('/login');
    }
}
