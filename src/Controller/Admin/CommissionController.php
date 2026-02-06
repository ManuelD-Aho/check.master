<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Commission\RoleCommission;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Commission\CompteRenduCommissionRepository;
use App\Repository\Commission\MembreCommissionRepository;
use App\Repository\Commission\SessionCommissionRepository;
use App\Repository\Report\RapportRepository;
use App\Repository\Staff\EnseignantRepository;
use App\Repository\User\UtilisateurRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Commission\AffectationService;
use App\Service\Commission\CommissionService;
use DateTimeImmutable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommissionController extends AbstractController
{
    private MembreCommissionRepository $membreRepository;
    private SessionCommissionRepository $sessionRepository;
    private CompteRenduCommissionRepository $compteRenduRepository;
    private CommissionService $commissionService;
    private AffectationService $affectationService;
    private UtilisateurRepository $utilisateurRepository;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;
    private EnseignantRepository $enseignantRepository;
    private RapportRepository $rapportRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        MembreCommissionRepository $membreRepository,
        SessionCommissionRepository $sessionRepository,
        CompteRenduCommissionRepository $compteRenduRepository,
        CommissionService $commissionService,
        AffectationService $affectationService,
        UtilisateurRepository $utilisateurRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
        EnseignantRepository $enseignantRepository,
        RapportRepository $rapportRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->membreRepository = $membreRepository;
        $this->sessionRepository = $sessionRepository;
        $this->compteRenduRepository = $compteRenduRepository;
        $this->commissionService = $commissionService;
        $this->affectationService = $affectationService;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
        $this->enseignantRepository = $enseignantRepository;
        $this->rapportRepository = $rapportRepository;
    }

    public function membres(Request $request): Response
    {
        $membres = $this->membreRepository->findAll();

        return $this->render('admin/commission/membres', [
            'membres' => $membres,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function addMembre(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/commission/membres');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/commission/membres');
        }

        try {
            $utilisateurId = (int)($data['utilisateur_id'] ?? 0);
            $anneeId = (int)($data['annee_id'] ?? 0);
            $roleValue = (string)($data['role'] ?? '');

            $utilisateur = $utilisateurId > 0 ? $this->utilisateurRepository->find($utilisateurId) : null;
            $annee = $anneeId > 0 ? $this->anneeAcademiqueRepository->find($anneeId) : null;
            $role = RoleCommission::tryFrom($roleValue);

            if ($utilisateur === null || $annee === null || $role === null) {
                $this->addFlash('error', 'Paramètres invalides');
                return $this->redirect('/admin/commission/membres');
            }

            $this->commissionService->addMembre($annee, $utilisateur, $role, new DateTimeImmutable());

            $this->addFlash('success', 'Membre ajouté');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'ajout du membre : ' . $e->getMessage());
        }

        return $this->redirect('/admin/commission/membres');
    }

    public function removeMembre(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $membre = $id !== null ? $this->membreRepository->find((int) $id) : null;

        if ($membre !== null) {
            $this->membreRepository->remove($membre);
            $this->addFlash('success', 'Membre supprime');
        } else {
            $this->addFlash('error', 'Membre introuvable');
        }

        return $this->redirect('/admin/commission/membres');
    }

    public function sessions(Request $request): Response
    {
        $sessions = $this->sessionRepository->findAll();

        return $this->render('admin/commission/sessions', [
            'sessions' => $sessions,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function createSession(Request $request): Response
    {
        return $this->redirect('/admin/commission/sessions');
    }

    public function closeSession(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $session = $id !== null ? $this->sessionRepository->find((int) $id) : null;

        if ($session === null) {
            $this->addFlash('error', 'Session introuvable');
            return $this->redirect('/admin/commission/sessions');
        }

        try {
            $this->commissionService->closeSession($session);
            $this->addFlash('success', 'Session clôturée');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la clôture : ' . $e->getMessage());
        }

        return $this->redirect('/admin/commission/sessions');
    }

    public function compteRendu(Request $request): Response
    {
        $compteRendus = $this->compteRenduRepository->findAll();

        return $this->render('admin/commission/compte-rendu', [
            'compteRendus' => $compteRendus,
        ]);
    }

    public function saveMembres(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/commission/membres');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/commission/membres');
        }

        $membres = $data['membres'] ?? [];
        if (!is_array($membres) || $membres === []) {
            $this->addFlash('error', 'Aucun membre à enregistrer');
            return $this->redirect('/admin/commission/membres');
        }

        try {
            foreach ($membres as $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                $utilisateurId = (int)($entry['utilisateur_id'] ?? 0);
                $anneeId = (int)($entry['annee_id'] ?? 0);
                $roleValue = (string)($entry['role'] ?? '');

                $utilisateur = $utilisateurId > 0 ? $this->utilisateurRepository->find($utilisateurId) : null;
                $annee = $anneeId > 0 ? $this->anneeAcademiqueRepository->find($anneeId) : null;
                $role = RoleCommission::tryFrom($roleValue);

                if ($utilisateur === null || $annee === null || $role === null) {
                    continue;
                }

                $this->commissionService->addMembre($annee, $utilisateur, $role, new DateTimeImmutable());
            }

            $this->addFlash('success', 'Configuration des membres enregistrée');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }

        return $this->redirect('/admin/commission/membres');
    }

    public function storeSession(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/commission/sessions');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/commission/sessions');
        }

        try {
            $anneeId = (int)($data['annee_id'] ?? 0);
            $mois = (int)($data['mois'] ?? 0);
            $annee = (int)($data['annee'] ?? 0);
            $libelle = trim((string)($data['libelle'] ?? ''));
            $dateDebutStr = (string)($data['date_debut'] ?? '');
            $dateFinStr = (string)($data['date_fin'] ?? '');

            $anneeAcademique = $anneeId > 0 ? $this->anneeAcademiqueRepository->find($anneeId) : null;

            if ($anneeAcademique === null || $libelle === '' || $dateDebutStr === '' || $dateFinStr === '') {
                $this->addFlash('error', 'Paramètres invalides');
                return $this->redirect('/admin/commission/sessions');
            }

            $dateDebut = new DateTimeImmutable($dateDebutStr);
            $dateFin = new DateTimeImmutable($dateFinStr);

            $this->commissionService->createSession($anneeAcademique, $mois, $annee, $libelle, $dateDebut, $dateFin);

            $this->addFlash('success', 'Session enregistrée');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de la session : ' . $e->getMessage());
        }

        return $this->redirect('/admin/commission/sessions');
    }

    public function showSession(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $session = $id !== null ? $this->sessionRepository->find((int) $id) : null;

        return $this->render('admin/commission/sessions-show', [
            'session' => $session,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function downloadSessionPdf(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');

        $this->addFlash('info', 'Telechargement du PDF en cours');

        return $this->redirect('/admin/commission/sessions/' . $id);
    }

    public function assignation(Request $request): Response
    {
        return $this->render('admin/commission/assignation', [
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function assignationForm(Request $request): Response
    {
        $rapportId = $this->getRouteParam($request, 'rapportId');

        return $this->render('admin/commission/assignation-form', [
            'rapportId' => $rapportId,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function assign(Request $request): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/commission/assignation');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/commission/assignation');
        }

        try {
            $rapportId = (int)($data['rapport_id'] ?? 0);
            $enseignantMatricule = trim((string)($data['enseignant_matricule'] ?? ''));
            $commentaire = isset($data['commentaire']) && $data['commentaire'] !== ''
                ? trim((string)$data['commentaire'])
                : null;

            $rapport = $rapportId > 0 ? $this->rapportRepository->find($rapportId) : null;
            $enseignant = $enseignantMatricule !== '' ? $this->enseignantRepository->findByMatricule($enseignantMatricule) : null;
            $affecteur = $this->getUser();

            if ($rapport === null || $enseignant === null || $affecteur === null) {
                $this->addFlash('error', 'Paramètres invalides');
                return $this->redirect('/admin/commission/assignation');
            }

            $this->affectationService->assignDirecteurMemoire($rapport, $enseignant, $affecteur, $commentaire);

            $this->addFlash('success', 'Encadrant assigné avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'assignation : ' . $e->getMessage());
        }

        return $this->redirect('/admin/commission/assignation');
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
