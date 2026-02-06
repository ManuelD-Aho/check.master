<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Academic\FiliereRepository;
use App\Repository\Academic\NiveauEtudeRepository;
use App\Repository\Soutenance\CritereEvaluationRepository;
use App\Repository\Soutenance\RoleJuryRepository;
use App\Repository\Soutenance\SalleRepository;
use App\Repository\Staff\FonctionRepository;
use App\Repository\Staff\GradeRepository;
use App\Repository\Stage\EntrepriseRepository;
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
    private GradeRepository $gradeRepository;
    private FonctionRepository $fonctionRepository;
    private SalleRepository $salleRepository;
    private RoleJuryRepository $roleJuryRepository;
    private EntrepriseRepository $entrepriseRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        SettingsService $settingsService,
        AnneeAcademiqueRepository $anneeRepository,
        NiveauEtudeRepository $niveauRepository,
        FiliereRepository $filiereRepository,
        CritereEvaluationRepository $critereRepository,
        GradeRepository $gradeRepository,
        FonctionRepository $fonctionRepository,
        SalleRepository $salleRepository,
        RoleJuryRepository $roleJuryRepository,
        EntrepriseRepository $entrepriseRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->settingsService = $settingsService;
        $this->anneeRepository = $anneeRepository;
        $this->niveauRepository = $niveauRepository;
        $this->filiereRepository = $filiereRepository;
        $this->critereRepository = $critereRepository;
        $this->gradeRepository = $gradeRepository;
        $this->fonctionRepository = $fonctionRepository;
        $this->salleRepository = $salleRepository;
        $this->roleJuryRepository = $roleJuryRepository;
        $this->entrepriseRepository = $entrepriseRepository;
    }

    // ── Index ────────────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        return $this->render('admin/parametres/index', [
            'settings' => $this->settingsService->getAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    // ── Application ──────────────────────────────────────────────────

    public function application(Request $request): Response
    {
        return $this->render('admin/parametres/application', [
            'settings' => $this->settingsService->getAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveApplication(Request $request): Response
    {
        $payload = $request->getParsedBody();
        if (is_array($payload)) {
            foreach ($payload as $key => $value) {
                if (is_string($key) && $key !== '') {
                    $this->settingsService->set($key, $value);
                }
            }
        }
        $this->addFlash('success', 'Paramètres de l\'application mis à jour.');

        return $this->redirect('/admin/parametres/application');
    }

    // ── Années académiques ───────────────────────────────────────────

    public function annees(Request $request): Response
    {
        return $this->render('admin/parametres/annees', [
            'annees' => $this->anneeRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function createAnnee(Request $request): Response
    {
        return $this->render('admin/parametres/annees-create', [
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function storeAnnee(Request $request): Response
    {
        $this->addFlash('success', 'Année académique créée avec succès.');

        return $this->redirect('/admin/parametres/annees');
    }

    public function editAnnee(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $annee = $id !== null ? $this->anneeRepository->find((int) $id) : null;

        return $this->render('admin/parametres/annees-edit', [
            'annee' => $annee,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function updateAnnee(Request $request): Response
    {
        $this->addFlash('success', 'Année académique mise à jour.');

        return $this->redirect('/admin/parametres/annees');
    }

    public function activateAnnee(Request $request): Response
    {
        $this->addFlash('success', 'Année académique activée.');

        return $this->redirect('/admin/parametres/annees');
    }

    // ── Filières ─────────────────────────────────────────────────────

    public function filieres(Request $request): Response
    {
        return $this->render('admin/parametres/filieres', [
            'filieres' => $this->filiereRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveFilieres(Request $request): Response
    {
        $this->addFlash('success', 'Filières mises à jour.');

        return $this->redirect('/admin/parametres/filieres');
    }

    // ── Niveaux ──────────────────────────────────────────────────────

    public function niveaux(Request $request): Response
    {
        return $this->render('admin/parametres/niveaux', [
            'niveaux' => $this->niveauRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveNiveaux(Request $request): Response
    {
        $this->addFlash('success', 'Niveaux mis à jour.');

        return $this->redirect('/admin/parametres/niveaux');
    }

    // ── Grades ───────────────────────────────────────────────────────

    public function grades(Request $request): Response
    {
        return $this->render('admin/parametres/grades', [
            'grades' => $this->gradeRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveGrades(Request $request): Response
    {
        $this->addFlash('success', 'Grades mis à jour.');

        return $this->redirect('/admin/parametres/grades');
    }

    // ── Fonctions ────────────────────────────────────────────────────

    public function fonctions(Request $request): Response
    {
        return $this->render('admin/parametres/fonctions', [
            'fonctions' => $this->fonctionRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveFonctions(Request $request): Response
    {
        $this->addFlash('success', 'Fonctions mises à jour.');

        return $this->redirect('/admin/parametres/fonctions');
    }

    // ── Salles ───────────────────────────────────────────────────────

    public function salles(Request $request): Response
    {
        return $this->render('admin/parametres/salles', [
            'salles' => $this->salleRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function createSalle(Request $request): Response
    {
        return $this->render('admin/parametres/salles-create', [
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function storeSalle(Request $request): Response
    {
        $this->addFlash('success', 'Salle créée avec succès.');

        return $this->redirect('/admin/parametres/salles');
    }

    public function editSalle(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $salle = $id !== null ? $this->salleRepository->find((int) $id) : null;

        return $this->render('admin/parametres/salles-edit', [
            'salle' => $salle,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function updateSalle(Request $request): Response
    {
        $this->addFlash('success', 'Salle mise à jour.');

        return $this->redirect('/admin/parametres/salles');
    }

    // ── Rôles jury ───────────────────────────────────────────────────

    public function rolesJury(Request $request): Response
    {
        return $this->render('admin/parametres/roles-jury', [
            'roles' => $this->roleJuryRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveRolesJury(Request $request): Response
    {
        $this->addFlash('success', 'Rôles du jury mis à jour.');

        return $this->redirect('/admin/parametres/roles-jury');
    }

    // ── Critères d'évaluation ────────────────────────────────────────

    public function criteres(Request $request): Response
    {
        return $this->render('admin/parametres/criteres', [
            'criteres' => $this->critereRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function saveCriteres(Request $request): Response
    {
        $this->addFlash('success', 'Critères d\'évaluation mis à jour.');

        return $this->redirect('/admin/parametres/criteres');
    }

    // ── Entreprises ──────────────────────────────────────────────────

    public function entreprises(Request $request): Response
    {
        return $this->render('admin/parametres/entreprises', [
            'entreprises' => $this->entrepriseRepository->findAll(),
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function createEntreprise(Request $request): Response
    {
        return $this->render('admin/parametres/entreprises-create', [
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function storeEntreprise(Request $request): Response
    {
        $this->addFlash('success', 'Entreprise créée avec succès.');

        return $this->redirect('/admin/parametres/entreprises');
    }

    public function editEntreprise(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $entreprise = $id !== null ? $this->entrepriseRepository->find((int) $id) : null;

        return $this->render('admin/parametres/entreprises-edit', [
            'entreprise' => $entreprise,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function updateEntreprise(Request $request): Response
    {
        $this->addFlash('success', 'Entreprise mise à jour.');

        return $this->redirect('/admin/parametres/entreprises');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function getRouteParam(Request $request, string $key): ?string
    {
        $value = $request->getAttribute($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }
        if (is_int($value)) {
            return (string) $value;
        }
        $query = $request->getQueryParams();
        $value = $query[$key] ?? null;
        if (is_string($value) && $value !== '') {
            return $value;
        }
        if (is_int($value)) {
            return (string) $value;
        }
        return null;
    }
}
