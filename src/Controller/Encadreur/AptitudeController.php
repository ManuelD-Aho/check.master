<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Soutenance\AptitudeSoutenanceRepository;
use Doctrine\ORM\EntityManagerInterface;

class AptitudeController extends AbstractController
{
    private AptitudeSoutenanceRepository $aptitudeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AptitudeSoutenanceRepository $aptitudeRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->aptitudeRepository = $aptitudeRepository;
        $this->entityManager = $entityManager;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->getUser();
        $matricule = $user ? $user->getMatriculeEnseignant() : null;
        $aptitudes = $matricule ? $this->aptitudeRepository->findByEncadreur($matricule) : [];

        return $this->render('encadreur/aptitude/index', ['aptitudes' => $aptitudes]);
    }

    public function validate(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $request->getParsedBody();

        $aptitude = $this->aptitudeRepository->find($id);
        if ($aptitude && isset($data['est_apte'])) {
            $aptitude->setEstApte($data['est_apte'] === '1');
            $aptitude->setCommentaire($data['commentaire'] ?? null);
            $aptitude->setDateValidation(new \DateTimeImmutable());
            $this->entityManager->flush();
            $this->addFlash('success', 'Aptitude validee');
        }

        return $this->redirect('/encadreur/aptitude');
    }
}
