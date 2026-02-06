<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Soutenance\JuryRepository;
use App\Repository\Soutenance\NoteSoutenanceRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SoutenanceController extends AbstractController
{
    private SoutenanceRepository $soutenanceRepository;
    private JuryRepository $juryRepository;
    private NoteSoutenanceRepository $noteRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        SoutenanceRepository $soutenanceRepository,
        JuryRepository $juryRepository,
        NoteSoutenanceRepository $noteRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->soutenanceRepository = $soutenanceRepository;
        $this->juryRepository = $juryRepository;
        $this->noteRepository = $noteRepository;
    }

    public function index(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        return $this->render('admin/soutenance/index', [
            'soutenances' => $soutenances,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function schedule(Request $request): Response
    {
        return $this->render('admin/soutenance/schedule', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function reschedule(Request $request): Response
    {
        $this->addFlash('success', 'Soutenance replanifiee');

        return $this->redirect('/admin/soutenances');
    }

    public function cancel(Request $request): Response
    {
        $this->addFlash('success', 'Soutenance annulee');

        return $this->redirect('/admin/soutenances');
    }

    public function jury(Request $request): Response
    {
        $jurys = $this->juryRepository->findAll();

        return $this->render('admin/soutenance/jury', [
            'jurys' => $jurys,
        ]);
    }

    public function saisirNotes(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'soutenanceId');
        $notes = $id !== null ? $this->noteRepository->findBySoutenance((int) $id) : [];

        return $this->render('admin/soutenance/notes', [
            'notes' => $notes,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function deliberation(Request $request): Response
    {
        return $this->render('admin/soutenance/deliberation', [
            'soutenances' => $this->soutenanceRepository->findAll(),
        ]);
    }

    private function getRouteParam(Request $request, string $key): ?string
    {
        $value = $request->getAttribute($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }
        if (is_int($value)) {
            return (string) $value;
        }
        $query = $request->getQueryParams();
        $value = $query[$key] ?? null;
        if (is_string($value) && $value !== '') {
            return $value;
        }
        if (is_int($value)) {
            return (string) $value;
        }
        return null;
    }
}
