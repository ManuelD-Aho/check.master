<?php
declare(strict_types=1);

namespace App\Controller\Admin\Maintenance;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\System\SettingsService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ModeController extends AbstractController
{
    private SettingsService $settingsService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        SettingsService $settingsService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->settingsService = $settingsService;
    }

    public function index(Request $request): Response
    {
        $maintenanceMode = $this->settingsService->isMaintenanceMode();

        return $this->render('admin/maintenance/mode', [
            'maintenance_mode' => $maintenanceMode,
            'user' => $this->getUser(),
            'csrf_token' => $this->getCsrfToken(),
        ]);
    }

    public function toggle(Request $request): Response
    {
        $body = (array) $request->getParsedBody();
        $token = (string) ($body['csrf_token'] ?? '');

        if (!$this->validateCsrf($token)) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/maintenance/mode');
        }

        $enabled = ($body['maintenance_mode'] ?? '0') === '1';
        $this->settingsService->setMaintenanceMode($enabled);

        if ($enabled) {
            $this->addFlash('warning', 'Le mode maintenance a été activé.');
        } else {
            $this->addFlash('success', 'Le mode maintenance a été désactivé.');
        }

        return $this->redirect('/admin/maintenance/mode');
    }
}
