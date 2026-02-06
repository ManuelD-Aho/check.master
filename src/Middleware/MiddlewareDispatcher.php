<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use DI\Container;
use App\App;

class MiddlewareDispatcher implements RequestHandlerInterface
{
    private Container $container;
    private App $app;
    private array $middlewares = [];
    private int $index = 0;

    public function __construct(Container $container, App $app)
    {
        $this->container = $container;
        $this->app = $app;
        $this->loadMiddlewares();
    }

    private function loadMiddlewares(): void
    {
        $this->middlewares = [
            SessionMiddleware::class,
            CsrfMiddleware::class,
            MaintenanceModeMiddleware::class,
            RateLimitMiddleware::class,
            AuthenticationMiddleware::class,
            PermissionMiddleware::class,
            AuditMiddleware::class,
        ];
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->index < count($this->middlewares)) {
            $middlewareClass = $this->middlewares[$this->index];
            $this->index++;
            
            $middleware = $this->container->get($middlewareClass);
            
            return $middleware->process($request, $this);
        }
        
        return $this->app->dispatch($request);
    }

    public function reset(): void
    {
        $this->index = 0;
    }
}
