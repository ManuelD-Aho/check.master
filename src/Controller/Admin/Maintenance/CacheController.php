<?php
declare(strict_types=1);

namespace App\Controller\Admin\Maintenance;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\System\CacheService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CacheController extends AbstractController
{
    private CacheService $cacheService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        CacheService $cacheService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->cacheService = $cacheService;
    }

    public function index(Request $request): Response
    {
        return $this->render('admin/maintenance/cache', [
            'user' => $this->getUser(),
            'csrf_token' => $this->getCsrfToken(),
        ]);
    }

    public function clear(Request $request): Response
    {
        $body = (array) $request->getParsedBody();
        $token = (string) ($body['csrf_token'] ?? '');

        if (!$this->validateCsrf($token)) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/maintenance/cache');
        }

        $success = $this->cacheService->clear();

        if ($success) {
            $this->addFlash('success', 'Le cache a été vidé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors du vidage du cache.');
        }

        return $this->redirect('/admin/maintenance/cache');
    }
}
