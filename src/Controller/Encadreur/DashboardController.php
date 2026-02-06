<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Commission\AffectationEncadrantRepository;
use App\Repository\Report\RapportRepository;

class DashboardController extends AbstractController
{
    private AffectationEncadrantRepository $affectationRepository;
    private RapportRepository $rapportRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        AffectationEncadrantRepository $affectationRepository,
        RapportRepository $rapportRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->affectationRepository = $affectationRepository;
        $this->rapportRepository = $rapportRepository;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->getUser();
        $matricule = $user ? $user->getMatriculeEnseignant() : null;
        $affectations = $matricule ? $this->affectationRepository->findByEnseignant($matricule) : [];

        return $this->render('encadreur/dashboard/index', [
            'affectations' => $affectations,
        ]);
    }
}
