<?php
declare(strict_types=1);

namespace App\Controller\Etudiant;

use App\Controller\AbstractController;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Report\RapportRepository;
use App\Repository\Student\EtudiantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Rapport\RapportService;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class RapportController extends AbstractController
{
    private RapportService $rapportService;
    private RapportRepository $rapportRepository;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;
    private EtudiantRepository $etudiantRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        RapportService $rapportService,
        RapportRepository $rapportRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
        EtudiantRepository $etudiantRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->rapportService = $rapportService;
        $this->rapportRepository = $rapportRepository;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
        $this->etudiantRepository = $etudiantRepository;
    }

    public function index(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/index', [
            'matricule' => $matricule,
        ]);
    }

    public function create(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/create', [
            'matricule' => $matricule,
        ]);
    }

    public function edit(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/edit', [
            'matricule' => $matricule,
        ]);
    }

    public function save(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/save', [
            'matricule' => $matricule,
        ]);
    }

    public function submit(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/submit', [
            'matricule' => $matricule,
        ]);
    }

    public function show(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/show', [
            'matricule' => $matricule,
        ]);
    }

    public function versions(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/versions', [
            'matricule' => $matricule,
        ]);
    }

    public function choisirModele(Request $request): ResponseInterface
    {
        return $this->create($request);
    }

    public function creer(Request $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $csrfToken = (string) ($body['_csrf_token'] ?? '');

        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Jeton CSRF invalide');
            return $this->redirect('/etudiant/rapport/nouveau');
        }

        $user = $this->getUser();
        $matricule = $user?->getMatriculeEtudiant();

        if ($user === null || $matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $etudiant = $this->etudiantRepository->findByMatricule($matricule);
        if ($etudiant === null) {
            $this->addFlash('error', 'Etudiant introuvable');
            return $this->redirect('/etudiant/rapport');
        }

        $annees = $this->anneeAcademiqueRepository->findActive();
        $anneeAcademique = $annees[0] ?? null;
        if ($anneeAcademique === null) {
            $this->addFlash('error', 'Aucune annee academique active');
            return $this->redirect('/etudiant/rapport');
        }

        $titre = (string) ($body['titre'] ?? '');
        $theme = (string) ($body['theme'] ?? '');
        $contenuHtml = (string) ($body['contenu_html'] ?? '');

        $this->rapportService->createRapport($etudiant, $anneeAcademique, $titre, $theme, $contenuHtml, $user);

        $this->addFlash('success', 'Rapport cree');
        return $this->redirect('/etudiant/rapport/editeur');
    }

    public function editeur(Request $request): ResponseInterface
    {
        return $this->edit($request);
    }

    public function sauvegarder(Request $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $csrfToken = (string) ($body['_csrf_token'] ?? '');

        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Jeton CSRF invalide');
            return $this->redirect('/etudiant/rapport/editeur');
        }

        $user = $this->getUser();
        $matricule = $user?->getMatriculeEtudiant();

        if ($user === null || $matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $rapport = $this->findRapportByMatricule($matricule);
        if ($rapport === null) {
            $this->addFlash('error', 'Rapport introuvable');
            return $this->redirect('/etudiant/rapport');
        }

        $contenuHtml = (string) ($body['contenu_html'] ?? '');
        $commentaire = isset($body['commentaire']) ? (string) $body['commentaire'] : null;

        $this->rapportService->saveContent($rapport, $contenuHtml, $user, $commentaire);

        $this->addFlash('success', 'Rapport sauvegarde');
        return $this->redirect('/etudiant/rapport/editeur');
    }

    public function informations(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/informations', [
            'matricule' => $matricule,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function updateInformations(Request $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $csrfToken = (string) ($body['_csrf_token'] ?? '');

        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Jeton CSRF invalide');
            return $this->redirect('/etudiant/rapport/informations');
        }

        $user = $this->getUser();
        $matricule = $user?->getMatriculeEtudiant();

        if ($user === null || $matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $rapport = $this->findRapportByMatricule($matricule);
        if ($rapport === null) {
            $this->addFlash('error', 'Rapport introuvable');
            return $this->redirect('/etudiant/rapport');
        }

        $contenuHtml = (string) ($body['contenu_html'] ?? $rapport->getContenuHtml() ?? '');
        $commentaire = isset($body['commentaire']) ? (string) $body['commentaire'] : null;

        $this->rapportService->saveContent($rapport, $contenuHtml, $user, $commentaire);

        $this->addFlash('success', 'Informations mises a jour');
        return $this->redirect('/etudiant/rapport/informations');
    }

    public function soumettre(Request $request): ResponseInterface
    {
        $user = $this->getUser();
        $matricule = $user?->getMatriculeEtudiant();

        if ($user === null || $matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        $rapport = $this->findRapportByMatricule($matricule);
        if ($rapport === null) {
            $this->addFlash('error', 'Rapport introuvable');
            return $this->redirect('/etudiant/rapport');
        }

        $this->rapportService->submit($rapport, $user);

        $this->addFlash('success', 'Rapport soumis');
        return $this->redirect('/etudiant/rapport');
    }

    public function voir(Request $request): ResponseInterface
    {
        return $this->show($request);
    }

    public function telecharger(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return new Response(200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport.pdf"',
        ], '');
    }

    private function findRapportByMatricule(string $matricule): ?object
    {
        $annees = $this->anneeAcademiqueRepository->findActive();
        $anneeAcademique = $annees[0] ?? null;
        if ($anneeAcademique === null) {
            return null;
        }

        return $this->rapportRepository->findByEtudiantAndAnnee(
            $matricule,
            $anneeAcademique->getIdAnneeAcademique()
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
}
