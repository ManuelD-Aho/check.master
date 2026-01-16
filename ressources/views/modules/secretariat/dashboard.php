<?php
/**
 * Dashboard Secrétariat - CheckMaster
 * ===================================
 * Tableau de bord service secrétariat
 */

declare(strict_types=1);

$stats = $stats ?? [
    'dossiers_en_cours' => 28,
    'documents_a_verifier' => 12,
    'convocations_a_envoyer' => 5,
    'pv_a_rediger' => 2,
];

$tasks = $tasks ?? [
    ['task' => 'Vérifier dossiers nouveaux inscrits', 'priority' => 'Haute', 'due' => 'Aujourd\'hui'],
    ['task' => 'Préparer convocations soutenances', 'priority' => 'Moyenne', 'due' => 'Demain'],
    ['task' => 'Archiver PV commission #4', 'priority' => 'Basse', 'due' => 'Cette semaine'],
];

$pageTitle = 'Tableau de bord Secrétariat';
$currentPage = 'secretariat';
$breadcrumbs = [
    ['label' => 'Secrétariat', 'url' => '/secretariat'],
    ['label' => 'Tableau de bord']
];

ob_start();
?>

<div class="dashboard">
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['dossiers_en_cours'] ?></span>
                <span class="stat-card-label">Dossiers en cours</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['documents_a_verifier'] ?></span>
                <span class="stat-card-label">Documents à vérifier</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['convocations_a_envoyer'] ?></span>
                <span class="stat-card-label">Convocations à envoyer</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['pv_a_rediger'] ?></span>
                <span class="stat-card-label">PV à rédiger</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Tâches à effectuer</h3>
            </div>
            <div class="card-body">
                <div class="task-list">
                    <?php foreach ($tasks as $task): ?>
                    <div class="task-item">
                        <input type="checkbox" class="task-checkbox">
                        <div class="task-content">
                            <div class="task-title"><?= htmlspecialchars($task['task']) ?></div>
                            <div class="task-meta">
                                <span class="badge badge--<?= $task['priority'] === 'Haute' ? 'danger' : ($task['priority'] === 'Moyenne' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($task['priority']) ?>
                                </span>
                                • <?= htmlspecialchars($task['due']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Actions rapides</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="/secretariat/dossiers" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        </svg>
                        <span>Gérer dossiers</span>
                    </a>
                    <a href="/communication/messagerie" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                        <span>Messagerie</span>
                    </a>
                    <a href="/documents" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586"/>
                        </svg>
                        <span>Documents</span>
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
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
