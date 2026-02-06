<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Student\EtudiantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EtudiantController extends AbstractController
{
    private EtudiantRepository $etudiantRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EtudiantRepository $etudiantRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->etudiantRepository = $etudiantRepository;
    }

    public function index(Request $request): Response
    {
        $etudiants = $this->etudiantRepository->findAll();

        return $this->render('admin/etudiant/index', [
            'etudiants' => $etudiants,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $etudiant = $matricule !== null
            ? $this->etudiantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/etudiant/show', [
            'etudiant' => $etudiant,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/etudiant/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $this->addFlash('success', 'Etudiant enregistre');

        return $this->redirect('/admin/etudiants');
    }

    public function edit(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $etudiant = $matricule !== null
            ? $this->etudiantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/etudiant/edit', [
            'etudiant' => $etudiant,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function update(Request $request): Response
    {
        $this->addFlash('success', 'Etudiant mis a jour');

        return $this->redirect('/admin/etudiants');
    }

    public function import(Request $request): Response
    {
        $uploads = $request->getUploadedFiles();

        return $this->render('admin/etudiant/import', [
            'uploads' => $uploads,
            'csrf' => $this->getCsrfToken(),
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
