<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Service\System\AuditService;

class AuditMiddleware implements MiddlewareInterface
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = microtime(true);
        
        $response = $handler->handle($request);
        
        $duration = microtime(true) - $startTime;
        
        $this->logRequest($request, $response, $duration);
        
        return $response;
    }

    private function logRequest(
        ServerRequestInterface $request,
        ResponseInterface $response,
        float $duration
    ): void {
        $user = $request->getAttribute('user');
        $userId = $user?->getIdUtilisateur();
        
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $statusCode = $response->getStatusCode();
        
        if ($method === 'GET' && $statusCode < 400) {
            return;
        }

        $action = $this->determineAction($method, $path);
        
        if ($action === null) {
            return;
        }

        $this->auditService->log(
            $userId,
            $action,
            $statusCode < 400 ? 'succes' : 'echec',
            null,
            null,
            null,
            null,
            $this->getClientIp($request),
            $request->getHeaderLine('User-Agent'),
            json_encode([
                'path' => $path,
                'method' => $method,
                'status' => $statusCode,
                'duration_ms' => round($duration * 1000, 2)
            ])
        );
    }

    private function determineAction(string $method, string $path): ?string
    {
        return match($method) {
            'POST' => str_contains($path, '/login') ? 'auth.login' : 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => null
        };
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();
        
        if (!empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        return $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
