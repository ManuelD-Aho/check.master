<?php
declare(strict_types=1);

namespace App\Controller\Etudiant;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Etudiant\EtudiantService;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProfilController extends AbstractController
{
    private EtudiantService $etudiantService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EtudiantService $etudiantService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->etudiantService = $etudiantService;
    }

    public function show(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/profil/show', [
            'matricule' => $matricule,
        ]);
    }

    public function edit(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/profil/edit', [
            'matricule' => $matricule,
        ]);
    }

    public function update(Request $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $csrfToken = (string) ($body['_csrf_token'] ?? '');

        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Jeton CSRF invalide');
            return $this->redirect('/etudiant/profil/modifier');
        }

        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $this->etudiantService->updateProfile($matricule, $body);

        $this->addFlash('success', 'Profil mis a jour');
        return $this->redirect('/etudiant/profil');
    }
}
