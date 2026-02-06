<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Nyholm\Psr7\Response;
use App\Service\Auth\AuthenticationService;
use App\Entity\User\Utilisateur;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private AuthenticationService $authService;
    
    private array $publicRoutes = [
        '/login',
        '/logout',
        '/login/2fa',
        '/mot-de-passe/oublie',
        '/mot-de-passe/reinitialiser',
    ];

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        
        if ($this->isPublicRoute($path)) {
            return $handler->handle($request);
        }

        $user = $this->authenticateRequest($request);
        
        if ($user === null) {
            return $this->redirectToLogin();
        }

        if ($user->getStatutUtilisateur() !== \App\Entity\User\UtilisateurStatut::Actif) {
            return $this->redirectToLogin('Votre compte est inactif ou bloqué');
        }

        $request = $request->withAttribute('user', $user);
        $request = $request->withAttribute('user_id', $user->getIdUtilisateur());
        $request = $request->withAttribute('groupe_id', $user->getIdGroupeUtilisateur());

        return $handler->handle($request);
    }

    private function isPublicRoute(string $path): bool
    {
        foreach ($this->publicRoutes as $route) {
            if (str_starts_with($path, $route)) {
                return true;
            }
        }
        
        if (str_starts_with($path, '/assets/') || str_starts_with($path, '/api/public/')) {
            return true;
        }
        
        return false;
    }

    private function authenticateRequest(ServerRequestInterface $request): ?Utilisateur
    {
        if (isset($_SESSION['user_id'])) {
            return $this->authService->getUserById((int)$_SESSION['user_id']);
        }

        $token = $this->getTokenFromRequest($request);
        
        if ($token !== null) {
            return $this->authService->validateSession($token);
        }

        return null;
    }

    private function getTokenFromRequest(ServerRequestInterface $request): ?string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        $cookies = $request->getCookieParams();
        
        if (isset($cookies['auth_token'])) {
            return $cookies['auth_token'];
        }

        return null;
    }

    private function redirectToLogin(?string $message = null): ResponseInterface
    {
        if ($message !== null) {
            $_SESSION['flash_error'] = $message;
        }
        
        return new Response(302, ['Location' => '/login']);
    }
}
