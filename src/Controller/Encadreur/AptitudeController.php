<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Repository\Soutenance\AptitudeSoutenanceRepository;
use Doctrine\ORM\EntityManagerInterface;

class AptitudeController extends AbstractController
{
    private AptitudeSoutenanceRepository $aptitudeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        AptitudeSoutenanceRepository $aptitudeRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->aptitudeRepository = $aptitudeRepository;
        $this->entityManager = $entityManager;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $aptitudes = $this->aptitudeRepository->findAll();

        return $this->render('encadreur/aptitude/index', ['aptitudes' => $aptitudes]);
    }

    public function form(ServerRequestInterface $request): ResponseInterface
    {
        $matricule = $request->getAttribute('matricule');

        return $this->render('encadreur/aptitude/form', [
            'matricule' => $matricule,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function validate(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (is_array($data)) {
            $this->addFlash('success', 'Aptitude validee');
        }

        return $this->redirect('/encadreur/aptitude');
    }
}
