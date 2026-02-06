<?php

declare(strict_types=1);

namespace App\Controller\Commission;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Commission\SessionCommissionRepository;
use App\Repository\Commission\CompteRenduCommissionRepository;
use App\Repository\Report\RapportRepository;

class SessionController extends AbstractController
{
    private SessionCommissionRepository $sessionRepository;
    private CompteRenduCommissionRepository $compteRenduRepository;
    private RapportRepository $rapportRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        SessionCommissionRepository $sessionRepository,
        CompteRenduCommissionRepository $compteRenduRepository,
        RapportRepository $rapportRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->sessionRepository = $sessionRepository;
        $this->compteRenduRepository = $compteRenduRepository;
        $this->rapportRepository = $rapportRepository;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $sessions = $this->sessionRepository->findAll();
        return $this->render('commission/sessions/index', ['sessions' => $sessions]);
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $session = $this->sessionRepository->find($id);

        return $this->render('commission/sessions/show', ['session' => $session]);
    }

    public function rapports(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $rapports = $this->rapportRepository->findByStatut('en_commission');

        return $this->render('commission/sessions/rapports', ['rapports' => $rapports]);
    }

    public function compteRendu(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $compteRendu = $this->compteRenduRepository->findBySession($id);

        return $this->render('commission/sessions/compte-rendu', ['compteRendu' => $compteRendu]);
    }
}
