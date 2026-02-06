<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Nyholm\Psr7\Response;
use App\Service\Auth\RateLimiterService;

class RateLimitMiddleware implements MiddlewareInterface
{
    private RateLimiterService $rateLimiter;
    private array $limitedPaths = ['/login', '/mot-de-passe/oublie'];

    public function __construct(RateLimiterService $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        
        if ($method !== 'POST') {
            return $handler->handle($request);
        }

        $shouldLimit = false;
        foreach ($this->limitedPaths as $limitedPath) {
            if (str_starts_with($path, $limitedPath)) {
                $shouldLimit = true;
                break;
            }
        }

        if (!$shouldLimit) {
            return $handler->handle($request);
        }

        $clientIp = $this->getClientIp($request);
        $action = $this->pathToAction($path);

        if ($this->rateLimiter->isBlocked($action, $clientIp)) {
            $remainingTime = $this->rateLimiter->getRemainingBlockTime($action, $clientIp);
            return new Response(429, [
                'Content-Type' => 'application/json',
                'Retry-After' => (string)$remainingTime
            ], json_encode([
                'error' => 'Trop de tentatives',
                'message' => "Veuillez réessayer dans $remainingTime minutes",
                'retry_after' => $remainingTime
            ]));
        }

        return $handler->handle($request);
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

    private function pathToAction(string $path): string
    {
        return match(true) {
            str_starts_with($path, '/login') => 'login',
            str_starts_with($path, '/mot-de-passe') => 'reset_password',
            default => 'unknown'
        };
    }
}
