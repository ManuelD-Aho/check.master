<?php

declare(strict_types=1);

namespace App\Controller\Commission;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Commission\EvaluationRapportRepository;
use App\Repository\Report\RapportRepository;

class DashboardController extends AbstractController
{
    private EvaluationRapportRepository $evaluationRepository;
    private RapportRepository $rapportRepository;

    public function __construct(
        EvaluationRapportRepository $evaluationRepository,
        RapportRepository $rapportRepository
    ) {
        $this->evaluationRepository = $evaluationRepository;
        $this->rapportRepository = $rapportRepository;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->getUser();
        $pendingRapports = $this->rapportRepository->findByStatut('en_commission');
        $myEvaluations = $user ? $this->evaluationRepository->findByEvaluateur($user->getIdUtilisateur()) : [];

        return $this->render('commission/dashboard/index', [
            'pendingRapports' => $pendingRapports,
            'myEvaluations' => $myEvaluations,
        ]);
    }
}
