<?php
/**
 * Dashboard Admin Module - CheckMaster
 * ====================================
 * Tableau de bord spécifique au module administration
 * 
 * Variables attendues:
 * @var array $stats - Statistiques du module admin
 */

declare(strict_types=1);

// Données de démonstration
$stats = $stats ?? [
    'total_users' => 45,
    'active_sessions' => 12,
    'logs_today' => 342,
    'pending_requests' => 5,
];

$recentActions = $recentActions ?? [
    ['action' => 'Nouvel utilisateur créé', 'user' => 'Dr. KOUAME', 'time' => 'Il y a 10 min'],
    ['action' => 'Paramètre modifié', 'user' => 'Admin System', 'time' => 'Il y a 1h'],
    ['action' => 'Référentiel mis à jour', 'user' => 'BAMBA Sekou', 'time' => 'Il y a 2h'],
];

// Configuration page
$pageTitle = 'Tableau de bord Administration';
$currentPage = 'admin';
$breadcrumbs = [
    ['label' => 'Administration', 'url' => '/admin'],
    ['label' => 'Tableau de bord']
];

// Contenu de la page
ob_start();
?>

<div class="dashboard">
    <!-- Stats Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['total_users'] ?></span>
                <span class="stat-card-label">Utilisateurs actifs</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v6m10-7h-6M7 12H1"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['active_sessions'] ?></span>
                <span class="stat-card-label">Sessions actives</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['logs_today'] ?></span>
                <span class="stat-card-label">Logs aujourd'hui</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['pending_requests'] ?></span>
                <span class="stat-card-label">Demandes en attente</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Actions rapides</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="/admin/utilisateurs/create" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    <span>Nouvel utilisateur</span>
                </a>
                <a href="/admin/audit/console" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <span>Voir audit</span>
                </a>
                <a href="/admin/parametres" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83"/>
                    </svg>
                    <span>Paramètres</span>
                </a>
                <a href="/admin/archives" class="quick-action-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 8v13H3V8M1 3h22v5H1z"/>
                    </svg>
                    <span>Archives</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Actions -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Activités récentes</h3>
        </div>
        <div class="card-body">
            <div class="activity-list">
                <?php foreach ($recentActions as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text"><?= htmlspecialchars($activity['action']) ?></div>
                        <div class="activity-meta">
                            Par <?= htmlspecialchars($activity['user']) ?> • <?= htmlspecialchars($activity['time']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
