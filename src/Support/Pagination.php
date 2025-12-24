<?php

declare(strict_types=1);

namespace Src\Support;

/**
 * Gestionnaire de pagination
 * 
 * Classe utilitaire pour la pagination des résultats.
 */
class Pagination
{
    /**
     * Nombre d'éléments par page par défaut
     */
    public const DEFAULT_PER_PAGE = 20;

    /**
     * Nombre maximum d'éléments par page
     */
    public const MAX_PER_PAGE = 100;

    private int $total;
    private int $perPage;
    private int $currentPage;
    private int $lastPage;

    /**
     * @var array<mixed>
     */
    private array $items = [];

    /**
     * URL de base pour la génération des liens
     */
    private string $baseUrl = '';

    /**
     * Paramètres de query string à conserver
     *
     * @var array<string, mixed>
     */
    private array $queryParams = [];

    /**
     * Constructeur
     *
     * @param int $total Nombre total d'éléments
     * @param int $currentPage Page courante (commence à 1)
     * @param int $perPage Éléments par page
     */
    public function __construct(int $total, int $currentPage = 1, int $perPage = self::DEFAULT_PER_PAGE)
    {
        $this->total = max(0, $total);
        $this->perPage = min(max(1, $perPage), self::MAX_PER_PAGE);
        $this->lastPage = max(1, (int) ceil($this->total / $this->perPage));
        $this->currentPage = max(1, min($currentPage, $this->lastPage));
    }

    /**
     * Factory depuis une requête HTTP
     *
     * @param int $total Nombre total d'éléments
     * @param int|null $perPage Éléments par page (ou null pour défaut)
     */
    public static function fromRequest(int $total, ?int $perPage = null): self
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = $perPage ?? (int) ($_GET['per_page'] ?? self::DEFAULT_PER_PAGE);
        
