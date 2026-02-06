<?php
declare(strict_types=1);

namespace App\Service\System;

use App\Entity\System\CategorieFonctionnalite;
use App\Entity\System\Fonctionnalite;
use App\Repository\System\FonctionnaliteRepository;

class MenuService
{
    private const CACHE_KEY = 'menu_cache';

    private FonctionnaliteRepository $repository;
    private CacheService $cache;

    public function __construct(FonctionnaliteRepository $repository, CacheService $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function getMenuForUser(int $userId, array $permissions): array
    {
        if ($permissions === []) {
            return [];
        }

        $cache = $this->getCache();
        $key = 'user_' . $userId . '_' . hash('sha256', serialize($permissions));

        if (isset($cache['users'][$key]) && is_array($cache['users'][$key])) {
            return $cache['users'][$key];
        }

        $fonctionnalites = $this->repository->findAll();
        $permissionIndex = $this->normalizePermissions($permissions);
        $allowed = [];
        $byId = [];

        foreach ($fonctionnalites as $fonctionnalite) {
            if (!$fonctionnalite instanceof Fonctionnalite || !$fonctionnalite->isActif()) {
                continue;
            }

            $id = $fonctionnalite->getIdFonctionnalite();

            if ($id === null) {
                continue;
            }

            $byId[$id] = $fonctionnalite;

            if ($this->hasAccess($fonctionnalite, $permissionIndex)) {
                $allowed[$id] = true;
            }
        }

        foreach (array_keys($allowed) as $id) {
            $current = $byId[$id] ?? null;
            while ($current instanceof Fonctionnalite && $current->getPageParente() !== null) {
                $parent = $current->getPageParente();
                $parentId = $parent?->getIdFonctionnalite();
                if ($parentId !== null) {
                    $allowed[$parentId] = true;
                }
                $current = $parent;
            }
        }

        $filtered = [];

        foreach ($allowed as $id => $_) {
            if (isset($byId[$id])) {
                $filtered[] = $byId[$id];
            }
        }

        $menu = $this->buildMenuTree($filtered);
        $cache['users'][$key] = $menu;
        $this->setCache($cache);

        return $menu;
    }

    public function getCategoriesWithFonctionnalites(): array
    {
        $cache = $this->getCache();

        if (isset($cache['categories']) && is_array($cache['categories'])) {
            return $cache['categories'];
        }

        $fonctionnalites = $this->repository->findAll();
        $categories = [];

        foreach ($fonctionnalites as $fonctionnalite) {
            if (!$fonctionnalite instanceof Fonctionnalite || !$fonctionnalite->isActif()) {
                continue;
            }

            $categorie = $fonctionnalite->getCategorie();

            if (!$categorie instanceof CategorieFonctionnalite || !$categorie->isActif()) {
                continue;
            }

            $categoryId = $categorie->getIdCategorie();

            if ($categoryId === null) {
                continue;
            }

            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'id' => $categoryId,
                    'code' => $categorie->getCodeCategorie(),
                    'label' => $categorie->getLibelleCategorie(),
                    'description' => $categorie->getDescriptionCategorie(),
                    'icon' => $categorie->getIconeCategorie(),
                    'ordre' => $categorie->getOrdreAffichage(),
                    'items' => [],
                ];
            }

            $categories[$categoryId]['items'][] = $fonctionnalite;
        }

        $result = [];

        foreach ($categories as $category) {
            $items = $category['items'];
            $category['items'] = $this->buildMenuTree($items);
            $result[] = $category;
        }

        usort($result, fn (array $a, array $b) => ($a['ordre'] ?? 0) <=> ($b['ordre'] ?? 0));

        $cache['categories'] = $result;
        $this->setCache($cache);

