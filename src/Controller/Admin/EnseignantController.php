<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Staff\Enseignant;
use App\Entity\Staff\TypeEnseignant;
use App\Repository\Staff\EnseignantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EnseignantController extends AbstractController
{
    private EnseignantRepository $enseignantRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        EnseignantRepository $enseignantRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->enseignantRepository = $enseignantRepository;
        $this->entityManager = $entityManager;
    }

    public function index(Request $request): Response
    {
        $enseignants = $this->enseignantRepository->findAll();

        return $this->render('admin/enseignant/index', [
            'enseignants' => $enseignants,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $enseignant = $matricule !== null
            ? $this->enseignantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/enseignant/show', [
            'enseignant' => $enseignant,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/enseignant/create', [
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/enseignants');
        }

        // CSRF validation
        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/enseignants/nouveau');
        }

        try {
            $enseignant = new Enseignant();

            // Generate matricule
            $matricule = sprintf('ENS-%s-%04d', date('Y'), count($this->enseignantRepository->findAll()) + 1);
            $enseignant->setMatriculeEnseignant($matricule);

            // Set basic fields
            $enseignant->setNomEnseignant(trim((string)($data['nom'] ?? '')));
            $enseignant->setPrenomEnseignant(trim((string)($data['prenom'] ?? '')));
            $enseignant->setEmailEnseignant(trim((string)($data['email'] ?? '')));

            if (!empty($data['telephone'])) {
                $enseignant->setTelephoneEnseignant(trim((string)$data['telephone']));
            }

            if (!empty($data['specialite'])) {
                $enseignant->setSpecialite(trim((string)$data['specialite']));
            }

            // TypeEnseignant
            $typeValue = (string)($data['type_enseignant'] ?? '');
            if ($typeValue === 'permanent') {
                $enseignant->setTypeEnseignant(TypeEnseignant::Permanent);
            } elseif ($typeValue === 'vacataire') {
                $enseignant->setTypeEnseignant(TypeEnseignant::Vacataire);
            }

            $enseignant->setActif(true);
            $now = new DateTimeImmutable();
            $enseignant->setDateCreation($now);
            $enseignant->setDateModification($now);

            $this->entityManager->persist($enseignant);
            $this->entityManager->flush();

            $this->addFlash('success', "Enseignant {$matricule} créé avec succès");
            return $this->redirect("/admin/enseignants/{$matricule}");

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création : ' . $e->getMessage());
            return $this->redirect('/admin/enseignants/nouveau');
        }
    }

    public function edit(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');
        $enseignant = $matricule !== null
            ? $this->enseignantRepository->findByMatricule($matricule)
            : null;

        return $this->render('admin/enseignant/edit', [
            'enseignant' => $enseignant,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function update(Request $request): Response
    {
        $matricule = $this->getRouteParam($request, 'matricule') ?? $this->getRouteParam($request, 'id');

        if ($matricule === null) {
            $this->addFlash('error', 'Matricule invalide');
            return $this->redirect('/admin/enseignants');
        }

        $enseignant = $this->enseignantRepository->findByMatricule($matricule);

        if ($enseignant === null) {
            $this->addFlash('error', 'Enseignant non trouvé');
            return $this->redirect('/admin/enseignants');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect("/admin/enseignants/{$matricule}/modifier");
        }

        // CSRF validation
        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect("/admin/enseignants/{$matricule}/modifier");
        }

        try {
            $enseignant->setNomEnseignant(trim((string)($data['nom'] ?? '')));
            $enseignant->setPrenomEnseignant(trim((string)($data['prenom'] ?? '')));
            $enseignant->setEmailEnseignant(trim((string)($data['email'] ?? '')));

            if (isset($data['telephone'])) {
                $enseignant->setTelephoneEnseignant(!empty($data['telephone']) ? trim((string)$data['telephone']) : null);
            }

            if (isset($data['specialite'])) {
                $enseignant->setSpecialite(!empty($data['specialite']) ? trim((string)$data['specialite']) : null);
            }

            // TypeEnseignant
            $typeValue = (string)($data['type_enseignant'] ?? '');
            if ($typeValue === 'permanent') {
                $enseignant->setTypeEnseignant(TypeEnseignant::Permanent);
            } elseif ($typeValue === 'vacataire') {
                $enseignant->setTypeEnseignant(TypeEnseignant::Vacataire);
            }

            $enseignant->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($enseignant);
            $this->entityManager->flush();

            $this->addFlash('success', 'Enseignant mis à jour avec succès');
            return $this->redirect("/admin/enseignants/{$matricule}");

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
            return $this->redirect("/admin/enseignants/{$matricule}/modifier");
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
