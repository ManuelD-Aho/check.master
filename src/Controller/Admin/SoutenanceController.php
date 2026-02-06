<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Soutenance\CritereEvaluationRepository;
use App\Repository\Soutenance\JuryRepository;
use App\Repository\Soutenance\NoteSoutenanceRepository;
use App\Repository\Soutenance\RoleJuryRepository;
use App\Repository\Soutenance\SalleRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use App\Repository\Staff\EnseignantRepository;
use App\Repository\Student\EtudiantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Soutenance\JuryService;
use App\Service\Soutenance\SoutenanceService;
use DateTimeImmutable;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SoutenanceController extends AbstractController
{
    private SoutenanceRepository $soutenanceRepository;
    private JuryRepository $juryRepository;
    private NoteSoutenanceRepository $noteRepository;
    private SoutenanceService $soutenanceService;
    private JuryService $juryService;
    private EtudiantRepository $etudiantRepository;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;
    private SalleRepository $salleRepository;
    private CritereEvaluationRepository $critereRepository;
    private EnseignantRepository $enseignantRepository;
    private RoleJuryRepository $roleJuryRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        SoutenanceRepository $soutenanceRepository,
        JuryRepository $juryRepository,
        NoteSoutenanceRepository $noteRepository,
        SoutenanceService $soutenanceService,
        JuryService $juryService,
        EtudiantRepository $etudiantRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
        SalleRepository $salleRepository,
        CritereEvaluationRepository $critereRepository,
        EnseignantRepository $enseignantRepository,
        RoleJuryRepository $roleJuryRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->soutenanceRepository = $soutenanceRepository;
        $this->juryRepository = $juryRepository;
        $this->noteRepository = $noteRepository;
        $this->soutenanceService = $soutenanceService;
        $this->juryService = $juryService;
        $this->etudiantRepository = $etudiantRepository;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
        $this->salleRepository = $salleRepository;
        $this->critereRepository = $critereRepository;
        $this->enseignantRepository = $enseignantRepository;
        $this->roleJuryRepository = $roleJuryRepository;
    }

    public function index(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        return $this->render('admin/soutenance/index', [
            'soutenances' => $soutenances,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/soutenance/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/soutenances');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/soutenances/programmer');
        }

        try {
            $jury = $this->juryRepository->find((int)($data['jury_id'] ?? 0));
            $etudiant = $this->etudiantRepository->findByMatricule(trim((string)($data['matricule'] ?? '')));
            $salle = $this->salleRepository->find((int)($data['salle_id'] ?? 0));

            if ($jury === null || $etudiant === null || $salle === null) {
                $this->addFlash('error', 'Jury, étudiant ou salle introuvable');
                return $this->redirect('/admin/soutenances/programmer');
            }

            $dateSoutenance = new DateTimeImmutable((string)($data['date_soutenance'] ?? 'now'));
            $heureDebut = new DateTimeImmutable((string)($data['heure_debut'] ?? 'now'));
            $dureeMinutes = (int)($data['duree_minutes'] ?? 60);
            $theme = trim((string)($data['theme'] ?? ''));
            $observations = !empty($data['observations']) ? trim((string)$data['observations']) : null;

            $programmeur = $this->getUser();
            if ($programmeur === null) {
                $this->addFlash('error', 'Utilisateur non authentifié');
                return $this->redirect('/admin/soutenances/programmer');
            }

            $this->soutenanceService->schedule(
                $jury,
                $etudiant,
                $salle,
                $dateSoutenance,
                $heureDebut,
                $dureeMinutes,
                $theme,
                $programmeur,
                $observations
            );

            $this->addFlash('success', 'Soutenance programmée avec succès');
            return $this->redirect('/admin/soutenances');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la programmation : ' . $e->getMessage());
            return $this->redirect('/admin/soutenances/programmer');
        }
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/show', [
            'soutenance' => $soutenance,
        ]);
    }

    public function edit(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/edit', [
            'soutenance' => $soutenance,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function update(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');

        if ($id === null) {
            $this->addFlash('error', 'Identifiant invalide');
            return $this->redirect('/admin/soutenances');
        }

        $soutenance = $this->soutenanceRepository->find((int) $id);

        if ($soutenance === null) {
            $this->addFlash('error', 'Soutenance non trouvée');
            return $this->redirect('/admin/soutenances');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect("/admin/soutenances/{$id}/modifier");
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect("/admin/soutenances/{$id}/modifier");
        }

        try {
            $dateSoutenance = new DateTimeImmutable((string)($data['date_soutenance'] ?? 'now'));
            $heureDebut = new DateTimeImmutable((string)($data['heure_debut'] ?? 'now'));
            $salle = !empty($data['salle_id']) ? $this->salleRepository->find((int)$data['salle_id']) : null;
            $dureeMinutes = !empty($data['duree_minutes']) ? (int)$data['duree_minutes'] : null;

            $this->soutenanceService->reschedule(
                $soutenance,
                $dateSoutenance,
                $heureDebut,
                $salle,
                $dureeMinutes
            );

            $this->addFlash('success', 'Soutenance modifiée avec succès');
            return $this->redirect('/admin/soutenances');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la modification : ' . $e->getMessage());
            return $this->redirect("/admin/soutenances/{$id}/modifier");
        }
    }

    public function jurys(Request $request): Response
    {
        $jurys = $this->juryRepository->findAll();

        return $this->render('admin/soutenance/jurys', [
            'jurys' => $jurys,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function composeJury(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule');

        return $this->render('admin/soutenance/compose-jury', [
            'matricule' => $matricule,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function saveJury(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/soutenances/jurys');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/soutenances/jurys');
        }

        try {
            $matricule = trim((string)($data['matricule'] ?? ''));
            $etudiant = $this->etudiantRepository->findByMatricule($matricule);

            if ($etudiant === null) {
                $this->addFlash('error', 'Étudiant introuvable');
                return $this->redirect('/admin/soutenances/jurys');
            }

            $annee = $this->anneeAcademiqueRepository->find((int)($data['annee_id'] ?? 0));

            if ($annee === null) {
                $this->addFlash('error', 'Année académique introuvable');
                return $this->redirect('/admin/soutenances/jurys');
            }

            $createur = $this->getUser();
            if ($createur === null) {
                $this->addFlash('error', 'Utilisateur non authentifié');
                return $this->redirect('/admin/soutenances/jurys');
            }

            $jury = $this->juryService->createJury($etudiant, $annee, $createur);

            $membres = $data['membres'] ?? [];
            if (is_array($membres)) {
                foreach ($membres as $membre) {
                    if (!is_array($membre)) {
                        continue;
                    }

                    $enseignant = $this->enseignantRepository->findByMatricule(trim((string)($membre['matricule_enseignant'] ?? '')));
                    $roleJury = $this->roleJuryRepository->find((int)($membre['role_id'] ?? 0));

                    if ($enseignant === null || $roleJury === null) {
                        continue;
                    }

                    $commentaire = !empty($membre['commentaire']) ? trim((string)$membre['commentaire']) : null;

                    $this->juryService->addMember($jury, $enseignant, $roleJury, $createur, $commentaire);
                }
            }

            $this->addFlash('success', 'Jury enregistré avec succès');
            return $this->redirect('/admin/soutenances/jurys');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'enregistrement du jury : ' . $e->getMessage());
            return $this->redirect('/admin/soutenances/jurys');
        }
    }

    public function planning(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        return $this->render('admin/soutenance/planning', [
            'soutenances' => $soutenances,
        ]);
    }

    public function planningPdf(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        if (empty($soutenances)) {
            $this->addFlash('info', 'Aucune soutenance à exporter');
            return $this->redirect('/admin/soutenances/planning');
        }

        $content = 'Planning des soutenances - Généré le ' . date('d/m/Y H:i');

        return new Psr7Response(200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="planning-soutenances.pdf"',
        ], $content);
    }

    public function notationForm(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/notation', [
            'soutenance' => $soutenance,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function saveNotation(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');

        if ($id === null) {
            $this->addFlash('error', 'Identifiant invalide');
            return $this->redirect('/admin/soutenances');
        }

        $soutenance = $this->soutenanceRepository->find((int) $id);

        if ($soutenance === null) {
            $this->addFlash('error', 'Soutenance non trouvée');
            return $this->redirect('/admin/soutenances');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect("/admin/soutenances/{$id}/notation");
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect("/admin/soutenances/{$id}/notation");
        }

        try {
            $utilisateur = $this->getUser();
            if ($utilisateur === null) {
                $this->addFlash('error', 'Utilisateur non authentifié');
                return $this->redirect("/admin/soutenances/{$id}/notation");
            }

            $notes = $data['notes'] ?? [];
            if (is_array($notes)) {
                foreach ($notes as $entry) {
                    if (!is_array($entry)) {
                        continue;
                    }

                    $critere = $this->critereRepository->find((int)($entry['critere_id'] ?? 0));
                    if ($critere === null) {
                        continue;
                    }

                    $note = trim((string)($entry['note'] ?? ''));
                    $commentaire = !empty($entry['commentaire']) ? trim((string)$entry['commentaire']) : null;

                    $this->soutenanceService->recordNote($soutenance, $critere, $note, $utilisateur, $commentaire);
                }
            }

            $this->addFlash('success', 'Notes enregistrées avec succès');
            return $this->redirect('/admin/soutenances');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'enregistrement des notes : ' . $e->getMessage());
            return $this->redirect("/admin/soutenances/{$id}/notation");
        }
    }

    public function deliberation(Request $request): Response
    {
        $soutenances = $this->soutenanceRepository->findAll();

        return $this->render('admin/soutenance/deliberation', [
            'soutenances' => $soutenances,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function delibererForm(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $soutenance = $id !== null ? $this->soutenanceRepository->find((int) $id) : null;

        return $this->render('admin/soutenance/deliberer', [
            'soutenance' => $soutenance,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function deliberer(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');

        if ($id === null) {
            $this->addFlash('error', 'Identifiant invalide');
            return $this->redirect('/admin/soutenances/deliberation');
        }

        $soutenance = $this->soutenanceRepository->find((int) $id);

        if ($soutenance === null) {
            $this->addFlash('error', 'Soutenance non trouvée');
            return $this->redirect('/admin/soutenances/deliberation');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect("/admin/soutenances/{$id}/deliberer");
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect("/admin/soutenances/{$id}/deliberer");
        }

        try {
            $validateur = $this->getUser();
            if ($validateur === null) {
                $this->addFlash('error', 'Utilisateur non authentifié');
                return $this->redirect("/admin/soutenances/{$id}/deliberer");
            }

            $annee = $this->anneeAcademiqueRepository->find((int)($data['annee_id'] ?? 0));
            if ($annee === null) {
                $this->addFlash('error', 'Année académique introuvable');
                return $this->redirect("/admin/soutenances/{$id}/deliberer");
            }

            $etudiant = $soutenance->getEtudiant();
            if ($etudiant === null) {
                $this->addFlash('error', 'Étudiant introuvable pour cette soutenance');
                return $this->redirect("/admin/soutenances/{$id}/deliberer");
            }

            $moyenneM1 = trim((string)($data['moyenne_m1'] ?? ''));
            $moyenneS1M2 = !empty($data['moyenne_s1_m2']) ? trim((string)$data['moyenne_s1_m2']) : null;

            $this->soutenanceService->calculateFinalResult(
                $soutenance,
                $annee,
                $etudiant,
                $validateur,
                $moyenneM1,
                $moyenneS1M2
            );

            $this->addFlash('success', 'Délibération enregistrée avec succès');
            return $this->redirect('/admin/soutenances/deliberation');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la délibération : ' . $e->getMessage());
            return $this->redirect("/admin/soutenances/{$id}/deliberer");
        }
    }

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