        return $result;
    }

    public function buildMenuTree(array $fonctionnalites): array
    {
        $items = [];
        $tree = [];

        foreach ($fonctionnalites as $fonctionnalite) {
            if (!$fonctionnalite instanceof Fonctionnalite) {
                continue;
            }

            $id = $fonctionnalite->getIdFonctionnalite();

            if ($id === null || !$fonctionnalite->isActif()) {
                continue;
            }

            $items[$id] = $this->formatFonctionnalite($fonctionnalite);
        }

        foreach ($items as $id => $item) {
            $parentId = $item['parent_id'];

            if ($parentId !== null && isset($items[$parentId])) {
                $items[$parentId]['children'][] = $item;
            } else {
                $tree[] = $item;
            }
        }

        $sortTree = function (array &$nodes) use (&$sortTree): void {
            usort($nodes, fn (array $a, array $b) => ($a['ordre'] ?? 0) <=> ($b['ordre'] ?? 0));
            foreach ($nodes as &$node) {
                if (!empty($node['children'])) {
                    $sortTree($node['children']);
                }
            }
        };

        $sortTree($tree);

        return $tree;
    }

    public function clearCache(): void
    {
        $this->cache->delete(self::CACHE_KEY);
    }

    private function formatFonctionnalite(Fonctionnalite $fonctionnalite): array
    {
        $categorie = $fonctionnalite->getCategorie();

        return [
            'id' => $fonctionnalite->getIdFonctionnalite(),
            'code' => $fonctionnalite->getCodeFonctionnalite(),
            'label' => $fonctionnalite->getLibelleFonctionnalite(),
            'label_court' => $fonctionnalite->getLabelCourt(),
            'description' => $fonctionnalite->getDescriptionFonctionnalite(),
            'url' => $fonctionnalite->getUrlFonctionnalite(),
            'icon' => $fonctionnalite->getIconeFonctionnalite(),
            'ordre' => $fonctionnalite->getOrdreAffichage(),
            'est_sous_page' => $fonctionnalite->isEstSousPage(),
            'parent_id' => $fonctionnalite->getPageParente()?->getIdFonctionnalite(),
            'categorie' => [
                'id' => $categorie->getIdCategorie(),
                'code' => $categorie->getCodeCategorie(),
                'label' => $categorie->getLibelleCategorie(),
                'icon' => $categorie->getIconeCategorie(),
                'ordre' => $categorie->getOrdreAffichage(),
            ],
            'children' => [],
        ];
    }

    private function normalizePermissions(array $permissions): array
    {
        $ids = [];
        $codes = [];

        foreach ($permissions as $permission) {
            if (is_int($permission)) {
                $ids[$permission] = true;
                continue;
            }

            if (is_string($permission)) {
                $codes[$permission] = true;
                continue;
            }

            if (is_array($permission)) {
                if (isset($permission['id'], $permission['code'])) {
                    $ids[(int) $permission['id']] = true;
                    $codes[(string) $permission['code']] = true;
                    continue;
                }
                if (isset($permission['id_fonctionnalite'])) {
                    $ids[(int) $permission['id_fonctionnalite']] = true;
                }
                if (isset($permission['code_fonctionnalite'])) {
                    $codes[(string) $permission['code_fonctionnalite']] = true;
                }
                continue;
            }

            if (is_object($permission)) {
                if (method_exists($permission, 'getFonctionnalite')) {
                    $fonctionnalite = $permission->getFonctionnalite();
                    if ($fonctionnalite instanceof Fonctionnalite) {
                        $id = $fonctionnalite->getIdFonctionnalite();
                        if ($id !== null) {
                            $ids[$id] = true;
                        }
                        $codes[$fonctionnalite->getCodeFonctionnalite()] = true;
                    }
                }
            }
        }

        return [
            'ids' => $ids,
            'codes' => $codes,
        ];
    }

    private function hasAccess(Fonctionnalite $fonctionnalite, array $permissionIndex): bool
    {
        $id = $fonctionnalite->getIdFonctionnalite();
        $code = $fonctionnalite->getCodeFonctionnalite();

        if (isset($permissionIndex['codes']['*'])) {
            return true;
        }

        if ($id !== null && isset($permissionIndex['ids'][$id])) {
            return true;
        }

        return isset($permissionIndex['codes'][$code]);
    }

    private function getCache(): array
    {
        $cache = $this->cache->get(self::CACHE_KEY, []);

        return is_array($cache) ? $cache : [];
    }

    private function setCache(array $cache): void
    {
        $this->cache->set(self::CACHE_KEY, $cache, 3600);
    }
}
