<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Nyholm\Psr7\Response;
use App\Service\Auth\AuthorizationService;
use App\Entity\User\Utilisateur;

class PermissionMiddleware implements MiddlewareInterface
{
    private AuthorizationService $authService;

    public function __construct(AuthorizationService $authService)
    {
        $this->authService = $authService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute('user');
        
        if ($user === null) {
            return $handler->handle($request);
        }

        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        if (!$this->authService->checkRoutePermission($user, $path, $method)) {
            return $this->accessDenied($request);
        }

        return $handler->handle($request);
    }

    private function accessDenied(ServerRequestInterface $request): ResponseInterface
    {
        $acceptHeader = $request->getHeaderLine('Accept');
        
        if (str_contains($acceptHeader, 'application/json')) {
            return new Response(403, ['Content-Type' => 'application/json'], json_encode([
                'error' => 'Accès non autorisé',
                'message' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource'
            ]));
        }

        $templatePath = __DIR__ . '/../../templates/error/403.php';
        
        if (file_exists($templatePath)) {
            ob_start();
            include $templatePath;
            $content = ob_get_clean();
        } else {
            $content = '<h1>403 - Accès non autorisé</h1>';
        }

        return new Response(403, ['Content-Type' => 'text/html'], $content);
    }
}
