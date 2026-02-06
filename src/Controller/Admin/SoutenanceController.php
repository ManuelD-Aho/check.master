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

    public function create(Request $request): Response
    {
        return $this->render('admin/soutenance/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $this->addFlash('success', 'Soutenance programmée avec succès');

        return $this->redirect('/admin/soutenances');
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/show', [
            'soutenance' => $soutenance,
        ]);
    }

    public function edit(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/edit', [
            'soutenance' => $soutenance,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function update(Request $request): Response
    {
        $this->addFlash('success', 'Soutenance modifiée avec succès');

        return $this->redirect('/admin/soutenances');
    }

    public function jurys(Request $request): Response
    {
        $jurys = $this->juryRepository->findAll();

        return $this->render('admin/soutenance/jurys', [
            'jurys' => $jurys,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function composeJury(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule');

        return $this->render('admin/soutenance/compose-jury', [
            'matricule' => $matricule,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function saveJury(Request $request): Response
    {
        $this->addFlash('success', 'Jury enregistré avec succès');

        return $this->redirect('/admin/soutenances/jurys');
    }

    public function planning(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        return $this->render('admin/soutenance/planning', [
            'soutenances' => $soutenances,
        ]);
    }

    public function planningPdf(Request $request): Response
    {
        $this->addFlash('info', 'Génération du PDF en cours');

        return $this->redirect('/admin/soutenances/planning');
    }

    public function notationForm(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/notation', [
            'soutenance' => $soutenance,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function saveNotation(Request $request): Response
    {
        $this->addFlash('success', 'Notes enregistrées avec succès');

        return $this->redirect('/admin/soutenances');
    }

    public function deliberation(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        return $this->render('admin/soutenance/deliberation', [
            'soutenances' => $soutenances,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function delibererForm(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/deliberer', [
            'soutenance' => $soutenance,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function deliberer(Request $request): Response
    {
        $this->addFlash('success', 'Délibération enregistrée avec succès');

        return $this->redirect('/admin/soutenances/deliberation');
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
