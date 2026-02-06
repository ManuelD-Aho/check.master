<?php

declare(strict_types=1);

namespace App\Controller\Encadreur;

use App\Controller\AbstractController;
use App\Entity\Soutenance\AptitudeSoutenance;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Soutenance\AptitudeSoutenanceRepository;
use App\Repository\Student\EtudiantRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AptitudeController extends AbstractController
{
    private AptitudeSoutenanceRepository $aptitudeRepository;
    private EntityManagerInterface $entityManager;
    private EtudiantRepository $etudiantRepository;
    private AnneeAcademiqueRepository $anneeAcademiqueRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        AptitudeSoutenanceRepository $aptitudeRepository,
        EntityManagerInterface $entityManager,
        EtudiantRepository $etudiantRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->aptitudeRepository = $aptitudeRepository;
        $this->entityManager = $entityManager;
        $this->etudiantRepository = $etudiantRepository;
        $this->anneeAcademiqueRepository = $anneeAcademiqueRepository;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $aptitudes = $this->aptitudeRepository->findAll();

        return $this->render('encadreur/aptitude/index', ['aptitudes' => $aptitudes]);
    }

    public function form(ServerRequestInterface $request): ResponseInterface
    {
        $matricule = $request->getAttribute('matricule');

        return $this->render('encadreur/aptitude/form', [
            'matricule' => $matricule,
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function validate(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/encadreur/aptitude');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/encadreur/aptitude');
        }

        $matricule = trim((string)($data['matricule'] ?? ''));
        if ($matricule === '') {
            $this->addFlash('error', 'Matricule étudiant requis');
            return $this->redirect('/encadreur/aptitude');
        }

        $etudiant = $this->etudiantRepository->findByMatricule($matricule);
        if ($etudiant === null) {
            $this->addFlash('error', 'Étudiant introuvable');
            return $this->redirect('/encadreur/aptitude');
        }

        $anneeId = (int)($data['annee_id'] ?? 0);
        if ($anneeId <= 0) {
            $this->addFlash('error', 'Année académique requise');
            return $this->redirect('/encadreur/aptitude');
        }

        $annee = $this->anneeAcademiqueRepository->find($anneeId);
        if ($annee === null) {
            $this->addFlash('error', 'Année académique introuvable');
            return $this->redirect('/encadreur/aptitude');
        }

        $user = $this->getUser();
        if ($user === null) {
            $this->addFlash('error', 'Utilisateur non connecté');
            return $this->redirect('/encadreur/aptitude');
        }

        $encadreur = $user->getEnseignant();
        if ($encadreur === null) {
            $this->addFlash('error', 'Encadreur introuvable');
            return $this->redirect('/encadreur/aptitude');
        }

        try {
            $aptitude = new AptitudeSoutenance();
            $aptitude->setEtudiant($etudiant);
            $aptitude->setAnneeAcademique($annee);
            $aptitude->setEncadreur($encadreur);
            $aptitude->setEstApte(filter_var($data['est_apte'] ?? false, FILTER_VALIDATE_BOOLEAN));
            $aptitude->setCommentaire(trim((string)($data['motif'] ?? '')));
            $aptitude->setDateValidation(new DateTime());
            $aptitude->setDateCreation(new DateTime());

            $this->entityManager->persist($aptitude);
            $this->entityManager->flush();

            $this->addFlash('success', 'Aptitude validée avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la validation : ' . $e->getMessage());
        }

        return $this->redirect('/encadreur/aptitude');
    }
}
