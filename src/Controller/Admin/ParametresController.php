<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Academic\FiliereRepository;
use App\Repository\Academic\NiveauEtudeRepository;
use App\Repository\Soutenance\CritereEvaluationRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\System\SettingsService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ParametresController extends AbstractController
{
    private SettingsService $settingsService;
    private AnneeAcademiqueRepository $anneeRepository;
    private NiveauEtudeRepository $niveauRepository;
    private FiliereRepository $filiereRepository;
    private CritereEvaluationRepository $critereRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        SettingsService $settingsService,
        AnneeAcademiqueRepository $anneeRepository,
        NiveauEtudeRepository $niveauRepository,
        FiliereRepository $filiereRepository,
        CritereEvaluationRepository $critereRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->settingsService = $settingsService;
        $this->anneeRepository = $anneeRepository;
        $this->niveauRepository = $niveauRepository;
        $this->filiereRepository = $filiereRepository;
        $this->critereRepository = $critereRepository;
    }

    public function index(Request $request): Response
    {
        $settings = $this->settingsService->getAll();

        return $this->render('admin/parametrage/index', [
            'settings' => $settings,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function update(Request $request): Response
    {
        $payload = $request->getParsedBody();
        if (is_array($payload)) {
            foreach ($payload as $key => $value) {
                if (is_string($key) && $key !== '') {
                    $this->settingsService->set($key, $value);
                }
            }
        }
        $this->addFlash('success', 'Parametres mis a jour');

        return $this->redirect('/admin/parametrage');
    }

    public function anneeAcademique(Request $request): Response
    {
        return $this->render('admin/parametrage/annee-academique', [
            'annees' => $this->anneeRepository->findAll(),
        ]);
    }

    public function niveaux(Request $request): Response
    {
        return $this->render('admin/parametrage/niveaux', [
            'niveaux' => $this->niveauRepository->findAll(),
        ]);
    }

    public function filieres(Request $request): Response
    {
        return $this->render('admin/parametrage/filieres', [
            'filieres' => $this->filiereRepository->findAll(),
        ]);
    }

    public function criteres(Request $request): Response
    {
        return $this->render('admin/parametrage/criteres', [
            'criteres' => $this->critereRepository->findAll(),
        ]);
    }
}
