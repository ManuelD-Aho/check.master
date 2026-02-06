<?php
declare(strict_types=1);

namespace App\Controller\Admin\Maintenance;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\System\AuditService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuditController extends AbstractController
{
    private AuditService $auditService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        AuditService $auditService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->auditService = $auditService;
    }

    public function index(Request $request): Response
    {
        $logs = $this->auditService->getRecentLogs(100);

        return $this->render('admin/maintenance/audit', [
            'logs' => $logs,
            'user' => $this->getUser(),
        ]);
    }
}
