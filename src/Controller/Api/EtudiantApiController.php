<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Student\EtudiantRepository;

class EtudiantApiController extends AbstractController
{
    private EtudiantRepository $etudiantRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EtudiantRepository $etudiantRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->etudiantRepository = $etudiantRepository;
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $matricule = $request->getAttribute('matricule');
        $etudiant = $this->etudiantRepository->findByMatricule($matricule);

        if ($etudiant === null) {
            return $this->json(['error' => 'Etudiant non trouve'], 404);
        }

        return $this->json([
            'matricule' => $etudiant->getMatriculeEtudiant(),
            'nom' => $etudiant->getNomEtudiant(),
            'prenom' => $etudiant->getPrenomEtudiant(),
        ]);
    }

    public function search(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams()['q'] ?? '';
        $etudiants = [];

        if (strlen($query) >= 2) {
            $results = $this->etudiantRepository->findAll();
            foreach ($results as $e) {
                $nom = $e->getNomEtudiant() . ' ' . $e->getPrenomEtudiant();
                if (stripos($nom, $query) !== false || stripos($e->getMatriculeEtudiant(), $query) !== false) {
                    $etudiants[] = [
                        'matricule' => $e->getMatriculeEtudiant(),
                        'nom' => $e->getNomEtudiant(),
                        'prenom' => $e->getPrenomEtudiant(),
                    ];
                }
            }
        }

        return $this->json(['etudiants' => array_slice($etudiants, 0, 20)]);
    }
}
