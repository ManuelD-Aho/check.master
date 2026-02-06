<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Report\RapportRepository;
use App\Repository\Report\CommentaireRapportRepository;
use App\Service\Rapport\RapportService;

class RapportController extends AbstractController
{
    private RapportRepository $rapportRepository;
    private RapportService $rapportService;

    public function __construct(
        RapportRepository $rapportRepository,
        RapportService $rapportService
    ) {
        $this->rapportRepository = $rapportRepository;
        $this->rapportService = $rapportService;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->getUser();
        $rapports = $this->rapportRepository->findAll();

        return $this->render('encadreur/rapports/index', ['rapports' => $rapports]);
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $rapport = $this->rapportRepository->find($id);

        return $this->render('encadreur/rapports/show', ['rapport' => $rapport]);
    }

    public function comment(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $request->getParsedBody();
        $user = $this->getUser();

        if ($user && isset($data['contenu'])) {
            $this->rapportService->addComment(
                $id,
                $user->getIdUtilisateur(),
                $data['contenu'],
                'verification',
                true
            );
            $this->addFlash('success', 'Commentaire ajoute');
        }

        return $this->redirect('/encadreur/rapports/' . $id);
    }
}
