<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Staff\EnseignantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EnseignantController extends AbstractController
{
    private EnseignantRepository $enseignantRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EnseignantRepository $enseignantRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->enseignantRepository = $enseignantRepository;
    }

    public function index(Request $request): Response
    {
        $enseignants = $this->enseignantRepository->findAll();

        return $this->render('admin/enseignant/index', [
            'enseignants' => $enseignants,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $enseignant = $matricule !== null
            ? $this->enseignantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/enseignant/show', [
            'enseignant' => $enseignant,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/enseignant/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $this->addFlash('success', 'Enseignant enregistre');

        return $this->redirect('/admin/enseignants');
    }

    public function edit(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $enseignant = $matricule !== null
            ? $this->enseignantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/enseignant/edit', [
            'enseignant' => $enseignant,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function update(Request $request): Response
    {
        $this->addFlash('success', 'Enseignant mis a jour');

        return $this->redirect('/admin/enseignants');
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
