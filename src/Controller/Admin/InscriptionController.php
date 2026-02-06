<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Student\InscriptionRepository;
use App\Repository\Student\VersementRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InscriptionController extends AbstractController
{
    private InscriptionRepository $inscriptionRepository;
    private VersementRepository $versementRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        InscriptionRepository $inscriptionRepository,
        VersementRepository $versementRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->inscriptionRepository = $inscriptionRepository;
        $this->versementRepository = $versementRepository;
    }

    public function index(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule');
        $inscriptions = $matricule !== null
            ? $this->inscriptionRepository->findByEtudiant($matricule)
            : $this->inscriptionRepository->findAll();

        return $this->render('admin/inscription/index', [
            'inscriptions' => $inscriptions,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'inscriptionId');
        $inscription = $id !== null ? $this->inscriptionRepository->find((int) $id) : null;
        $versements = $id !== null ? $this->versementRepository->findByInscription((int) $id) : [];

        return $this->render('admin/inscription/show', [
            'inscription' => $inscription,
            'versements' => $versements,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/inscription/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $this->addFlash('success', 'Inscription enregistree');

        return $this->redirect('/admin/inscriptions');
    }

    public function recordVersement(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'inscriptionId');
        $inscription = $id !== null ? $this->inscriptionRepository->find((int) $id) : null;
        $versements = $id !== null ? $this->versementRepository->findByInscription((int) $id) : [];

        return $this->render('admin/inscription/versement', [
            'inscription' => $inscription,
            'versements' => $versements,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function generateRecu(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'versementId');
        $versement = $id !== null ? $this->versementRepository->find((int) $id) : null;

        return $this->render('admin/inscription/recu', [
            'versement' => $versement,
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