        return new self($total, $page, $perPage);
    }

    /**
     * Définit les éléments de la page courante
     *
     * @param array<mixed> $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Retourne les éléments de la page courante
     *
     * @return array<mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Définit l'URL de base pour les liens
     */
    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Définit les paramètres de query string à conserver
     *
     * @param array<string, mixed> $params
     */
    public function setQueryParams(array $params): self
    {
        unset($params['page']); // On gère page nous-mêmes
        $this->queryParams = $params;
        return $this;
    }

    /**
     * Retourne le nombre total d'éléments
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Retourne le nombre d'éléments par page
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Retourne la page courante
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Retourne la dernière page
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * Retourne l'offset pour la requête SQL
     */
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Retourne l'index du premier élément de la page
     */
    public function getFrom(): int
    {
        return $this->total > 0 ? $this->getOffset() + 1 : 0;
    }

    /**
     * Retourne l'index du dernier élément de la page
     */
    public function getTo(): int
    {
        return min($this->currentPage * $this->perPage, $this->total);
    }

    /**
     * Vérifie s'il y a une page précédente
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Vérifie s'il y a une page suivante
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    /**
     * Vérifie s'il y a des pages (plus d'une page)
     */
    public function hasPages(): bool
    {
        return $this->lastPage > 1;
    }

    /**
     * Vérifie si on est sur la première page
     */
    public function onFirstPage(): bool
    {
        return $this->currentPage === 1;
    }

    /**
     * Vérifie si on est sur la dernière page
     */
    public function onLastPage(): bool
    {
        return $this->currentPage === $this->lastPage;
    }

    /**
     * Génère l'URL pour une page donnée
     */
    public function url(int $page): string
    {
        $params = array_merge($this->queryParams, ['page' => $page]);
        $query = http_build_query($params);
        
        return $this->baseUrl . ($query ? '?' . $query : '');
    }

    /**
     * Retourne l'URL de la page précédente
     */
    public function previousPageUrl(): ?string
    {
        return $this->hasPreviousPage() ? $this->url($this->currentPage - 1) : null;
    }

    /**
     * Retourne l'URL de la page suivante
     */
    public function nextPageUrl(): ?string
    {
        return $this->hasNextPage() ? $this->url($this->currentPage + 1) : null;
    }

    /**
     * Retourne l'URL de la première page
     */
    public function firstPageUrl(): string
    {
        return $this->url(1);
    }

    /**
     * Retourne l'URL de la dernière page
     */
    public function lastPageUrl(): string
    {
        return $this->url($this->lastPage);
    }

    /**
     * Génère les numéros de page pour l'affichage avec fenêtre glissante
     *
     * @param int $onEachSide Nombre de pages de chaque côté de la page courante
     * @return array<int>
     */
    public function getPageRange(int $onEachSide = 3): array
    {
        $start = max(1, $this->currentPage - $onEachSide);
        $end = min($this->lastPage, $this->currentPage + $onEachSide);

        // Ajuster si on est proche des extrémités
        if ($start === 1) {
            $end = min($this->lastPage, 1 + ($onEachSide * 2));
        }
        if ($end === $this->lastPage) {
            $start = max(1, $this->lastPage - ($onEachSide * 2));
        }

        return range($start, $end);
    }

    /**
     * Génère les éléments de pagination avec ellipses
     *
     * @param int $onEachSide Nombre de pages de chaque côté
     * @return array<array{type: string, page?: int, url?: string, current?: bool}>
     */
    public function getElements(int $onEachSide = 2): array
    {
        $elements = [];

        // Bouton précédent
        $elements[] = [
            'type' => 'prev',
            'page' => $this->currentPage - 1,
            'url' => $this->previousPageUrl(),
            'disabled' => !$this->hasPreviousPage(),
        ];

        // Première page
        $elements[] = [
            'type' => 'page',
            'page' => 1,
            'url' => $this->url(1),
            'current' => $this->currentPage === 1,
        ];

        // Ellipse de début
        $start = max(2, $this->currentPage - $onEachSide);
        if ($start > 2) {
            $elements[] = ['type' => 'ellipsis'];
        }

        // Pages centrales
        $end = min($this->lastPage - 1, $this->currentPage + $onEachSide);
        for ($i = $start; $i <= $end; $i++) {
            $elements[] = [
                'type' => 'page',
                'page' => $i,
                'url' => $this->url($i),
                'current' => $this->currentPage === $i,
            ];
        }

        // Ellipse de fin
        if ($end < $this->lastPage - 1) {
            $elements[] = ['type' => 'ellipsis'];
        }

        // Dernière page
        if ($this->lastPage > 1) {
            $elements[] = [
                'type' => 'page',
                'page' => $this->lastPage,
                'url' => $this->url($this->lastPage),
                'current' => $this->currentPage === $this->lastPage,
            ];
        }

        // Bouton suivant
        $elements[] = [
            'type' => 'next',
            'page' => $this->currentPage + 1,
            'url' => $this->nextPageUrl(),
            'disabled' => !$this->hasNextPage(),
        ];

        return $elements;
    }

    /**
     * Convertit en tableau pour API/JSON
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'has_more' => $this->hasNextPage(),
        ];
    }

    /**
     * Applique la pagination à une requête SQL (retourne LIMIT et OFFSET)
     *
     * @return array{limit: int, offset: int}
     */
    public function getLimitOffset(): array
    {
        return [
            'limit' => $this->perPage,
            'offset' => $this->getOffset(),
        ];
    }

    /**
     * Génère le HTML Bootstrap pour la pagination
     */
    public function renderBootstrap(): string
    {
        if (!$this->hasPages()) {
            return '';
        }

        $html = '<nav aria-label="Pagination"><ul class="pagination">';

        foreach ($this->getElements() as $element) {
            $type = $element['type'];

            if ($type === 'prev') {
                $disabled = $element['disabled'] ? ' disabled' : '';
                $url = $element['url'] ?? '#';
                $html .= "<li class=\"page-item{$disabled}\">";
                $html .= "<a class=\"page-link\" href=\"{$url}\" aria-label=\"Précédent\">&laquo;</a>";
                $html .= '</li>';
            } elseif ($type === 'next') {
                $disabled = $element['disabled'] ? ' disabled' : '';
                $url = $element['url'] ?? '#';
                $html .= "<li class=\"page-item{$disabled}\">";
                $html .= "<a class=\"page-link\" href=\"{$url}\" aria-label=\"Suivant\">&raquo;</a>";
                $html .= '</li>';
            } elseif ($type === 'ellipsis') {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            } elseif ($type === 'page') {
                $active = ($element['current'] ?? false) ? ' active' : '';
                $page = $element['page'] ?? '';
                $url = $element['url'] ?? '#';
                $html .= "<li class=\"page-item{$active}\">";
                $html .= "<a class=\"page-link\" href=\"{$url}\">{$page}</a>";
                $html .= '</li>';
            }
        }

        $html .= '</ul></nav>';

        return $html;
    }
}
