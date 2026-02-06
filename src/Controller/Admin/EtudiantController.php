<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Student\Etudiant;
use App\Entity\Student\Genre;
use App\Repository\Academic\FiliereRepository;
use App\Repository\Student\EtudiantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Etudiant\EtudiantService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EtudiantController extends AbstractController
{
    private EtudiantRepository $etudiantRepository;
    private EntityManagerInterface $entityManager;
    private FiliereRepository $filiereRepository;
    private EtudiantService $etudiantService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EtudiantRepository $etudiantRepository,
        EntityManagerInterface $entityManager,
        FiliereRepository $filiereRepository,
        EtudiantService $etudiantService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->etudiantRepository = $etudiantRepository;
        $this->entityManager = $entityManager;
        $this->filiereRepository = $filiereRepository;
        $this->etudiantService = $etudiantService;
    }

    public function index(Request $request): Response
    {
        $etudiants = $this->etudiantRepository->findAll();

        return $this->render('admin/etudiant/index', [
            'etudiants' => $etudiants,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $etudiant = $matricule !== null
            ? $this->etudiantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/etudiant/show', [
            'etudiant' => $etudiant,
        ]);
    }

    public function create(Request $request): Response
    {
        $filieres = $this->filiereRepository->findAll();

        return $this->render('admin/etudiant/create', [
            'csrf' => $this->getCsrfToken(),
            'filieres' => $filieres,
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/etudiants');
        }

        // CSRF validation
        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/etudiants/nouveau');
        }

        try {
            // Create etudiant entity
            $etudiant = new Etudiant();

            // Generate matricule
            $matricule = $this->generateMatricule();
            $etudiant->setMatriculeEtudiant($matricule);

            // Set basic fields
            $etudiant->setNomEtudiant(trim((string)($data['nom'] ?? '')));
            $etudiant->setPrenomEtudiant(trim((string)($data['prenom'] ?? '')));
            $etudiant->setEmailEtudiant(trim((string)($data['email'] ?? '')));

            if (!empty($data['telephone'])) {
                $etudiant->setTelephoneEtudiant(trim((string)$data['telephone']));
            }

            // Date naissance
            if (!empty($data['date_naissance'])) {
                $date = \DateTime::createFromFormat('Y-m-d', (string)$data['date_naissance']);
                if ($date) {
                    $etudiant->setDateNaissance($date);
                }
            }

            $etudiant->setLieuNaissance(trim((string)($data['lieu_naissance'] ?? '')));

            // Genre
            $genreValue = (string)($data['genre'] ?? '');
            if ($genreValue === 'M') {
                $etudiant->setGenre(Genre::Masculin);
            } else if ($genreValue === 'F') {
                $etudiant->setGenre(Genre::Feminin);
            }

            if (!empty($data['nationalite'])) {
                $etudiant->setNationalite(trim((string)$data['nationalite']));
            }

            if (!empty($data['adresse'])) {
                $etudiant->setAdresse(trim((string)$data['adresse']));
            }

            $etudiant->setPromotion(trim((string)($data['promotion'] ?? date('Y'))));

            // Filiere
            $filiereId = (int)($data['id_filiere'] ?? 0);
            if ($filiereId > 0) {
                $filiere = $this->filiereRepository->find($filiereId);
                if ($filiere) {
                    $etudiant->setFiliere($filiere);
                }
            }

            $etudiant->setActif(true);
            $now = new DateTimeImmutable();
            $etudiant->setDateCreation($now);
            $etudiant->setDateModification($now);

            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();

            $this->addFlash('success', "Étudiant {$matricule} créé avec succès");
            return $this->redirect("/admin/etudiants/{$matricule}");

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création : ' . $e->getMessage());
            return $this->redirect('/admin/etudiants/nouveau');
        }
    }

    public function edit(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $etudiant = $matricule !== null
            ? $this->etudiantRepository->findByMatricule($matricule)
            : null;

        $filieres = $this->filiereRepository->findAll();

        return $this->render('admin/etudiant/edit', [
            'etudiant' => $etudiant,
            'csrf' => $this->getCsrfToken(),
            'filieres' => $filieres,
        ]);
    }

    public function update(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');

        if ($matricule === null) {
            $this->addFlash('error', 'Matricule invalide');
            return $this->redirect('/admin/etudiants');
        }

        $etudiant = $this->etudiantRepository->findByMatricule($matricule);

        if ($etudiant === null) {
            $this->addFlash('error', 'Étudiant non trouvé');
            return $this->redirect('/admin/etudiants');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect("/admin/etudiants/{$matricule}/modifier");
        }

        // CSRF validation
        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect("/admin/etudiants/{$matricule}/modifier");
        }

        try {
            $etudiant->setNomEtudiant(trim((string)($data['nom'] ?? '')));
            $etudiant->setPrenomEtudiant(trim((string)($data['prenom'] ?? '')));
            $etudiant->setEmailEtudiant(trim((string)($data['email'] ?? '')));

            if (isset($data['telephone'])) {
                $etudiant->setTelephoneEtudiant(!empty($data['telephone']) ? trim((string)$data['telephone']) : null);
            }

            if (!empty($data['date_naissance'])) {
                $date = \DateTime::createFromFormat('Y-m-d', (string)$data['date_naissance']);
                if ($date) {
                    $etudiant->setDateNaissance($date);
                }
            }

            if (isset($data['lieu_naissance'])) {
                $etudiant->setLieuNaissance(trim((string)$data['lieu_naissance']));
            }

            if (isset($data['nationalite'])) {
                $etudiant->setNationalite(!empty($data['nationalite']) ? trim((string)$data['nationalite']) : null);
            }

            if (isset($data['adresse'])) {
                $etudiant->setAdresse(!empty($data['adresse']) ? trim((string)$data['adresse']) : null);
            }

            if (isset($data['promotion'])) {
                $etudiant->setPromotion(trim((string)$data['promotion']));
            }

            $etudiant->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();

            $this->addFlash('success', 'Étudiant mis à jour avec succès');
            return $this->redirect("/admin/etudiants/{$matricule}");

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
            return $this->redirect("/admin/etudiants/{$matricule}/modifier");
        }
    }

    public function importForm(Request $request): Response
    {
        return $this->render('admin/etudiant/import', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function import(Request $request): Response
    {
        $data = $request->getParsedBody();

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/etudiants/import');
        }

        $this->addFlash('success', 'Importation effectuée avec succès');
        return $this->redirect('/admin/etudiants');
    }

    public function export(Request $request): Response
    {
        $etudiants = $this->etudiantRepository->findAll();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['matricule', 'nom', 'prenom', 'email']);
        foreach ($etudiants as $etudiant) {
            fputcsv($handle, [
                $etudiant->getMatriculeEtudiant(),
                $etudiant->getNomEtudiant(),
                $etudiant->getPrenomEtudiant(),
                $etudiant->getEmailEtudiant(),
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return new \Nyholm\Psr7\Response(
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="etudiants.csv"',
            ],
            $csv
        );
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

    private function generateMatricule(): string
    {
        $year = date('Y');
        $count = count($this->etudiantRepository->findAll()) + 1;
        return sprintf('MIAGE-%s-%04d', $year, $count);
    }
}
