<?php
declare(strict_types=1);

namespace App\Controller\Etudiant;

use App\Controller\AbstractController;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController extends AbstractController
{
    public function index(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $statut = $this->getUser()?->getStatutUtilisateur();

        return $this->render('etudiant/dashboard/index', [
            'matricule' => $matricule,
            'statut' => $statut?->value,
        ]);
    }
}
