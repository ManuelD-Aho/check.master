<?php

declare(strict_types=1);

namespace Src;

use Src\Http\Request;
use Src\Http\Response;
use Src\Exceptions\NotFoundException;
use Src\Kernel;

/**
 * Routeur HTTP pour CheckMaster
 * 
 * Gère le mapping des URLs vers les contrôleurs.
 * Compatible avec le format AltoRouter pour les routes existantes.
 */
class Router
{
    /**
     * Routes enregistrées
     * @var array<string, array<string, array{controller: string, action: string, middleware: array, name: string}>>
     */
    private array $routes = [];

    /**
     * Routes nommées pour reverse routing
     * @var array<string, string>
     */
    private array $namedRoutes = [];

    /**
     * Préfixe de namespace des contrôleurs
     */
    private string $controllerNamespace = 'App\\Controllers\\';

    /**
     * Instance du kernel pour les middleware
     */
    private ?Kernel $kernel = null;

    /**
     * Définit le kernel
     */
    public function setKernel(Kernel $kernel): self
    {
        $this->kernel = $kernel;
        return $this;
    }

    /**
     * Enregistre une route (format AltoRouter compatible)
     * 
     * @param string $methods Méthodes HTTP séparées par | (ex: "GET|POST")
     * @param string $uri URI de la route
     * @param string $handler Handler au format "Controller#action" ou "Controller@action"
     * @param string|null $name Nom de la route
     * @param array $middleware Middleware de route
     */
    public function map(string $methods, string $uri, string $handler, ?string $name = null, array $middleware = []): self
    {
        // Parser le handler (supporte # et @)
        $separator = strpos($handler, '#') !== false ? '#' : '@';
        [$controller, $action] = explode($separator, $handler);
        
        // Enregistrer pour chaque méthode
        foreach (explode('|', $methods) as $method) {
            $method = strtoupper(trim($method));
            $this->routes[$method][$uri] = [
                'controller' => $controller,
                'action' => $action,
                'middleware' => $middleware,
                'name' => $name ?? '',
            ];
        }
        
        // Enregistrer le nom de route
        if ($name !== null) {
            $this->namedRoutes[$name] = $uri;
        }
        
        return $this;
    }

    /**
     * Enregistre une route GET
     */
    public function get(string $uri, string $handler, array $middleware = []): self
    {
        return $this->map('GET', $uri, $handler, null, $middleware);
    }

    /**
     * Enregistre une route POST
     */
    public function post(string $uri, string $handler, array $middleware = []): self
    {
        return $this->map('POST', $uri, $handler, null, $middleware);
    }

    /**
     * Enregistre une route pour plusieurs méthodes
     */
    public function match(array $methods, string $uri, string $handler, array $middleware = []): self
    {
        return $this->map(implode('|', $methods), $uri, $handler, null, $middleware);
    }

    /**
     * Dispatch la requête vers le contrôleur approprié
     */
    public function dispatch(Request $request): Response
    {
        $method = $request::method();
        $uri = $request::uri();
        
        // Normaliser l'URI
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }
        
        // Chercher une route exacte
        if (isset($this->routes[$method][$uri])) {
            return $this->executeRoute($this->routes[$method][$uri], $request);
        }
        
        // Chercher une route avec paramètres
        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            if ($params = $this->matchRoute($pattern, $uri)) {
                return $this->executeRoute($route, $request, $params);
            }
        }
        
        throw new NotFoundException('Route', $uri);
    }

    /**
     * Match une route avec paramètres (format AltoRouter)
     * Supporte: [i:id] pour int, [a:slug] pour alphanum, [*:path] pour tout
     */
    private function matchRoute(string $pattern, string $uri): ?array
    {
        // Échapper les caractères spéciaux regex
        $regex = preg_quote($pattern, '#');
        
        // Convertir les patterns AltoRouter en regex
        $regex = preg_replace('/\\\\\[i:(\w+)\\\\\]/', '(?P<$1>\d+)', $regex);
        $regex = preg_replace('/\\\\\[a:(\w+)\\\\\]/', '(?P<$1>[a-zA-Z0-9_-]+)', $regex);
        $regex = preg_replace('/\\\\\[\*:(\w+)\\\\\]/', '(?P<$1>.+)', $regex);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $uri, $matches)) {
            // Filtrer uniquement les clés nommées
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }
        
        return null;
    }

    /**
     * Exécute une route
     */
    private function executeRoute(array $route, Request $request, array $params = []): Response
    {
        $controllerName = $route['controller'];
        
        // Gérer les namespaces (ex: "Admin\\SessionsController" ou "Api\\UsersController")
        $controllerClass = $this->controllerNamespace . $controllerName;
        
        $action = $route['action'];
        
        if (!class_exists($controllerClass)) {
            throw new NotFoundException('Controller', $controllerClass);
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $action)) {
            throw new NotFoundException('Action', "{$controllerClass}::{$action}");
        }
        
        // Exécuter avec middleware si kernel disponible
        if ($this->kernel !== null && !empty($route['middleware'])) {
            return $this->kernel->runRouteMiddleware(
                $route['middleware'],
                $request,
                fn() => $controller->$action(...array_values($params))
            );
        }
        
        return $controller->$action(...array_values($params));
    }

    /**
     * Génère une URL pour une route nommée
     */
    public function generate(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \InvalidArgumentException("Route '{$name}' non trouvée");
        }
        
        $url = $this->namedRoutes[$name];
        
        // Remplacer les paramètres (échapper la clé pour éviter injection regex)
        foreach ($params as $key => $value) {
            $safeKey = preg_quote($key, '/');
            $url = preg_replace('/\[[aic\*]:' . $safeKey . '\]/', (string) $value, $url);
        }
        
        return $url;
    }

    /**
     * Charge les routes depuis un fichier
     */
    public function loadRoutes(string $path): self
    {
        $router = $this;
        require $path;
        return $this;
    }

    /**
     * Retourne toutes les routes enregistrées
     * @return array<string, array<string, array>>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
