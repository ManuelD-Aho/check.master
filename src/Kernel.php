<?php

declare(strict_types=1);

namespace Src;

use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Exceptions\AppException;
use Src\Exceptions\ValidationException;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ForbiddenException;
use Src\Exceptions\UnauthorizedException;
use Src\Exceptions\MaintenanceException;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\JsonMiddleware;
use App\Middleware\LoggingMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\SecurityHeadersMiddleware;
use App\Middleware\CorrelationIdMiddleware;
use App\Middleware\MaintenanceMiddleware;

/**
 * Noyau de l'application CheckMaster
 * 
 * Gère le cycle de vie complet de la requête HTTP:
 * - Pipeline middleware
 * - Gestion des erreurs robuste
 * - Intégration avec le Container DI
 */
class Kernel
{
    /**
     * Instance du conteneur
     */
    protected ?Container $container = null;

    /**
     * Instance du routeur
     */
    protected ?Router $router = null;

    /**
     * Indique si le kernel a été initialisé
     */
    protected bool $booted = false;

    /**
     * Middleware global (exécuté à chaque requête)
     * Ordre d'exécution: du premier au dernier
     *
     * @var array<class-string>
     */
    protected array $middleware = [
        CorrelationIdMiddleware::class,
        SecurityHeadersMiddleware::class,
        CorsMiddleware::class,
        MaintenanceMiddleware::class,
        RateLimitMiddleware::class,
        LoggingMiddleware::class,
        CsrfMiddleware::class,
    ];

    /**
     * Middleware de route (exécuté selon la configuration des routes)
     *
     * @var array<string, class-string>
     */
    protected array $routeMiddleware = [
        'auth' => AuthMiddleware::class,
        'json' => JsonMiddleware::class,
        'api' => \App\Middleware\ApiKeyMiddleware::class,
        'permission' => \App\Middleware\PermissionMiddleware::class,
        'workflow' => \App\Middleware\WorkflowGateMiddleware::class,
        'throttle' => \App\Middleware\ThrottleMiddleware::class,
        'session.expire' => \App\Middleware\SessionExpirationMiddleware::class,
        'content.negotiate' => \App\Middleware\ContentNegotiationMiddleware::class,
    ];

    /**
     * Groupes de middleware prédéfinis
     *
     * @var array<string, array<string>>
     */
    protected array $middlewareGroups = [
        'web' => [
            'auth',
        ],
        'api' => [
            'api',
            'json',
            'throttle',
        ],
    ];

    /**
     * Handlers d'exception personnalisés
     *
     * @var array<class-string, callable>
     */
    protected array $exceptionHandlers = [];

    /**
     * Constructeur
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container ?? Container::getInstance();
    }

    /**
     * Initialise le kernel
     */
    public function boot(): self
    {
        if ($this->booted) {
            return $this;
        }

        // Enregistrer le kernel dans le container
        $this->container->instance(self::class, $this);
        $this->container->instance('kernel', $this);

        // Enregistrer les handlers d'exception par défaut
        $this->registerExceptionHandlers();

        $this->booted = true;

        return $this;
    }

    /**
     * Définit le routeur
     */
    public function setRouter(Router $router): self
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Retourne le conteneur
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Gère la requête entrante
     */
    public function handle(Request $request): Response
    {
        $this->boot();

        try {
            // Exécuter le pipeline de middleware global
            return $this->runMiddlewarePipeline($request, function (Request $req) {
                return $this->dispatch($req);
            });
        } catch (\Throwable $e) {
            return $this->handleException($e, $request);
        }
    }

    /**
     * Dispatch la requête vers le contrôleur approprié
     */
    protected function dispatch(Request $request): Response
    {
        // Si un routeur est défini, l'utiliser
        if ($this->router !== null) {
            return $this->router->dispatch($request);
        }

        // Fallback basique
        $uri = $request::uri();

        // Page d'accueil
        if ($uri === '/' || $uri === '') {
            return Response::html($this->getWelcomePage(), 200);
        }

        // Route non trouvée
        throw new NotFoundException('Page', $uri);
    }

    /**
     * Exécute le pipeline de middleware
     *
     * @param callable(Request): Response $destination
     */
    protected function runMiddlewarePipeline(Request $request, callable $destination): Response
    {
        $pipeline = array_reverse($this->middleware);
        $next = $destination;

        foreach ($pipeline as $middlewareClass) {
            $next = function (Request $req) use ($middlewareClass, $next): Response {
                $middleware = $this->resolveMiddleware($middlewareClass);
                $result = $middleware->handle($next);
                
                // Si le middleware retourne déjà une Response, la retourner
                if ($result instanceof Response) {
                    return $result;
                }
                
                // Sinon c'est le résultat du next()
                return $result;
            };
        }

        return $next($request);
    }

