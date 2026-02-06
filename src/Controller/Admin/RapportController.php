<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Report\StatutRapport;
use App\Repository\Report\RapportRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use DateTimeImmutable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RapportController extends AbstractController
{
    private RapportRepository $rapportRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        RapportRepository $rapportRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->rapportRepository = $rapportRepository;
    }

    public function index(Request $request): Response
    {
        $rapports = $this->rapportRepository->findAll();

        return $this->render('admin/rapport/index', [
            'rapports' => $rapports,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $rapport = $id !== null ? $this->rapportRepository->find((int) $id) : null;

        return $this->render('admin/rapport/show', [
            'rapport' => $rapport,
        ]);
    }

    public function approve(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $rapport = $id !== null ? $this->rapportRepository->find((int) $id) : null;

        if ($rapport !== null) {
            $rapport->setStatutRapport(StatutRapport::APPROUVE);
            $rapport->setDateValidation(new DateTimeImmutable());
            $rapport->setValidePar($this->getUser());
            $this->rapportRepository->save($rapport);
            $this->addFlash('success', 'Rapport approuve');
        } else {
            $this->addFlash('error', 'Rapport introuvable');
        }

        return $this->redirect('/admin/rapports');
    }

    public function returnReport(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $rapport = $id !== null ? $this->rapportRepository->find((int) $id) : null;

        if ($rapport !== null) {
            $rapport->setStatutRapport(StatutRapport::RETOURNE);
            $rapport->setDateValidation(new DateTimeImmutable());
            $rapport->setValidePar($this->getUser());
            $this->rapportRepository->save($rapport);
            $this->addFlash('success', 'Rapport retourne');
        } else {
            $this->addFlash('error', 'Rapport introuvable');
        }

        return $this->redirect('/admin/rapports');
    }

    public function sendToCommission(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $rapport = $id !== null ? $this->rapportRepository->find((int) $id) : null;

        if ($rapport !== null) {
            $rapport->setStatutRapport(StatutRapport::EN_COMMISSION);
            $rapport->setDateValidation(new DateTimeImmutable());
            $rapport->setValidePar($this->getUser());
            $this->rapportRepository->save($rapport);
            $this->addFlash('success', 'Rapport envoye en commission');
        } else {
            $this->addFlash('error', 'Rapport introuvable');
        }

        return $this->redirect('/admin/rapports');
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
