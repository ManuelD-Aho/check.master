<?php
declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User\Permission;
use App\Entity\User\RouteAction;
use App\Entity\User\RouteHttpMethod;
use App\Entity\User\Utilisateur;
use App\Repository\User\PermissionRepository;
use App\Service\System\AuditService;
use App\Service\System\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class AuthorizationService
{
    private PermissionRepository $permissionRepository;
    private CacheService $cacheService;
    private AuditService $auditService;

    public function __construct(
        PermissionRepository $permissionRepository,
        CacheService $cacheService,
        AuditService $auditService
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->cacheService = $cacheService;
        $this->auditService = $auditService;
    }

    public function can(int $userId, string $fonctionnaliteCode, string $action): bool
    {
        try {
            $permissions = $this->getPermissionsForUser($userId);
            $action = strtolower($action);

            foreach ($permissions as $permission) {
                if ($permission['fonctionnalite_code'] !== $fonctionnaliteCode) {
                    continue;
                }

                return $this->actionAllowed($permission, $action);
            }
        } catch (Throwable) {
        }

        return false;
    }

    public function canAccess(int $userId, string $routePattern, string $httpMethod): bool
    {
        try {
            $permissions = $this->getPermissionsForUser($userId);
            $routes = $this->getRouteActions($httpMethod);

            foreach ($routes as $route) {
                if (!$this->matchRoutePattern($route['route_pattern'], $routePattern)) {
                    continue;
                }

                if ($this->hasPermission($permissions, $route['fonctionnalite_code'], $route['action'])) {
                    return true;
                }
            }
        } catch (Throwable) {
        }

        $this->recordAudit($userId, $routePattern, $httpMethod);

        return false;
    }

    public function getPermissionsForUser(int $userId): array
    {
        $cacheKey = $this->getCacheKey($userId);

        $cached = $this->cacheGet($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $permissions = $this->fetchPermissionsFromDb($userId);
        $this->cacheSet($cacheKey, $permissions);

        return $permissions;
    }

    public function hasRole(int $userId, string $roleCode): bool
    {
        $roleCode = strtolower($roleCode);

        foreach ($this->getPermissionsForUser($userId) as $permission) {
            if (strtolower($permission['groupe_code']) === $roleCode) {
                return true;
            }
        }

        $groupCode = $this->fetchUserGroupCode($userId);

        return $groupCode !== null && strtolower($groupCode) === $roleCode;
    }

    public function clearUserPermissionCache(int $userId): void
    {
        $this->cacheDelete($this->getCacheKey($userId));
    }

    public function checkRoutePermission(Utilisateur $user, string $path, string $method): bool
    {
        $userId = $user->getIdUtilisateur();
        if ($userId === null) {
            return false;
        }

        return $this->canAccess($userId, $path, $method);
    }

    private function fetchPermissionsFromDb(int $userId): array
    {
        $entityManager = $this->getEntityManager();
        if ($entityManager === null) {
            return [];
        }

        $qb = $entityManager->createQueryBuilder();
        $qb->select('p', 'f', 'g')
            ->from(Permission::class, 'p')
            ->join('p.groupeUtilisateur', 'g')
            ->join('g.utilisateurs', 'u')
            ->join('p.fonctionnalite', 'f')
            ->where('u.idUtilisateur = :userId')
            ->setParameter('userId', $userId);

        $results = $qb->getQuery()->getResult();
        $permissions = [];

        foreach ($results as $permission) {
            if (!$permission instanceof Permission) {
                continue;
            }

            $fonctionnalite = $permission->getFonctionnalite();
            $groupe = $permission->getGroupeUtilisateur();

            $permissions[] = [
                'fonctionnalite_code' => $fonctionnalite->getCodeFonctionnalite(),
                'peut_voir' => $permission->isPeutVoir(),
                'peut_creer' => $permission->isPeutCreer(),
                'peut_modifier' => $permission->isPeutModifier(),
                'peut_supprimer' => $permission->isPeutSupprimer(),
                'groupe_code' => $groupe->getCodeGroupe()
            ];
        }

        return $permissions;
    }

    private function fetchUserGroupCode(int $userId): ?string
    {
        $entityManager = $this->getEntityManager();
        if ($entityManager === null) {
            return null;
        }

        $qb = $entityManager->createQueryBuilder();
        $qb->select('g.codeGroupe')
            ->from(Utilisateur::class, 'u')
            ->join('u.groupeUtilisateur', 'g')
            ->where('u.idUtilisateur = :userId')
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getOneOrNullResult();

        return is_array($result) && isset($result['codeGroupe']) ? (string)$result['codeGroupe'] : null;
    }

    private function getRouteActions(string $httpMethod): array
    {
        $entityManager = $this->getEntityManager();
        if ($entityManager === null) {
            return [];
        }

        try {
            $methodEnum = RouteHttpMethod::from(strtoupper($httpMethod));
        } catch (Throwable) {
            return [];
        }

        $qb = $entityManager->createQueryBuilder();
        $qb->select('r', 'f')
            ->from(RouteAction::class, 'r')
            ->join('r.fonctionnalite', 'f')
            ->where('r.httpMethod = :method')
            ->andWhere('r.actif = true')
            ->setParameter('method', $methodEnum);

        $results = $qb->getQuery()->getResult();
        $routes = [];

        foreach ($results as $route) {
            if (!$route instanceof RouteAction) {
                continue;
            }

            $fonctionnalite = $route->getFonctionnalite();

            $routes[] = [
                'route_pattern' => $route->getRoutePattern(),
                'fonctionnalite_code' => $fonctionnalite->getCodeFonctionnalite(),
                'action' => $route->getActionCrud()->value
            ];
        }

        return $routes;
    }

    private function hasPermission(array $permissions, string $fonctionnaliteCode, string $action): bool
    {
        foreach ($permissions as $permission) {
            if ($permission['fonctionnalite_code'] !== $fonctionnaliteCode) {
                continue;
            }

            return $this->actionAllowed($permission, strtolower($action));
        }

        return false;
    }

    private function actionAllowed(array $permission, string $action): bool
    {
        return match ($action) {
            'voir', 'read', 'view' => (bool)($permission['peut_voir'] ?? false),
            'creer', 'create' => (bool)($permission['peut_creer'] ?? false),
            'modifier', 'update', 'edit' => (bool)($permission['peut_modifier'] ?? false),
            'supprimer', 'delete' => (bool)($permission['peut_supprimer'] ?? false),
            default => false
        };
    }

    private function matchRoutePattern(string $pattern, string $path): bool
    {
        $regex = preg_replace('#\{[^/]+\}#', '[^/]+', $pattern);
        $regex = str_replace('*', '.*', $regex ?? $pattern);
        $regex = '#^' . $regex . '$#';

        return (bool)preg_match($regex, $path);
    }

    private function getEntityManager(): ?EntityManagerInterface
    {
        if (method_exists($this->permissionRepository, 'getEntityManager')) {
            return $this->permissionRepository->getEntityManager();
        }

        try {
            $reflection = new \ReflectionClass($this->permissionRepository);
            if ($reflection->hasProperty('entityManager')) {
                $property = $reflection->getProperty('entityManager');
                $property->setAccessible(true);
                $value = $property->getValue($this->permissionRepository);
                if ($value instanceof EntityManagerInterface) {
                    return $value;
                }
            }
        } catch (Throwable) {
        }

        return null;
    }

    private function cacheGet(string $key): mixed
    {
        try {
            if (method_exists($this->cacheService, 'get')) {
                return $this->cacheService->get($key);
            }
        } catch (Throwable) {
        }

        return null;
    }

    private function cacheSet(string $key, array $value): void
    {
        try {
            if (method_exists($this->cacheService, 'set')) {
                $this->cacheService->set($key, $value);
            }
        } catch (Throwable) {
        }

    }

    private function cacheDelete(string $key): void
    {
        try {
            if (method_exists($this->cacheService, 'delete')) {
                $this->cacheService->delete($key);
            }
        } catch (Throwable) {
        }

    }

    private function getCacheKey(int $userId): string
    {
        return 'auth.permissions.user.' . $userId;
    }

    private function recordAudit(int $userId, string $path, string $method): void
    {
        try {
            $details = 'path=' . $path . ',method=' . $method;
            if (method_exists($this->auditService, 'log')) {
                $this->auditService->log('auth.access_denied', 'echec', $userId, null, null, null, null, $details);
            }
        } catch (Throwable) {
        }
    }
}
