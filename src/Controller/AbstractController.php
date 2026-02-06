<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User\Utilisateur;
use App\Middleware\CsrfMiddleware;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractController
{
    protected ContainerInterface $container;
    protected ?Utilisateur $currentUser;
    protected AuthenticationService $authenticationService;
    protected AuthorizationService $authorizationService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService
    ) {
        $this->container = $container;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
        $this->currentUser = $this->resolveCurrentUser();
    }

    protected function render(string $template, array $data = []): ResponseInterface
    {
        $settings = $this->container->get('settings');
        $templatesPath = $settings['paths']['templates'] ?? __DIR__ . '/../../templates';
        $normalized = ltrim($template, '/\\');

        if (!str_ends_with($normalized, '.php')) {
            $normalized .= '.php';
        }

        $path = rtrim($templatesPath, '/\\') . DIRECTORY_SEPARATOR . $normalized;

        if (!is_file($path)) {
            return new Response(500, ['Content-Type' => 'text/plain'], 'Template not found');
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $path;
        $content = ob_get_clean();

        return new Response(200, ['Content-Type' => 'text/html'], $content === false ? '' : $content);
    }

    protected function json(array $data, int $status = 200): ResponseInterface
    {
        $payload = json_encode($data);

        if ($payload === false) {
            $payload = '{}';
            $status = 500;
        }

        return new Response($status, ['Content-Type' => 'application/json'], $payload);
    }

    protected function redirect(string $url, int $status = 302): ResponseInterface
    {
        return new Response($status, ['Location' => $url]);
    }

    protected function getUser(): ?Utilisateur
    {
        return $this->currentUser;
    }

    protected function isGranted(string $permission): bool
    {
        if ($this->currentUser === null) {
            return false;
        }

        $userId = $this->currentUser->getIdUtilisateur();
        if ($userId === null) {
            return false;
        }

        $parts = preg_split('/[.:]/', $permission, 2);
        if (!is_array($parts) || count($parts) !== 2) {
            return false;
        }

        [$fonctionnalite, $action] = $parts;

        if ($fonctionnalite === '' || $action === '') {
            return false;
        }

        return $this->authorizationService->can($userId, $fonctionnalite, $action);
    }

    protected function addFlash(string $type, string $message): void
    {
        if (!isset($_SESSION['flashes']) || !is_array($_SESSION['flashes'])) {
            $_SESSION['flashes'] = [];
        }

        if (!isset($_SESSION['flashes'][$type]) || !is_array($_SESSION['flashes'][$type])) {
            $_SESSION['flashes'][$type] = [];
        }

        $_SESSION['flashes'][$type][] = $message;
    }

    protected function getFlashes(): array
    {
        if (!isset($_SESSION['flashes']) || !is_array($_SESSION['flashes'])) {
            return [];
        }

        $flashes = $_SESSION['flashes'];
        unset($_SESSION['flashes']);

        return $flashes;
    }

    protected function getCsrfToken(): string
    {
        return CsrfMiddleware::generateToken();
    }

    protected function validateCsrf(string $token): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? null;

        if (!is_string($sessionToken) || $sessionToken === '' || $token === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    private function resolveCurrentUser(): ?Utilisateur
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $userId = (int)$_SESSION['user_id'];

        if ($userId <= 0) {
            return null;
        }

        return $this->authenticationService->getUserById($userId);
    }
}
