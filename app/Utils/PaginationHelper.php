<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour la pagination
 * 
 * Utilitaires pour la pagination de résultats.
 */
class PaginationHelper
{
    /**
     * Nombre d'éléments par page par défaut
     */
    public const DEFAULT_PER_PAGE = 20;

    /**
     * Nombre maximum d'éléments par page
     */
    public const MAX_PER_PAGE = 100;

    /**
     * Nombre de liens de page à afficher de chaque côté
     */
    public const LINKS_EACH_SIDE = 3;

    /**
     * Calcule les informations de pagination
     *
     * @return array{
     *     total: int,
     *     per_page: int,
     *     current_page: int,
     *     last_page: int,
     *     from: int,
     *     to: int,
     *     has_more_pages: bool,
     *     has_previous: bool,
     *     offset: int
     * }
     */
    public static function calculate(int $total, int $perPage = self::DEFAULT_PER_PAGE, int $currentPage = 1): array
    {
        // Valider les paramètres
        $perPage = max(1, min($perPage, self::MAX_PER_PAGE));
        $currentPage = max(1, $currentPage);

        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($currentPage, $lastPage);

        $from = $total > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
        $to = min($currentPage * $perPage, $total);

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'from' => $from,
            'to' => $to,
            'has_more_pages' => $currentPage < $lastPage,
            'has_previous' => $currentPage > 1,
            'offset' => ($currentPage - 1) * $perPage,
        ];
    }

    /**
     * Génère les liens de pagination
     *
     * @return array<int|string>
     */
    public static function generateLinks(int $currentPage, int $lastPage, int $linksEachSide = self::LINKS_EACH_SIDE): array
    {
        $links = [];

        // Toujours afficher la première page
        $links[] = 1;

        // Calculer les bornes
        $start = max(2, $currentPage - $linksEachSide);
        $end = min($lastPage - 1, $currentPage + $linksEachSide);

        // Ajouter des points de suspension si nécessaire avant
        if ($start > 2) {
            $links[] = '...';
        }

        // Pages du milieu
        for ($i = $start; $i <= $end; $i++) {
            $links[] = $i;
        }

        // Ajouter des points de suspension si nécessaire après
        if ($end < $lastPage - 1) {
            $links[] = '...';
        }

        // Toujours afficher la dernière page si différente de 1
        if ($lastPage > 1) {
            $links[] = $lastPage;
        }

        return $links;
    }

    /**
     * Pagine un tableau
     *
     * @param array<mixed> $items
     * @return array{
     *     data: array<mixed>,
     *     pagination: array{
     *         total: int,
     *         per_page: int,
     *         current_page: int,
     *         last_page: int,
     *         from: int,
     *         to: int,
     *         has_more_pages: bool,
     *         has_previous: bool,
     *         offset: int
     *     }
     * }
     */
    public static function paginate(array $items, int $perPage = self::DEFAULT_PER_PAGE, int $currentPage = 1): array
    {
        $total = count($items);
        $pagination = self::calculate($total, $perPage, $currentPage);

        $data = array_slice($items, $pagination['offset'], $perPage);

        return [
            'data' => $data,
            'pagination' => $pagination,
        ];
    }

    /**
     * Génère l'URL d'une page
     *
     * @param array<string, mixed> $queryParams Paramètres de requête existants
     */
    public static function generatePageUrl(string $baseUrl, int $page, array $queryParams = []): string
    {
        $queryParams['page'] = $page;
        $queryString = http_build_query($queryParams);

        return $baseUrl . '?' . $queryString;
    }

    /**
     * Extrait le numéro de page depuis les paramètres de requête
     */
    public static function getPageFromRequest(int $default = 1): int
    {
        $page = $_GET['page'] ?? $_POST['page'] ?? $default;
        return max(1, (int) $page);
    }

    /**
     * Extrait le nombre d'éléments par page depuis les paramètres
     */
    public static function getPerPageFromRequest(int $default = self::DEFAULT_PER_PAGE): int
    {
        $perPage = $_GET['per_page'] ?? $_GET['limit'] ?? $_POST['per_page'] ?? $default;
        return max(1, min((int) $perPage, self::MAX_PER_PAGE));
    }

    /**
     * Génère le HTML des liens de pagination (Bootstrap style)
     */
    public static function renderHtml(int $currentPage, int $lastPage, string $baseUrl = '', array $queryParams = []): string
    {
        if ($lastPage <= 1) {
            return '';
        }

        $links = self::generateLinks($currentPage, $lastPage);
        $html = '<nav aria-label="Pagination"><ul class="pagination">';

        // Bouton précédent
        $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
        $prevUrl = self::generatePageUrl($baseUrl, max(1, $currentPage - 1), $queryParams);
        $html .= '<li class="page-item' . $prevDisabled . '">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($prevUrl) . '" aria-label="Précédent">';
        $html .= '<span aria-hidden="true">&laquo;</span></a></li>';

        // Liens de pages
        foreach ($links as $link) {
            if ($link === '...') {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            } else {
                $active = $link === $currentPage ? ' active' : '';
                $url = self::generatePageUrl($baseUrl, (int) $link, $queryParams);
                $html .= '<li class="page-item' . $active . '">';
                $html .= '<a class="page-link" href="' . htmlspecialchars($url) . '">' . $link . '</a></li>';
            }
        }

        // Bouton suivant
        $nextDisabled = $currentPage >= $lastPage ? ' disabled' : '';
        $nextUrl = self::generatePageUrl($baseUrl, min($lastPage, $currentPage + 1), $queryParams);
        $html .= '<li class="page-item' . $nextDisabled . '">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($nextUrl) . '" aria-label="Suivant">';
        $html .= '<span aria-hidden="true">&raquo;</span></a></li>';

        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * Génère les méta-données pour l'API
     *
     * @return array{
     *     current_page: int,
     *     last_page: int,
     *     per_page: int,
     *     total: int,
     *     from: int,
     *     to: int,
     *     links: array{
     *         first: string,
     *         last: string,
     *         prev: string|null,
     *         next: string|null
     *     }
     * }
     */
    public static function generateApiMeta(int $total, int $perPage, int $currentPage, string $baseUrl): array
    {
        $pagination = self::calculate($total, $perPage, $currentPage);

        return [
            'current_page' => $pagination['current_page'],
            'last_page' => $pagination['last_page'],
            'per_page' => $pagination['per_page'],
            'total' => $pagination['total'],
            'from' => $pagination['from'],
            'to' => $pagination['to'],
            'links' => [
                'first' => self::generatePageUrl($baseUrl, 1, ['per_page' => $perPage]),
                'last' => self::generatePageUrl($baseUrl, $pagination['last_page'], ['per_page' => $perPage]),
                'prev' => $pagination['has_previous'] 
                    ? self::generatePageUrl($baseUrl, $currentPage - 1, ['per_page' => $perPage]) 
                    : null,
                'next' => $pagination['has_more_pages'] 
                    ? self::generatePageUrl($baseUrl, $currentPage + 1, ['per_page' => $perPage]) 
                    : null,
            ],
        ];
    }

    /**
     * Calcule l'offset SQL pour une requête paginée
     */
    public static function getOffset(int $page, int $perPage): int
    {
        return max(0, ($page - 1) * $perPage);
    }

    /**
     * Génère la clause LIMIT SQL
     */
    public static function getLimitClause(int $page, int $perPage): string
    {
        $offset = self::getOffset($page, $perPage);
        return "LIMIT {$perPage} OFFSET {$offset}";
    }

    /**
     * Affiche les informations de pagination en texte
     */
    public static function getSummary(int $total, int $from, int $to): string
    {
        if ($total === 0) {
            return 'Aucun résultat';
        }

        return "Affichage de {$from} à {$to} sur {$total} résultats";
    }
}
