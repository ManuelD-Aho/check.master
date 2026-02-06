<?php

declare(strict_types=1);

namespace App\Controller\Commission;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Report\RapportRepository;
use App\Repository\Commission\EvaluationRapportRepository;
use App\Service\Commission\VoteService;

class RapportController extends AbstractController
{
    private RapportRepository $rapportRepository;
    private EvaluationRapportRepository $evaluationRepository;
    private VoteService $voteService;

    public function __construct(
        RapportRepository $rapportRepository,
        EvaluationRapportRepository $evaluationRepository,
        VoteService $voteService
    ) {
        $this->rapportRepository = $rapportRepository;
        $this->evaluationRepository = $evaluationRepository;
        $this->voteService = $voteService;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $rapports = $this->rapportRepository->findByStatut('en_commission');
        return $this->render('commission/rapports/index', ['rapports' => $rapports]);
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $rapport = $this->rapportRepository->find($id);
        $evaluations = $this->evaluationRepository->findByRapport($id);

        return $this->render('commission/rapports/show', [
            'rapport' => $rapport,
            'evaluations' => $evaluations,
        ]);
    }

    public function evaluate(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $rapport = $this->rapportRepository->find($id);

        return $this->render('commission/rapports/evaluate', ['rapport' => $rapport]);
    }

    public function vote(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $request->getParsedBody();
        $user = $this->getUser();

        if ($user && isset($data['decision'])) {
            $this->voteService->castVote(
                $id,
                $user->getIdUtilisateur(),
                $data['decision'],
                isset($data['note_qualite']) ? (int) $data['note_qualite'] : null,
                $data['points_forts'] ?? null,
                $data['points_ameliorer'] ?? null,
                $data['commentaire'] ?? null
            );
            $this->addFlash('success', 'Vote enregistre');
        }

        return $this->redirect('/commission/rapports/' . $id);
    }
}
