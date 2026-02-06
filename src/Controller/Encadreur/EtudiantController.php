<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Student\EtudiantRepository;
use App\Repository\Commission\AffectationEncadrantRepository;

class EtudiantController extends AbstractController
{
    private EtudiantRepository $etudiantRepository;
    private AffectationEncadrantRepository $affectationRepository;

    public function __construct(
        EtudiantRepository $etudiantRepository,
        AffectationEncadrantRepository $affectationRepository
    ) {
        $this->etudiantRepository = $etudiantRepository;
        $this->affectationRepository = $affectationRepository;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->getUser();
        $matricule = $user ? $user->getMatriculeEnseignant() : null;
        $affectations = $matricule ? $this->affectationRepository->findByEnseignant($matricule) : [];

        return $this->render('encadreur/etudiants/index', ['affectations' => $affectations]);
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $matricule = $request->getAttribute('matricule');
        $etudiant = $this->etudiantRepository->findByMatricule($matricule);

        return $this->render('encadreur/etudiants/show', ['etudiant' => $etudiant]);
    }
}
