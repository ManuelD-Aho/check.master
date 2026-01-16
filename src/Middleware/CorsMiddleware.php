<?php

declare(strict_types=1);

namespace Src\Middleware;

use Src\Http\Request;
use Src\Http\Response;

/**
 * CORS Middleware - Gestion Cross-Origin Resource Sharing
 * 
 * @package Src\Middleware
 */
class CorsMiddleware
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'allowed_origins' => ['*'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'exposed_headers' => [],
            'max_age' => 3600,
            'supports_credentials' => false
        ], $config);
    }

    /**
     * Gérer la requête
     *
     * @param Request $request Requête
     * @param callable $next Suivant
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if ($request->method() === 'OPTIONS') {
            return $this->handlePreflight($origin);
        }

        $response = $next($request);

        if ($this->isAllowedOrigin($origin)) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', implode(', ', $this->config['allowed_methods']));
            $response->header('Access-Control-Allow-Headers', implode(', ', $this->config['allowed_headers']));

            if (!empty($this->config['exposed_headers'])) {
                $response->header('Access-Control-Expose-Headers', implode(', ', $this->config['exposed_headers']));
            }

            if ($this->config['supports_credentials']) {
                $response->header('Access-Control-Allow-Credentials', 'true');
            }
        }

        return $response;
    }

    /**
     * Gérer preflight request
     *
     * @param string $origin Origine
     * @return Response
     */
    private function handlePreflight(string $origin): Response
    {
        $response = new Response('', 200);

        if ($this->isAllowedOrigin($origin)) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', implode(', ', $this->config['allowed_methods']));
            $response->header('Access-Control-Allow-Headers', implode(', ', $this->config['allowed_headers']));
            $response->header('Access-Control-Max-Age', (string) $this->config['max_age']);

            if ($this->config['supports_credentials']) {
                $response->header('Access-Control-Allow-Credentials', 'true');
            }
        }

        return $response;
    }

    /**
     * Vérifier si origine autorisée
     *
     * @param string $origin Origine
     * @return bool Autorisée
     */
    private function isAllowedOrigin(string $origin): bool
    {
        if (in_array('*', $this->config['allowed_origins'])) {
            return true;
        }

        return in_array($origin, $this->config['allowed_origins']);
    }
}
