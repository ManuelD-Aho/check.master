<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Stage\EntrepriseRepository;

class EntrepriseApiController extends AbstractController
{
    private EntrepriseRepository $entrepriseRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EntrepriseRepository $entrepriseRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->entrepriseRepository = $entrepriseRepository;
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $entreprise = $this->entrepriseRepository->find($id);

        if ($entreprise === null) {
            return $this->json(['error' => 'Entreprise non trouvee'], 404);
        }

        return $this->json([
            'id' => $entreprise->getIdEntreprise(),
            'raison_sociale' => $entreprise->getRaisonSociale(),
            'sigle' => $entreprise->getSigle(),
            'secteur' => $entreprise->getSecteurActivite(),
        ]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->json(['success' => true, 'message' => 'Entreprise creee']);
    }

    public function search(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams()['q'] ?? '';
        $entreprises = [];

        if (strlen($query) >= 2) {
            $results = $this->entrepriseRepository->findByRaisonSociale($query);
            foreach ($results as $e) {
                $entreprises[] = [
                    'id' => $e->getIdEntreprise(),
                    'raison_sociale' => $e->getRaisonSociale(),
                    'sigle' => $e->getSigle(),
                    'secteur' => $e->getSecteurActivite(),
                ];
            }
        }

        return $this->json(['entreprises' => $entreprises]);
    }
}
