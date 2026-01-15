<?php

declare(strict_types=1);
/**
 * CheckMaster - Header Partial
 * ==============================
 * Barre de header avec breadcrumb et actions
 * 
 * Variables attendues:
 * @var string $pageTitle    - Titre de la page
 * @var array  $breadcrumbs  - Fil d'Ariane
 * @var array  $user         - Utilisateur connecté
 */

$pageTitle = $pageTitle ?? '';
$breadcrumbs = $breadcrumbs ?? [];
$user = $user ?? ['name' => 'Utilisateur', 'initials' => 'U'];
?>

<header class="header">
    <!-- Left: Menu toggle + Title -->
    <div class="header-left">
        <button type="button" class="header-menu-toggle" id="menu-toggle" aria-label="Ouvrir le menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>
        </button>

        <div class="header-title-section">
            <?php if (!empty($breadcrumbs)): ?>
                <nav class="header-breadcrumb" aria-label="Fil d'Ariane">
                    <?php foreach ($breadcrumbs as $index => $crumb): ?>
                        <?php if ($index > 0): ?>
                            <span class="breadcrumb-separator">›</span>
                        <?php endif; ?>
                        <?php if (isset($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
                            <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                        <?php else: ?>
                            <span><?= htmlspecialchars($crumb['label']) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <?php if ($pageTitle): ?>
                <h1 class="header-title"><?= htmlspecialchars($pageTitle) ?></h1>
            <?php endif; ?>
        </div>
    </div>

    <!-- Center: Search -->
    <div class="header-search">
        <svg class="header-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="search"
            class="header-search-input"
            placeholder="Rechercher..."
            aria-label="Rechercher">
        <kbd class="header-search-shortcut">⌘K</kbd>
    </div>

    <!-- Right: Actions + User -->
    <div class="header-right">
        <!-- Notifications -->
        <div class="dropdown">
            <button type="button" class="header-action" aria-label="Notifications">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 01-3.46 0" />
                </svg>
                <span class="header-action-badge">3</span>
            </button>
        </div>

        <!-- Messages -->
        <div class="dropdown">
            <button type="button" class="header-action" aria-label="Messages">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                </svg>
                <span class="header-action-badge">5</span>
            </button>
        </div>

        <!-- User Menu -->
        <div class="dropdown">
            <button type="button" class="header-user" aria-haspopup="true" aria-expanded="false">
                <div class="header-user-avatar">
                    <?= htmlspecialchars($user['initials'] ?? 'U') ?>
                </div>
                <div class="header-user-info">
                    <div class="header-user-name"><?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?></div>
                    <div class="header-user-role"><?= htmlspecialchars($user['role'] ?? '') ?></div>
                </div>
                <svg class="header-user-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6,9 12,15 18,9" />
                </svg>
            </button>

            <div class="dropdown-menu dropdown-menu-right">
                <a href="/etudiant/profil" class="dropdown-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Mon profil
                </a>
                <a href="/admin/parametres" class="dropdown-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33" />
                    </svg>
                    Paramètres
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout" class="dropdown-item is-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                        <polyline points="16,17 21,12 16,7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Déconnexion
                </a>
            </div>
        </div>
    </div>
</header>