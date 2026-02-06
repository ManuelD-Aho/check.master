<?php
declare(strict_types=1);

namespace App\Controller\Etudiant;

use App\Controller\AbstractController;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Stage\CandidatureRepository;
use App\Repository\Student\EtudiantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Stage\CandidatureService;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CandidatureController extends AbstractController
{
    private CandidatureService $candidatureService;
    private CandidatureRepository $candidatureRepository;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;
    private EtudiantRepository $etudiantRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        CandidatureService $candidatureService,
        CandidatureRepository $candidatureRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
        EtudiantRepository $etudiantRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->candidatureService = $candidatureService;
        $this->candidatureRepository = $candidatureRepository;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
        $this->etudiantRepository = $etudiantRepository;
    }

    public function index(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/candidature/index', [
            'matricule' => $matricule,
        ]);
    }

    public function create(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/candidature/create', [
            'matricule' => $matricule,
        ]);
    }

    public function store(Request $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $csrfToken = (string) ($body['_csrf_token'] ?? '');

        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Jeton CSRF invalide');
            return $this->redirect('/etudiant/candidature/formulaire');
        }

        $user = $this->getUser();
        $matricule = $user?->getMatriculeEtudiant();

        if ($user === null || $matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $etudiant = $this->etudiantRepository->findByMatricule($matricule);
        if ($etudiant === null) {
            $this->addFlash('error', 'Etudiant introuvable');
            return $this->redirect('/etudiant/candidature');
        }

        $annees = $this->anneeAcademiqueRepository->findActive();
        $anneeAcademique = $annees[0] ?? null;
        if ($anneeAcademique === null) {
            $this->addFlash('error', 'Aucune annee academique active');
            return $this->redirect('/etudiant/candidature');
        }

        $candidature = $this->candidatureRepository->findByEtudiantAndAnnee(
            $matricule,
            $anneeAcademique->getIdAnneeAcademique()
        );

        $commentaire = isset($body['commentaire']) ? (string) $body['commentaire'] : null;

        if ($candidature !== null) {
            $this->candidatureService->saveDraft($candidature, $user, $commentaire);
            $this->addFlash('success', 'Candidature sauvegardee');
        } else {
            $this->candidatureService->createCandidature($etudiant, $anneeAcademique, $user);
            $this->addFlash('success', 'Candidature creee');
        }

        return $this->redirect('/etudiant/candidature');
    }

    public function edit(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/candidature/edit', [
            'matricule' => $matricule,
        ]);
    }

    public function update(Request $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $csrfToken = (string) ($body['_csrf_token'] ?? '');

        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Jeton CSRF invalide');
            return $this->redirect('/etudiant/candidature');
        }

        $user = $this->getUser();
        if ($user === null) {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $id = $this->getRouteParam($request, 'id');
        if ($id === null) {
            $this->addFlash('error', 'Candidature introuvable');
            return $this->redirect('/etudiant/candidature');
        }

        $candidature = $this->candidatureRepository->find((int) $id);
        if ($candidature === null) {
            $this->addFlash('error', 'Candidature introuvable');
            return $this->redirect('/etudiant/candidature');
        }

        $commentaire = isset($body['commentaire']) ? (string) $body['commentaire'] : null;
        $this->candidatureService->saveDraft($candidature, $user, $commentaire);

        $this->addFlash('success', 'Candidature mise a jour');
        return $this->redirect('/etudiant/candidature');
    }

    public function submit(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        $matricule = $user?->getMatriculeEtudiant();

        if ($user === null || $matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $id = $this->getRouteParam($request, 'id');
        $candidature = null;

        if ($id !== null) {
            $candidature = $this->candidatureRepository->find((int) $id);
        } else {
            $annees = $this->anneeAcademiqueRepository->findActive();
            $anneeAcademique = $annees[0] ?? null;
            if ($anneeAcademique !== null) {
                $candidature = $this->candidatureRepository->findByEtudiantAndAnnee(
                    $matricule,
                    $anneeAcademique->getIdAnneeAcademique()
                );
            }
        }

        if ($candidature === null) {
            $this->addFlash('error', 'Candidature introuvable');
            return $this->redirect('/etudiant/candidature');
        }

        $this->candidatureService->submit($candidature, $user);
        $this->addFlash('success', 'Candidature soumise');
        return $this->redirect('/etudiant/candidature');
    }

    public function show(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/candidature/show', [
            'matricule' => $matricule,
        ]);
    }

    public function formulaire(Request $request): ResponseInterface
    {
        return $this->create($request);
    }

    public function sauvegarder(Request $request): ResponseInterface
    {
        return $this->store($request);
    }

    public function soumettre(Request $request): ResponseInterface
    {
        return $this->submit($request);
    }

    public function recapitulatif(Request $request): ResponseInterface
    {
        return $this->show($request);
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
