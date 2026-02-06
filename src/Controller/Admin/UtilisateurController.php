<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\User\Utilisateur;
use App\Entity\User\UtilisateurStatut;
use App\Repository\User\GroupeUtilisateurRepository;
use App\Repository\User\TypeUtilisateurRepository;
use App\Repository\User\UtilisateurRepository;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Auth\PasswordService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UtilisateurController extends AbstractController
{
    private UtilisateurRepository $utilisateurRepository;
    private GroupeUtilisateurRepository $groupeRepository;
    private TypeUtilisateurRepository $typeRepository;
    private PasswordService $passwordService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        UtilisateurRepository $utilisateurRepository,
        GroupeUtilisateurRepository $groupeRepository,
        TypeUtilisateurRepository $typeRepository,
        PasswordService $passwordService,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->utilisateurRepository = $utilisateurRepository;
        $this->groupeRepository = $groupeRepository;
        $this->typeRepository = $typeRepository;
        $this->passwordService = $passwordService;
        $this->entityManager = $entityManager;
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
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect('/admin/utilisateurs');
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect('/admin/utilisateurs/nouveau');
        }

        try {
            $utilisateur = new Utilisateur();

            $utilisateur->setLoginUtilisateur(trim((string)($data['login'] ?? '')));
            $utilisateur->setEmailUtilisateur(trim((string)($data['email'] ?? '')));
            $utilisateur->setNomComplet(trim((string)($data['nom_complet'] ?? '')));

            $hashedPassword = $this->passwordService->hash((string)($data['password'] ?? ''));
            $utilisateur->setMotDePasseHash($hashedPassword);

            $typeId = (int)($data['type_utilisateur'] ?? 0);
            if ($typeId > 0) {
                $type = $this->typeRepository->find($typeId);
                if ($type !== null) {
                    $utilisateur->setTypeUtilisateur($type);
                }
            }

            $groupeId = (int)($data['groupe_utilisateur'] ?? 0);
            if ($groupeId > 0) {
                $groupe = $this->groupeRepository->find($groupeId);
                if ($groupe !== null) {
                    $utilisateur->setGroupeUtilisateur($groupe);
                }
            }

            $utilisateur->setStatutUtilisateur(UtilisateurStatut::Actif);
            $utilisateur->setPremiereConnexion(true);
            $now = new DateTimeImmutable();
            $utilisateur->setDateCreation($now);
            $utilisateur->setDateModification($now);

            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirect('/admin/utilisateurs');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création : ' . $e->getMessage());
            return $this->redirect('/admin/utilisateurs/nouveau');
        }
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
        $id = $this->getRouteParam($request, 'id');

        if ($id === null) {
            $this->addFlash('error', 'Identifiant invalide');
            return $this->redirect('/admin/utilisateurs');
        }

        $utilisateur = $this->utilisateurRepository->find((int) $id);

        if ($utilisateur === null) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirect('/admin/utilisateurs');
        }

        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $this->addFlash('error', 'Données invalides');
            return $this->redirect("/admin/utilisateurs/{$id}/modifier");
        }

        $csrfToken = (string)($data['_csrf_token'] ?? '');
        if (!$this->validateCsrf($csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirect("/admin/utilisateurs/{$id}/modifier");
        }

        try {
            $utilisateur->setEmailUtilisateur(trim((string)($data['email'] ?? '')));
            $utilisateur->setNomComplet(trim((string)($data['nom_complet'] ?? '')));

            $typeId = (int)($data['type_utilisateur'] ?? 0);
            if ($typeId > 0) {
                $type = $this->typeRepository->find($typeId);
                if ($type !== null) {
                    $utilisateur->setTypeUtilisateur($type);
                }
            }

            $groupeId = (int)($data['groupe_utilisateur'] ?? 0);
            if ($groupeId > 0) {
                $groupe = $this->groupeRepository->find($groupeId);
                if ($groupe !== null) {
                    $utilisateur->setGroupeUtilisateur($groupe);
                }
            }

            $utilisateur->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Utilisateur mis à jour avec succès');
            return $this->redirect('/admin/utilisateurs');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
            return $this->redirect("/admin/utilisateurs/{$id}/modifier");
        }
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

    public function unblock(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $utilisateur = $id !== null ? $this->utilisateurRepository->find((int) $id) : null;

        if ($utilisateur !== null) {
            $utilisateur->setBlocked(false);
            $this->utilisateurRepository->save($utilisateur);
            $this->addFlash('success', 'Utilisateur debloque');
        } else {
            $this->addFlash('error', 'Utilisateur introuvable');
        }

        return $this->redirect('/admin/utilisateurs');
    }

    public function resetPassword(Request $request): Response
    {
        $id = $this->getRouteParam($request, 'id');
        $utilisateur = $id !== null ? $this->utilisateurRepository->find((int) $id) : null;

        if ($utilisateur !== null) {
            $tempPassword = $this->passwordService->generateSecurePassword();
            $hashedPassword = $this->passwordService->hash($tempPassword);
            $utilisateur->setMotDePasseHash($hashedPassword);
            $utilisateur->setPremiereConnexion(true);
            $this->utilisateurRepository->save($utilisateur);
            $this->addFlash('success', 'Mot de passe réinitialisé. Nouveau mot de passe temporaire : ' . $tempPassword);
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
