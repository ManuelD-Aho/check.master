<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Classe PaginatedResponse
 * 
 * Réponse paginée pour les listes de résultats.
 * Inclut automatiquement les métadonnées de pagination.
 */
class PaginatedResponse extends ApiResponse
{
    /**
     * Crée une réponse paginée
     *
     * @param array<mixed> $items Les éléments de la page courante
     * @param int $total Nombre total d'éléments
     * @param int $page Page courante (commence à 1)
     * @param int $perPage Éléments par page
     * @param string $message Message optionnel
     */
    public function __construct(
        array $items,
        int $total,
        int $page = 1,
        int $perPage = 20,
        string $message = ''
    ) {
        $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
        $page = max(1, $page);

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $lastPage,
            'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
            'to' => min($page * $perPage, $total),
            'has_more' => $page < $lastPage,
            'has_previous' => $page > 1,
        ];

        parent::__construct([
            'success' => true,
            'message' => $message,
            'data' => $items,
            'meta' => ['pagination' => $pagination],
        ], 200);
    }

    /**
     * Factory pour créer une réponse paginée depuis un tableau complet
     *
     * @param array<mixed> $allItems Tous les éléments
     * @param int $page Page courante
     * @param int $perPage Éléments par page
     */
    public static function fromArray(array $allItems, int $page = 1, int $perPage = 20): self
    {
        $total = count($allItems);
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        
        $items = array_slice($allItems, $offset, $perPage);

        return new self($items, $total, $page, $perPage);
    }

    /**
     * Factory pour créer une réponse paginée depuis une requête SQL
     *
     * @param array<mixed> $items Éléments de la page
     * @param int $totalCount Count total depuis la BDD
     * @param int $page Page courante
     * @param int $perPage Éléments par page
     */
    public static function fromQuery(array $items, int $totalCount, int $page = 1, int $perPage = 20): self
    {
        return new self($items, $totalCount, $page, $perPage);
    }

    /**
     * Ajoute des liens de navigation
     *
     * @param string $baseUrl URL de base pour la pagination
     * @param array<string, mixed> $queryParams Paramètres de requête à conserver
     */
    public function withNavigationLinks(string $baseUrl, array $queryParams = []): self
    {
        $pagination = $this->body['meta']['pagination'] ?? [];
        $currentPage = $pagination['current_page'] ?? 1;
        $lastPage = $pagination['total_pages'] ?? 1;

        $buildUrl = function (int $page) use ($baseUrl, $queryParams): string {
            $queryParams['page'] = $page;
            return $baseUrl . '?' . http_build_query($queryParams);
        };

        $links = [
            'first' => $buildUrl(1),
            'last' => $buildUrl($lastPage),
        ];

        if ($currentPage > 1) {
            $links['prev'] = $buildUrl($currentPage - 1);
        }

        if ($currentPage < $lastPage) {
            $links['next'] = $buildUrl($currentPage + 1);
        }

        $this->body['links'] = $links;
        $this->updateContent();

        return $this;
    }

    /**
     * Ajoute un curseur pour la pagination basée sur le curseur
     *
     * @param mixed $nextCursor Curseur pour la page suivante
     * @param mixed $prevCursor Curseur pour la page précédente
     */
    public function withCursor(mixed $nextCursor = null, mixed $prevCursor = null): self
    {
        $this->body['meta']['cursor'] = [
            'next' => $nextCursor,
            'prev' => $prevCursor,
        ];
        $this->updateContent();

        return $this;
    }

    /**
     * Retourne les informations de pagination
     *
     * @return array<string, mixed>
     */
    public function getPagination(): array
    {
        return $this->body['meta']['pagination'] ?? [];
    }

    /**
     * Vérifie s'il y a une page suivante
     */
    public function hasNextPage(): bool
    {
        return ($this->body['meta']['pagination']['has_more'] ?? false) === true;
    }

    /**
     * Vérifie s'il y a une page précédente
     */
    public function hasPreviousPage(): bool
    {
        return ($this->body['meta']['pagination']['has_previous'] ?? false) === true;
    }

    /**
     * Retourne le nombre total d'éléments
     */
    public function getTotal(): int
    {
        return (int) ($this->body['meta']['pagination']['total'] ?? 0);
    }

    /**
     * Retourne le nombre total de pages
     */
    public function getTotalPages(): int
    {
        return (int) ($this->body['meta']['pagination']['total_pages'] ?? 0);
    }

    /**
     * Retourne la page courante
     */
    public function getCurrentPage(): int
    {
        return (int) ($this->body['meta']['pagination']['current_page'] ?? 1);
    }
}
