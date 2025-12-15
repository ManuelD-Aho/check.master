<?php

declare(strict_types=1);
/**
 * CheckMaster - Sidebar Navigation Partial
 * ==========================================
 * Navigation principale de l'application
 * 
 * Variables attendues:
 * @var string $currentPage - Page courante pour highlight
 * @var array  $user        - Utilisateur connecté
 */

$currentPage = $currentPage ?? '';
$user = $user ?? ['name' => 'Utilisateur', 'role' => 'Rôle', 'initials' => 'U'];

// Menu items groupés par section
$menuGroups = [
    'principal' => [
        'title' => '',
        'items' => [
            ['icon' => 'home', 'label' => 'Tableau de bord', 'url' => '/dashboard', 'page' => 'dashboard'],
        ]
    ],
    'scolarite' => [
        'title' => 'Scolarité',
        'items' => [
            ['icon' => 'users', 'label' => 'Étudiants', 'url' => '/scolarite/etudiants', 'page' => 'etudiants'],
            ['icon' => 'file-text', 'label' => 'Candidatures', 'url' => '/scolarite/candidatures', 'page' => 'candidatures'],
            ['icon' => 'credit-card', 'label' => 'Paiements', 'url' => '/scolarite/paiements', 'page' => 'paiements'],
        ]
    ],
    'commission' => [
        'title' => 'Commission',
        'items' => [
            ['icon' => 'clipboard-list', 'label' => 'Sessions', 'url' => '/commission/sessions', 'page' => 'sessions'],
            ['icon' => 'check-square', 'label' => 'Évaluations', 'url' => '/commission/evaluations', 'page' => 'evaluations'],
        ]
    ],
    'soutenance' => [
        'title' => 'Soutenance',
        'items' => [
            ['icon' => 'calendar', 'label' => 'Planning', 'url' => '/soutenance/planning', 'page' => 'planning'],
            ['icon' => 'users', 'label' => 'Jury', 'url' => '/soutenance/jury', 'page' => 'jury'],
        ]
    ],
    'admin' => [
        'title' => 'Administration',
        'items' => [
            ['icon' => 'user-cog', 'label' => 'Utilisateurs', 'url' => '/admin/utilisateurs', 'page' => 'utilisateurs'],
            ['icon' => 'settings', 'label' => 'Paramètres', 'url' => '/admin/parametres', 'page' => 'parametres'],
            ['icon' => 'activity', 'label' => 'Audit', 'url' => '/admin/audit', 'page' => 'audit'],
        ]
    ],
];

// Helper function pour les icônes SVG
function sidebarIcon(string $name): string
{
    $icons = [
        'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
        'file-text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
        'credit-card' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
        'clipboard-list' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>',
        'check-square' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,11 12,14 22,4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'user-cog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><circle cx="18" cy="16" r="3"/></svg>',
        'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
        'activity' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>',
        'log-out' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
        'chevron-left' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg>',
    ];
    return $icons[$name] ?? '';
}
?>

<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-header">
        <a href="/dashboard" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <svg viewBox="0 0 36 36" fill="none">
                    <rect width="36" height="36" rx="8" fill="currentColor" fill-opacity="0.2" />
                    <path d="M10 18l5 5 11-11" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <span class="sidebar-logo-text">CheckMaster</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <?php foreach ($menuGroups as $groupKey => $group): ?>
            <div class="sidebar-nav-group">
                <?php if (!empty($group['title'])): ?>
                    <div class="sidebar-nav-group-title"><?= htmlspecialchars($group['title']) ?></div>
                <?php endif; ?>

                <?php foreach ($group['items'] as $item): ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>"
                        class="sidebar-nav-item <?= $currentPage === $item['page'] ? 'is-active' : '' ?>">
                        <span class="sidebar-nav-item-icon"><?= sidebarIcon($item['icon']) ?></span>
                        <span class="sidebar-nav-item-text"><?= htmlspecialchars($item['label']) ?></span>
                        <?php if (isset($item['badge'])): ?>
                            <span class="sidebar-nav-item-badge"><?= $item['badge'] ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <!-- User info -->
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <?= htmlspecialchars($user['initials'] ?? 'U') ?>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?></div>
                <div class="sidebar-user-role"><?= htmlspecialchars($user['role'] ?? '') ?></div>
            </div>
        </div>

        <!-- Logout -->
        <a href="/logout" class="sidebar-nav-item">
            <span class="sidebar-nav-item-icon"><?= sidebarIcon('log-out') ?></span>
            <span class="sidebar-nav-item-text">Déconnexion</span>
        </a>

        <!-- Collapse toggle -->
        <button type="button" class="sidebar-toggle" id="sidebar-toggle" title="Réduire le menu">
            <?= sidebarIcon('chevron-left') ?>
        </button>
    </div>
</aside>