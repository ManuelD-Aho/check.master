<?php
declare(strict_types=1);

namespace App\Controller\Admin\Maintenance;

use App\Controller\AbstractController;
use App\Repository\Student\EtudiantRepository;
use App\Repository\Stage\CandidatureRepository;
use App\Repository\Report\RapportRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use App\Repository\User\UtilisateurRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StatistiqueController extends AbstractController
{
    private UtilisateurRepository $utilisateurRepository;
    private EtudiantRepository $etudiantRepository;
    private CandidatureRepository $candidatureRepository;
    private RapportRepository $rapportRepository;
    private SoutenanceRepository $soutenanceRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        UtilisateurRepository $utilisateurRepository,
        EtudiantRepository $etudiantRepository,
        CandidatureRepository $candidatureRepository,
        RapportRepository $rapportRepository,
        SoutenanceRepository $soutenanceRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->utilisateurRepository = $utilisateurRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->candidatureRepository = $candidatureRepository;
        $this->rapportRepository = $rapportRepository;
        $this->soutenanceRepository = $soutenanceRepository;
    }

    public function index(Request $request): Response
    {
        $stats = [
            'utilisateurs' => $this->safeCount(fn () => $this->utilisateurRepository->findAll()),
            'etudiants' => $this->safeCount(fn () => $this->etudiantRepository->findAll()),
            'candidatures' => $this->safeCount(fn () => $this->candidatureRepository->findAll()),
            'rapports' => $this->safeCount(fn () => $this->rapportRepository->findAll()),
            'soutenances' => $this->safeCount(fn () => $this->soutenanceRepository->findAll()),
        ];

        return $this->render('admin/maintenance/statistiques', [
            'stats' => $stats,
            'user' => $this->getUser(),
        ]);
    }

    private function safeCount(callable $loader): int
    {
        try {
            $items = $loader();
        } catch (\Throwable) {
            return 0;
        }

        return is_array($items) ? count($items) : 0;
    }
}
