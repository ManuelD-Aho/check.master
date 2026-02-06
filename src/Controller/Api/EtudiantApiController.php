<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Student\EtudiantRepository;

class EtudiantApiController extends AbstractController
{
    private EtudiantRepository $etudiantRepository;

    public function __construct(EtudiantRepository $etudiantRepository)
    {
        $this->etudiantRepository = $etudiantRepository;
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
