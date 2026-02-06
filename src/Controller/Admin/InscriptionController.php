<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Academic\NiveauEtudeRepository;
use App\Repository\Student\EtudiantRepository;
use App\Repository\Student\InscriptionRepository;
use App\Repository\Student\VersementRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Etudiant\InscriptionService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InscriptionController extends AbstractController
{
    private InscriptionRepository $inscriptionRepository;
    private VersementRepository $versementRepository;
    private EtudiantRepository $etudiantRepository;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;
    private NiveauEtudeRepository $niveauEtudeRepository;
    private InscriptionService $inscriptionService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        InscriptionRepository $inscriptionRepository,
        VersementRepository $versementRepository,
        EtudiantRepository $etudiantRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
        NiveauEtudeRepository $niveauEtudeRepository,
        InscriptionService $inscriptionService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->inscriptionRepository = $inscriptionRepository;
        $this->versementRepository = $versementRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
        $this->niveauEtudeRepository = $niveauEtudeRepository;
        $this->inscriptionService = $inscriptionService;
    }

    public function index(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule');
        $inscriptions = $matricule !== null
            ? $this->inscriptionRepository->findByEtudiant($matricule)
            : $this->inscriptionRepository->findAll();

        return $this->render('admin/inscription/index', [
            'inscriptions' => $inscriptions,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'inscriptionId');
        $inscription = $id !== null ? $this->inscriptionRepository->find((int) $id) : null;
        $versements = $id !== null ? $this->versementRepository->findByInscription((int) $id) : [];

        return $this->render('admin/inscription/show', [
            'inscription' => $inscription,
            'versements' => $versements,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/inscription/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/inscriptions');
        }

        // CSRF validation
        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/inscriptions');
        }

        try {
            // Resolve etudiant from route param or POST data
            $matricule = $this->getRouteParam($request, 'matricule') ?? (string)($data['matricule'] ?? '');
            $etudiant = null;

            if (!empty($data['id_etudiant'])) {
                $etudiant = $this->etudiantRepository->find((int)$data['id_etudiant']);
            }

            if ($etudiant === null && $matricule !== '') {
                $etudiant = $this->etudiantRepository->findByMatricule($matricule);
            }

            if ($etudiant === null) {
                $this->addFlash('error', 'Étudiant non trouvé');
                return $this->redirect('/admin/inscriptions');
            }

            // Resolve annee academique
            $anneeAcademiqueId = (int)($data['id_annee_academique'] ?? 0);
            $anneeAcademique = $anneeAcademiqueId > 0
                ? $this->anneeAcademiqueRepository->find($anneeAcademiqueId)
                : null;

            if ($anneeAcademique === null) {
                $this->addFlash('error', 'Année académique non trouvée');
                return $this->redirect('/admin/inscriptions');
            }

            // Resolve niveau etude
            $niveauEtudeId = (int)($data['id_niveau_etude'] ?? 0);
            $niveauEtude = $niveauEtudeId > 0
                ? $this->niveauEtudeRepository->find($niveauEtudeId)
                : null;

            if ($niveauEtude === null) {
                $this->addFlash('error', 'Niveau d\'étude non trouvé');
                return $this->redirect('/admin/inscriptions');
            }

            $montantInscription = trim((string)($data['montant_inscription'] ?? '0'));
            $montantScolarite = trim((string)($data['montant_scolarite'] ?? '0'));
            $nombreTranches = (int)($data['nombre_tranches'] ?? 1);

            $dateInscription = null;
            if (!empty($data['date_inscription'])) {
                $date = \DateTimeImmutable::createFromFormat('Y-m-d', (string)$data['date_inscription']);
                if ($date !== false) {
                    $dateInscription = $date;
                }
            }

            $this->inscriptionService->createInscription(
                $etudiant,
                $niveauEtude,
                $anneeAcademique,
                $montantInscription,
                $montantScolarite,
                $nombreTranches,
                $dateInscription
            );

            $this->addFlash('success', 'Inscription enregistrée avec succès');
            return $this->redirect('/admin/inscriptions');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
            return $this->redirect('/admin/inscriptions');
        }
    }

    public function recordVersement(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'inscriptionId');
        $inscription = $id !== null ? $this->inscriptionRepository->find((int) $id) : null;
        $versements = $id !== null ? $this->versementRepository->findByInscription((int) $id) : [];

        return $this->render('admin/inscription/versement', [
            'inscription' => $inscription,
            'versements' => $versements,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function generateRecu(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id') ?? $this->getRouteParam($request, 'versementId');
        $versement = $id !== null ? $this->versementRepository->find((int) $id) : null;

        return $this->render('admin/inscription/recu', [
            'versement' => $versement,
        ]);
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