    /**
     * Exécute des middleware de route spécifiques
     *
     * @param array<string> $middlewareNames
     * @param callable(Request): Response $destination
     */
    public function runRouteMiddleware(array $middlewareNames, Request $request, callable $destination): Response
    {
        $middlewareClasses = [];

        foreach ($middlewareNames as $name) {
            // Vérifier si c'est un groupe
            if (isset($this->middlewareGroups[$name])) {
                foreach ($this->middlewareGroups[$name] as $groupMiddleware) {
                    if (isset($this->routeMiddleware[$groupMiddleware])) {
                        $middlewareClasses[] = $this->routeMiddleware[$groupMiddleware];
                    }
                }
            } elseif (isset($this->routeMiddleware[$name])) {
                $middlewareClasses[] = $this->routeMiddleware[$name];
            }
        }

        if (empty($middlewareClasses)) {
            return $destination($request);
        }

        $pipeline = array_reverse($middlewareClasses);
        $next = $destination;

        foreach ($pipeline as $middlewareClass) {
            $next = function (Request $req) use ($middlewareClass, $next): Response {
                $middleware = $this->resolveMiddleware($middlewareClass);
                $result = $middleware->handle($next);
                return $result instanceof Response ? $result : $result;
            };
        }

        return $next($request);
    }

    /**
     * Résout une classe de middleware
     */
    protected function resolveMiddleware(string $middlewareClass): object
    {
        if ($this->container->has($middlewareClass)) {
            return $this->container->make($middlewareClass);
        }

        return new $middlewareClass();
    }

    /**
     * Enregistre les handlers d'exception par défaut
     */
    protected function registerExceptionHandlers(): void
    {
        $this->exceptionHandlers[ValidationException::class] = function (ValidationException $e, Request $request): Response {
            if ($this->wantsJson($request)) {
                return JsonResponse::error(
                    $e->getMessage(),
                    $e->getErrors(),
                    $e->getHttpCode()
                );
            }
            return Response::html($this->renderValidationError($e), $e->getHttpCode());
        };

        $this->exceptionHandlers[NotFoundException::class] = function (NotFoundException $e, Request $request): Response {
            if ($this->wantsJson($request)) {
                return JsonResponse::notFound($e->getMessage());
            }
            return Response::html($this->render404($e), 404);
        };

        $this->exceptionHandlers[ForbiddenException::class] = function (ForbiddenException $e, Request $request): Response {
            if ($this->wantsJson($request)) {
                return JsonResponse::forbidden($e->getMessage());
            }
            return Response::html($this->render403($e), 403);
        };

        $this->exceptionHandlers[UnauthorizedException::class] = function (UnauthorizedException $e, Request $request): Response {
            if ($this->wantsJson($request)) {
                return JsonResponse::unauthorized($e->getMessage());
            }
            return Response::redirect('/login');
        };

        $this->exceptionHandlers[MaintenanceException::class] = function (MaintenanceException $e, Request $request): Response {
            if ($this->wantsJson($request)) {
                return JsonResponse::error($e->getMessage(), [], 503);
            }
            return Response::html($this->renderMaintenance($e), 503);
        };
    }

    /**
     * Ajoute un handler d'exception personnalisé
     *
     * @param class-string $exceptionClass
     */
    public function addExceptionHandler(string $exceptionClass, callable $handler): self
    {
        $this->exceptionHandlers[$exceptionClass] = $handler;
        return $this;
    }

    /**
     * Gère les exceptions non attrapées
     */
    protected function handleException(\Throwable $e, Request $request): Response
    {
        // Logger l'erreur
        $this->logException($e);

        // Chercher un handler spécifique
        foreach ($this->exceptionHandlers as $exceptionClass => $handler) {
            if ($e instanceof $exceptionClass) {
                return $handler($e, $request);
            }
        }

        // Handler par défaut pour AppException
        if ($e instanceof AppException) {
            return $this->handleAppException($e, $request);
        }

        // Handler générique
        return $this->handleGenericException($e, $request);
    }

    /**
     * Gère une AppException
     */
    protected function handleAppException(AppException $e, Request $request): Response
    {
        $statusCode = $e->getHttpCode();

        if ($this->wantsJson($request)) {
            return new JsonResponse($e->toArray(), $statusCode);
        }

        $content = $this->isDebug()
            ? $this->renderDebugError($e)
            : $this->renderProductionError($statusCode, $e->getMessage());

        return Response::html($content, $statusCode);
    }

    /**
     * Gère une exception générique
     */
    protected function handleGenericException(\Throwable $e, Request $request): Response
    {
        $statusCode = 500;

        if ($this->wantsJson($request)) {
            return JsonResponse::error(
                $this->isDebug() ? $e->getMessage() : 'Une erreur interne est survenue',
                $this->isDebug() ? ['trace' => $e->getTraceAsString()] : [],
                $statusCode
            );
        }

        $content = $this->isDebug()
            ? $this->renderDebugError($e)
            : $this->renderProductionError($statusCode, 'Une erreur interne est survenue');

        return Response::html($content, $statusCode);
    }

