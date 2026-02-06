<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Staff\EnseignantRepository;

class EnseignantApiController extends AbstractController
{
    private EnseignantRepository $enseignantRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EnseignantRepository $enseignantRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->enseignantRepository = $enseignantRepository;
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $matricule = $request->getAttribute('matricule');
        $enseignant = $this->enseignantRepository->findByMatricule($matricule);

        if ($enseignant === null) {
            return $this->json(['error' => 'Enseignant non trouve'], 404);
        }

        return $this->json([
            'matricule' => $enseignant->getMatriculeEnseignant(),
            'nom' => $enseignant->getNomEnseignant(),
            'prenom' => $enseignant->getPrenomEnseignant(),
        ]);
    }

    public function search(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams()['q'] ?? '';
        $enseignants = [];

        if (strlen($query) >= 2) {
            $results = $this->enseignantRepository->findActive();
            foreach ($results as $e) {
                $nom = $e->getNomEnseignant() . ' ' . $e->getPrenomEnseignant();
                if (stripos($nom, $query) !== false || stripos($e->getMatriculeEnseignant(), $query) !== false) {
                    $enseignants[] = [
                        'matricule' => $e->getMatriculeEnseignant(),
                        'nom' => $e->getNomEnseignant(),
                        'prenom' => $e->getPrenomEnseignant(),
                    ];
                }
            }
        }

        return $this->json(['enseignants' => array_slice($enseignants, 0, 20)]);
    }
}
