<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Stage\StatutCandidature;
use App\Repository\Stage\CandidatureRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use DateTimeImmutable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CandidatureController extends AbstractController
{
    private CandidatureRepository $candidatureRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        CandidatureRepository $candidatureRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->candidatureRepository = $candidatureRepository;
    }

    public function index(Request $request): Response
    {
        $candidatures = $this->candidatureRepository->findAll();

        return $this->render('admin/candidature/index', [
            'candidatures' => $candidatures,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $candidature = $id !== null ? $this->candidatureRepository->find((int) $id) : null;

        return $this->render('admin/candidature/show', [
            'candidature' => $candidature,
        ]);
    }

    public function validate(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $candidature = $id !== null ? $this->candidatureRepository->find((int) $id) : null;

        if ($candidature !== null) {
            $candidature->setStatutCandidature(StatutCandidature::Validee);
            $candidature->setDateTraitement(new DateTimeImmutable());
            $candidature->setValidateur($this->getUser());
            $this->candidatureRepository->save($candidature);
            $this->addFlash('success', 'Candidature validee');
        } else {
            $this->addFlash('error', 'Candidature introuvable');
        }

        return $this->redirect('/admin/candidatures');
    }

    public function reject(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $candidature = $id !== null ? $this->candidatureRepository->find((int) $id) : null;

        if ($candidature !== null) {
            $candidature->setStatutCandidature(StatutCandidature::Rejetee);
            $candidature->setDateTraitement(new DateTimeImmutable());
            $candidature->setValidateur($this->getUser());
            $this->candidatureRepository->save($candidature);
            $this->addFlash('success', 'Candidature rejetee');
        } else {
            $this->addFlash('error', 'Candidature introuvable');
        }

        return $this->redirect('/admin/candidatures');
    }

    public function pending(Request $request): Response
    {
        $candidatures = $this->candidatureRepository->findPendingValidation();

        return $this->render('admin/candidature/pending', [
            'candidatures' => $candidatures,
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