    /**
     * Log l'exception
     */
    protected function logException(\Throwable $e): void
    {
        try {
            if (function_exists('logger')) {
                logger()->error($e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        } catch (\Throwable $logError) {
            // Silencieux si le logger échoue
            error_log("Exception logging failed: " . $logError->getMessage());
        }
    }

    /**
     * Vérifie si la requête attend du JSON
     */
    protected function wantsJson(Request $request): bool
    {
        return $request::isAjax() 
            || $request::header('Accept') === 'application/json'
            || str_contains($request::header('Accept') ?? '', 'application/json')
            || str_starts_with($request::uri(), '/api/');
    }

    /**
     * Vérifie si on est en mode debug
     */
    protected function isDebug(): bool
    {
        return defined('APP_DEBUG') && APP_DEBUG === true;
    }

    /**
     * Rendu de la page d'accueil
     */
    protected function getWelcomePage(): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>CheckMaster - Gestion des Mémoires</title>
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
                .container { text-align: center; color: white; }
                h1 { font-size: 3rem; margin-bottom: 0.5rem; }
                p { font-size: 1.2rem; opacity: 0.9; }
                a { color: white; text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>✓ CheckMaster</h1>
                <p>Système de Gestion des Mémoires</p>
                <p><a href="/login">Se connecter</a></p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Rendu erreur de validation
     */
    protected function renderValidationError(ValidationException $e): string
    {
        $errors = $e->getErrors();
        $errorList = '';
        foreach ($errors as $field => $message) {
            $errorList .= "<li><strong>{$field}</strong>: {$message}</li>";
        }

        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head><meta charset="UTF-8"><title>Erreur de validation</title></head>
        <body style="font-family: sans-serif; padding: 2rem;">
            <h1>Erreur de validation</h1>
            <ul>{$errorList}</ul>
            <a href="javascript:history.back()">Retour</a>
        </body>
        </html>
        HTML;
    }

    /**
     * Rendu page 404
     */
    protected function render404(NotFoundException $e): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head><meta charset="UTF-8"><title>Page non trouvée</title></head>
        <body style="font-family: sans-serif; text-align: center; padding: 4rem;">
            <h1>404</h1>
            <p>La page demandée n'existe pas.</p>
            <a href="/">Retour à l'accueil</a>
        </body>
        </html>
        HTML;
    }

    /**
     * Rendu page 403
     */
    protected function render403(ForbiddenException $e): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head><meta charset="UTF-8"><title>Accès refusé</title></head>
        <body style="font-family: sans-serif; text-align: center; padding: 4rem;">
            <h1>403</h1>
            <p>Vous n'avez pas les droits pour accéder à cette ressource.</p>
            <a href="/">Retour à l'accueil</a>
        </body>
        </html>
        HTML;
    }

    /**
     * Rendu page maintenance
     */
    protected function renderMaintenance(MaintenanceException $e): string
    {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head><meta charset="UTF-8"><title>Maintenance</title></head>
        <body style="font-family: sans-serif; text-align: center; padding: 4rem; background: #f0f0f0;">
            <h1>🔧 Maintenance en cours</h1>
            <p>{$message}</p>
            <p>Merci de réessayer ultérieurement.</p>
        </body>
        </html>
        HTML;
    }

    /**
     * Rendu erreur debug
     */
    protected function renderDebugError(\Throwable $e): string
    {
        $class = htmlspecialchars(get_class($e), ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
        $line = $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head><meta charset="UTF-8"><title>Erreur</title></head>
        <body style="font-family: monospace; padding: 2rem; background: #1a1a2e; color: #eee;">
            <h1 style="color: #e74c3c;">{$class}</h1>
            <h2>{$message}</h2>
            <p><strong>File:</strong> {$file}:{$line}</p>
            <h3>Stack Trace:</h3>
            <pre style="background: #16213e; padding: 1rem; overflow-x: auto;">{$trace}</pre>
        </body>
        </html>
        HTML;
    }

    /**
     * Rendu erreur production
     */
    protected function renderProductionError(int $statusCode, string $message): string
    {
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        return <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head><meta charset="UTF-8"><title>Erreur {$statusCode}</title></head>
        <body style="font-family: sans-serif; text-align: center; padding: 4rem;">
            <h1>Erreur {$statusCode}</h1>
            <p>{$message}</p>
            <a href="/">Retour à l'accueil</a>
        </body>
        </html>
        HTML;
    }

    /**
     * Termine la requête
     */
    public function terminate(Request $request, Response $response): void
    {
        // Appeler les callbacks de terminaison si nécessaire
        // Ex: fermer les connexions DB, écrire les logs, etc.
    }
}
