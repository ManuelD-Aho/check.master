<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Academic\AnneeAcademique;
use App\Entity\Academic\Filiere;
use App\Entity\Academic\NiveauEtude;
use App\Entity\Soutenance\CritereEvaluation;
use App\Entity\Soutenance\RoleJury;
use App\Entity\Soutenance\Salle;
use App\Entity\Staff\Fonction;
use App\Entity\Staff\Grade;
use App\Entity\Staff\TypeFonction;
use App\Entity\Stage\Entreprise;
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
use DateTimeImmutable;
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
        $payload = (array) $request->getParsedBody();
        if (!$this->validateCsrf($payload['_csrf_token'] ?? '')) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/parametres/annees');
        }

        $now = new DateTimeImmutable();
        $annee = new AnneeAcademique();
        $annee->setLibelleAnnee($payload['libelle_annee'] ?? '')
            ->setDateDebut(new DateTimeImmutable($payload['date_debut'] ?? 'now'))
            ->setDateFin(new DateTimeImmutable($payload['date_fin'] ?? 'now'))
            ->setEstActive(false)
            ->setEstOuverteInscription(false)
            ->setDateCreation($now)
            ->setDateModification($now);

        $this->anneeRepository->save($annee);

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
        $id = $this->getRouteParam($request, 'id');
        $annee = $id !== null ? $this->anneeRepository->find((int) $id) : null;

        if ($annee === null) {
            $this->addFlash('error', 'Année académique introuvable.');
            return $this->redirect('/admin/parametres/annees');
        }

        $payload = (array) $request->getParsedBody();
        if (!$this->validateCsrf($payload['_csrf_token'] ?? '')) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/parametres/annees');
        }

        $annee->setLibelleAnnee($payload['libelle_annee'] ?? '')
            ->setDateDebut(new DateTimeImmutable($payload['date_debut'] ?? 'now'))
            ->setDateFin(new DateTimeImmutable($payload['date_fin'] ?? 'now'))
            ->setDateModification(new DateTimeImmutable());

        $this->anneeRepository->save($annee);

        $this->addFlash('success', 'Année académique mise à jour.');

        return $this->redirect('/admin/parametres/annees');
    }

    public function activateAnnee(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $annee = $id !== null ? $this->anneeRepository->find((int) $id) : null;

        if ($annee === null) {
            $this->addFlash('error', 'Année académique introuvable.');
            return $this->redirect('/admin/parametres/annees');
        }

        foreach ($this->anneeRepository->findAll() as $other) {
            $other->setEstActive(false);
            $this->anneeRepository->persist($other);
        }

        $annee->setEstActive(true);
        $this->anneeRepository->save($annee);

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
        $payload = (array) $request->getParsedBody();
        $items = $payload['filieres'] ?? [];

        foreach ($items as $data) {
            if (!empty($data['id'])) {
                $filiere = $this->filiereRepository->find((int) $data['id']);
                if ($filiere === null) {
                    continue;
                }
            } else {
                $filiere = new Filiere();
                $filiere->setDateCreation(new DateTimeImmutable());
            }

            $filiere->setCodeFiliere($data['code_filiere'] ?? '')
                ->setLibelleFiliere($data['libelle_filiere'] ?? '')
                ->setDescription($data['description'] ?? null)
                ->setActif(!empty($data['actif']));

            $this->filiereRepository->save($filiere);
        }

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
        $payload = (array) $request->getParsedBody();
        $items = $payload['niveaux'] ?? [];

        foreach ($items as $data) {
            if (!empty($data['id'])) {
                $niveau = $this->niveauRepository->find((int) $data['id']);
                if ($niveau === null) {
                    continue;
                }
            } else {
                $niveau = new NiveauEtude();
                $niveau->setDateCreation(new DateTimeImmutable());
            }

            $niveau->setCodeNiveau($data['code_niveau'] ?? '')
                ->setLibelleNiveau($data['libelle_niveau'] ?? '')
                ->setOrdreProgression((int) ($data['ordre'] ?? 0))
                ->setActif(!empty($data['actif']));

            $this->niveauRepository->save($niveau);
        }

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
        $payload = (array) $request->getParsedBody();
        $items = $payload['grades'] ?? [];

        foreach ($items as $data) {
            if (!empty($data['id'])) {
                $grade = $this->gradeRepository->find((int) $data['id']);
                if ($grade === null) {
                    continue;
                }
            } else {
                $grade = new Grade();
            }

            $grade->setCodeGrade($data['code_grade'] ?? '')
                ->setLibelleGrade($data['libelle_grade'] ?? '')
                ->setAbreviation($data['abreviation'] ?? '')
                ->setOrdreHierarchique((int) ($data['ordre_hierarchique'] ?? 0))
                ->setPeutPresiderJury(!empty($data['peut_presider_jury']))
                ->setActif(!empty($data['actif']));

            $this->gradeRepository->save($grade);
        }

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
        $payload = (array) $request->getParsedBody();
        $items = $payload['fonctions'] ?? [];

        foreach ($items as $data) {
            if (!empty($data['id'])) {
                $fonction = $this->fonctionRepository->find((int) $data['id']);
                if ($fonction === null) {
                    continue;
                }
            } else {
                $fonction = new Fonction();
            }

            $typeFonction = TypeFonction::tryFrom($data['type_fonction'] ?? '');
            if ($typeFonction === null) {
                continue;
            }

            $fonction->setCodeFonction($data['code_fonction'] ?? '')
                ->setLibelleFonction($data['libelle_fonction'] ?? '')
                ->setTypeFonction($typeFonction)
                ->setActif(!empty($data['actif']));

            $this->fonctionRepository->save($fonction);
        }

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
        $payload = (array) $request->getParsedBody();
        if (!$this->validateCsrf($payload['_csrf_token'] ?? '')) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/parametres/salles');
        }

        $salle = new Salle();
        $salle->setCodeSalle($payload['code_salle'] ?? '')
            ->setLibelleSalle($payload['libelle_salle'] ?? '')
            ->setCapacite(isset($payload['capacite']) ? (int) $payload['capacite'] : null)
            ->setEquipements($payload['equipements'] ?? null)
            ->setBatiment($payload['batiment'] ?? null)
            ->setEtage($payload['etage'] ?? null)
            ->setActif(true);

        $this->salleRepository->save($salle);

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
        $id = $this->getRouteParam($request, 'id');
        $salle = $id !== null ? $this->salleRepository->find((int) $id) : null;

        if ($salle === null) {
            $this->addFlash('error', 'Salle introuvable.');
            return $this->redirect('/admin/parametres/salles');
        }

        $payload = (array) $request->getParsedBody();
        if (!$this->validateCsrf($payload['_csrf_token'] ?? '')) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/parametres/salles');
        }

        $salle->setCodeSalle($payload['code_salle'] ?? '')
            ->setLibelleSalle($payload['libelle_salle'] ?? '')
            ->setCapacite(isset($payload['capacite']) ? (int) $payload['capacite'] : null)
            ->setEquipements($payload['equipements'] ?? null)
            ->setBatiment($payload['batiment'] ?? null)
            ->setEtage($payload['etage'] ?? null)
            ->setActif(!empty($payload['actif']));

        $this->salleRepository->save($salle);

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
        $payload = (array) $request->getParsedBody();
        $items = $payload['roles'] ?? [];

        foreach ($items as $data) {
            if (!empty($data['id'])) {
                $role = $this->roleJuryRepository->find((int) $data['id']);
                if ($role === null) {
                    continue;
                }
            } else {
                $role = new RoleJury();
            }

            $role->setCodeRole($data['code_role'] ?? '')
                ->setLibelleRole($data['libelle_role'] ?? '')
                ->setOrdreAffichage((int) ($data['ordre'] ?? 0))
                ->setActif(!empty($data['actif']));

            $this->roleJuryRepository->save($role);
        }

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
        $payload = (array) $request->getParsedBody();
        $items = $payload['criteres'] ?? [];

        foreach ($items as $data) {
            if (!empty($data['id'])) {
                $critere = $this->critereRepository->find((int) $data['id']);
                if ($critere === null) {
                    continue;
                }
            } else {
                $critere = new CritereEvaluation();
            }

            $critere->setCodeCritere($data['code_critere'] ?? '')
                ->setLibelleCritere($data['libelle_critere'] ?? '')
                ->setDescription($data['description'] ?? null)
                ->setOrdreAffichage((int) ($data['ordre_affichage'] ?? 0))
                ->setActif(!empty($data['actif']));

            $this->critereRepository->save($critere);
        }

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
        $payload = (array) $request->getParsedBody();
        if (!$this->validateCsrf($payload['_csrf_token'] ?? '')) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/parametres/entreprises');
        }

        $now = new DateTimeImmutable();
        $entreprise = new Entreprise();
        $entreprise->setRaisonSociale($payload['raison_sociale'] ?? '')
            ->setSigle($payload['sigle'] ?? null)
            ->setSecteurActivite($payload['secteur_activite'] ?? null)
            ->setAdresse($payload['adresse'] ?? null)
            ->setVille($payload['ville'] ?? null)
            ->setPays($payload['pays'] ?? 'Cameroun')
            ->setTelephone($payload['telephone'] ?? null)
            ->setEmail($payload['email'] ?? null)
            ->setSiteWeb($payload['site_web'] ?? null)
            ->setDescription($payload['description'] ?? null)
            ->setActif(true)
            ->setDateCreation($now)
            ->setDateModification($now);

        $this->entrepriseRepository->save($entreprise);

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
        $id = $this->getRouteParam($request, 'id');
        $entreprise = $id !== null ? $this->entrepriseRepository->find((int) $id) : null;

        if ($entreprise === null) {
            $this->addFlash('error', 'Entreprise introuvable.');
            return $this->redirect('/admin/parametres/entreprises');
        }

        $payload = (array) $request->getParsedBody();
        if (!$this->validateCsrf($payload['_csrf_token'] ?? '')) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/parametres/entreprises');
        }

        $entreprise->setRaisonSociale($payload['raison_sociale'] ?? '')
            ->setSigle($payload['sigle'] ?? null)
            ->setSecteurActivite($payload['secteur_activite'] ?? null)
            ->setAdresse($payload['adresse'] ?? null)
            ->setVille($payload['ville'] ?? null)
            ->setPays($payload['pays'] ?? 'Cameroun')
            ->setTelephone($payload['telephone'] ?? null)
            ->setEmail($payload['email'] ?? null)
            ->setSiteWeb($payload['site_web'] ?? null)
            ->setDescription($payload['description'] ?? null)
            ->setActif(!empty($payload['actif']))
            ->setDateModification(new DateTimeImmutable());

        $this->entrepriseRepository->save($entreprise);

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
