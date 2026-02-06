<?php
declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Nyholm\Psr7\Response;
use DI\Container;
use App\Middleware\MiddlewareDispatcher;

class App implements RequestHandlerInterface
{
    private Container $container;
    private Dispatcher $dispatcher;
    private MiddlewareDispatcher $middlewareDispatcher;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $this->createDispatcher();
        $this->middlewareDispatcher = new MiddlewareDispatcher($this->container, $this);
    }

    private function createDispatcher(): Dispatcher
    {
        $routes = require __DIR__ . '/../config/routes.php';
        
        return \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->middlewareDispatcher->handle($request);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $httpMethod = $request->getMethod();
        $uri = $request->getUri()->getPath();
        
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->notFoundResponse();
            
            case Dispatcher::METHOD_NOT_ALLOWED:
                return $this->methodNotAllowedResponse($routeInfo[1]);
            
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                
                foreach ($vars as $key => $value) {
                    $request = $request->withAttribute($key, $value);
                }
                
                return $this->executeHandler($handler, $request);
        }
        
        return $this->notFoundResponse();
    }

    private function executeHandler(array $handler, ServerRequestInterface $request): ResponseInterface
    {
        [$controllerClass, $method] = $handler;
        
        $controller = $this->container->get($controllerClass);
        
        return $controller->$method($request);
    }

    private function notFoundResponse(): ResponseInterface
    {
        $body = $this->renderErrorPage(404, 'Page non trouvée');
        return new Response(404, ['Content-Type' => 'text/html'], $body);
    }

    private function methodNotAllowedResponse(array $allowedMethods): ResponseInterface
    {
        $body = $this->renderErrorPage(405, 'Méthode non autorisée');
        return new Response(405, [
            'Content-Type' => 'text/html',
            'Allow' => implode(', ', $allowedMethods)
        ], $body);
    }

    private function renderErrorPage(int $code, string $message): string
    {
        $templatePath = __DIR__ . '/../templates/error/' . $code . '.php';
        
        if (file_exists($templatePath)) {
            ob_start();
            include $templatePath;
            return ob_get_clean();
        }
        
        return "<h1>$code - $message</h1>";
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
