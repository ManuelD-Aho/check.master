<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\User\UtilisateurStatut;
use App\Repository\User\GroupeUtilisateurRepository;
use App\Repository\User\TypeUtilisateurRepository;
use App\Repository\User\UtilisateurRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UtilisateurController extends AbstractController
{
    private UtilisateurRepository $utilisateurRepository;
    private GroupeUtilisateurRepository $groupeRepository;
    private TypeUtilisateurRepository $typeRepository;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        UtilisateurRepository $utilisateurRepository,
        GroupeUtilisateurRepository $groupeRepository,
        TypeUtilisateurRepository $typeRepository
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->utilisateurRepository = $utilisateurRepository;
        $this->groupeRepository = $groupeRepository;
        $this->typeRepository = $typeRepository;
    }

    public function index(Request $request): Response
    {
        $utilisateurs = $this->utilisateurRepository->findAll();

        return $this->render('admin/utilisateur/index', [
            'utilisateurs' => $utilisateurs,
            'flashes' => $this->getFlashes(),
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $utilisateur = $id !== null ? $this->utilisateurRepository->find((int) $id) : null;

        return $this->render('admin/utilisateur/show', [
            'utilisateur' => $utilisateur,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->render('admin/utilisateur/create', [
            'groupes' => $this->groupeRepository->findAll(),
            'types' => $this->typeRepository->findAll(),
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function store(Request $request): Response
    {
        $this->addFlash('success', 'Utilisateur enregistre');

        return $this->redirect('/admin/utilisateurs');
    }

    public function edit(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $utilisateur = $id !== null ? $this->utilisateurRepository->find((int) $id) : null;

        return $this->render('admin/utilisateur/edit', [
            'utilisateur' => $utilisateur,
            'groupes' => $this->groupeRepository->findAll(),
            'types' => $this->typeRepository->findAll(),
            'csrf' => $this->getCsrfToken(),
        ]);
    }

    public function update(Request $request): Response
    {
        $this->addFlash('success', 'Utilisateur mis a jour');

        return $this->redirect('/admin/utilisateurs');
    }

    public function delete(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $utilisateur = $id !== null ? $this->utilisateurRepository->find((int) $id) : null;

        if ($utilisateur !== null) {
            $this->utilisateurRepository->remove($utilisateur);
            $this->addFlash('success', 'Utilisateur supprime');
        } else {
            $this->addFlash('error', 'Utilisateur introuvable');
        }

        return $this->redirect('/admin/utilisateurs');
    }

    public function toggleStatus(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $utilisateur = $id !== null ? $this->utilisateurRepository->find((int) $id) : null;

        if ($utilisateur !== null) {
            $current = $utilisateur->getStatutUtilisateur();
            $next = $current === UtilisateurStatut::Actif ? UtilisateurStatut::Inactif : UtilisateurStatut::Actif;
            $utilisateur->setStatutUtilisateur($next);
            $this->utilisateurRepository->save($utilisateur);
            $this->addFlash('success', 'Statut mis a jour');
        } else {
            $this->addFlash('error', 'Utilisateur introuvable');
        }

        return $this->redirect('/admin/utilisateurs');
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
