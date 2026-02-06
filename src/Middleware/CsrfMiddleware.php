<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Nyholm\Psr7\Response;

class CsrfMiddleware implements MiddlewareInterface
{
    private array $safeMethods = ['GET', 'HEAD', 'OPTIONS'];
    private array $excludedPaths = ['/api/'];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        if (in_array($method, $this->safeMethods)) {
            return $handler->handle($request);
        }

        foreach ($this->excludedPaths as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return $handler->handle($request);
            }
        }

        $token = $this->getTokenFromRequest($request);
        $sessionToken = $_SESSION['csrf_token'] ?? null;

        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            return new Response(403, ['Content-Type' => 'text/html'], 'Token CSRF invalide');
        }

        return $handler->handle($request);
    }

    private function getTokenFromRequest(ServerRequestInterface $request): ?string
    {
        $parsedBody = $request->getParsedBody();
        
        if (is_array($parsedBody) && isset($parsedBody['_csrf_token'])) {
            return $parsedBody['_csrf_token'];
        }

        $header = $request->getHeaderLine('X-CSRF-TOKEN');
        
        if (!empty($header)) {
            return $header;
        }

        return null;
    }

    public static function generateToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    public static function getTokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
