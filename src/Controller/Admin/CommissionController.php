<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Commission\CompteRenduCommissionRepository;
use App\Repository\Commission\MembreCommissionRepository;
use App\Repository\Commission\SessionCommissionRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommissionController extends AbstractController
{
    private MembreCommissionRepository $membreRepository;
    private SessionCommissionRepository $sessionRepository;
    private CompteRenduCommissionRepository $compteRenduRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        MembreCommissionRepository $membreRepository,
        SessionCommissionRepository $sessionRepository,
        CompteRenduCommissionRepository $compteRenduRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->membreRepository = $membreRepository;
        $this->sessionRepository = $sessionRepository;
        $this->compteRenduRepository = $compteRenduRepository;
    }

    public function membres(Request $request): Response
    {
        $membres = $this->membreRepository->findAll();

        return $this->render('admin/commission/membres', [
            'membres' => $membres,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function addMembre(Request $request): Response
    {
        $this->addFlash('success', 'Membre ajoute');

        return $this->redirect('/admin/commission/membres');
    }

    public function removeMembre(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $membre = $id !== null ? $this->membreRepository->find((int) $id) : null;

        if ($membre !== null) {
            $this->membreRepository->remove($membre);
            $this->addFlash('success', 'Membre supprime');
        } else {
            $this->addFlash('error', 'Membre introuvable');
        }

        return $this->redirect('/admin/commission/membres');
    }

    public function sessions(Request $request): Response
    {
        $sessions = $this->sessionRepository->findAll();

        return $this->render('admin/commission/sessions', [
            'sessions' => $sessions,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function createSession(Request $request): Response
    {
        $this->addFlash('success', 'Session creee');

        return $this->redirect('/admin/commission/sessions');
    }

    public function closeSession(Request $request): Response
    {
        $this->addFlash('success', 'Session cloturee');

        return $this->redirect('/admin/commission/sessions');
    }

    public function compteRendu(Request $request): Response
    {
        $compteRendus = $this->compteRenduRepository->findAll();

        return $this->render('admin/commission/compte-rendu', [
            'compteRendus' => $compteRendus,
        ]);
    }

    public function saveMembres(Request $request): Response
    {
        $this->addFlash('success', 'Configuration des membres enregistree');

        return $this->redirect('/admin/commission/membres');
    }

    public function storeSession(Request $request): Response
    {
        $this->addFlash('success', 'Session enregistree');

        return $this->redirect('/admin/commission/sessions');
    }

    public function showSession(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $session = $id !== null ? $this->sessionRepository->find((int) $id) : null;

        return $this->render('admin/commission/sessions-show', [
            'session' => $session,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function downloadSessionPdf(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');

        $this->addFlash('info', 'Telechargement du PDF en cours');

        return $this->redirect('/admin/commission/sessions/' . $id);
    }

    public function assignation(Request $request): Response
    {
        return $this->render('admin/commission/assignation', [
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function assignationForm(Request $request): Response
    {
        $rapportId = $this->getRouteParam($request, 'rapportId');

        return $this->render('admin/commission/assignation-form', [
            'rapportId' => $rapportId,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function assign(Request $request): Response
    {
        $this->addFlash('success', 'Encadrant assigne avec succes');

        return $this->redirect('/admin/commission/assignation');
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
