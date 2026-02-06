<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Stage\EntrepriseRepository;

class EntrepriseApiController extends AbstractController
{
    private EntrepriseRepository $entrepriseRepository;

    public function __construct(EntrepriseRepository $entrepriseRepository)
    {
        $this->entrepriseRepository = $entrepriseRepository;
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
