<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Commission\AffectationEncadrantRepository;
use App\Repository\Report\RapportRepository;

class DashboardController extends AbstractController
{
    private AffectationEncadrantRepository $affectationRepository;
    private RapportRepository $rapportRepository;

    public function __construct(
        AffectationEncadrantRepository $affectationRepository,
        RapportRepository $rapportRepository
    ) {
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
