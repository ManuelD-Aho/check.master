<?php

/**
 * Dashboard Admin CheckMaster
 * ===========================
 * Tableau de bord principal avec statistiques complètes
 * 
 * Variables attendues:
 * @var array $stats - Statistiques globales
 * @var array $recentActivities - Activités récentes
 * @var array $pendingTasks - Tâches en attente
 */

declare(strict_types=1);

// Données de démonstration (en production, depuis la BDD via le Controller)
$stats = $stats ?? [
    'total_etudiants' => 40,
    'dossiers_actifs' => 25,
    'soutenances_mois' => 5,
    'taux_reussite' => 98,
    'paiements_total' => 18700000,
    'paiements_dus' => 800000,
    'rapports_attente' => 8,
    'jurys_constituer' => 3,
];

$workflowStats = $workflowStats ?? [
    ['etat' => 'Inscrits', 'count' => 5, 'color' => '#9ca3af'],
    ['etat' => 'Candidature soumise', 'count' => 2, 'color' => '#3b82f6'],
    ['etat' => 'En vérification', 'count' => 3, 'color' => '#8b5cf6'],
    ['etat' => 'En commission', 'count' => 3, 'color' => '#f59e0b'],
    ['etat' => 'Rapport validé', 'count' => 8, 'color' => '#22c55e'],
    ['etat' => 'Soutenance planifiée', 'count' => 2, 'color' => '#06b6d4'],
    ['etat' => 'Diplôme délivré', 'count' => 1, 'color' => '#10b981'],
];

$recentActivities = $recentActivities ?? [
    ['type' => 'soutenance', 'message' => 'KONE Adama - Soutenance terminée', 'time' => 'Il y a 2h', 'status' => 'success'],
    ['type' => 'paiement', 'message' => 'SANGARE Fatou - Paiement reçu 550 000 FCFA', 'time' => 'Il y a 4h', 'status' => 'info'],
    ['type' => 'rapport', 'message' => 'BROU Jean-Pierre - Rapport soumis pour évaluation', 'time' => 'Il y a 5h', 'status' => 'pending'],
    ['type' => 'commission', 'message' => 'Session commission #4 - Vote en cours', 'time' => 'Hier', 'status' => 'warning'],
    ['type' => 'alerte', 'message' => 'Alerte SLA - 3 dossiers en retard', 'time' => 'Hier', 'status' => 'danger'],
];

$upcomingSoutenances = $upcomingSoutenances ?? [
    ['etudiant' => 'BROU Jean-Pierre', 'date' => '20/12/2024', 'heure' => '10:00', 'salle' => 'Amphi 1', 'theme' => 'Système de suivi GPS'],
    ['etudiant' => 'ASSI Marie-Claire', 'date' => '22/12/2024', 'heure' => '09:00', 'salle' => 'Salle A102', 'theme' => 'Chatbot intelligent NLP'],
];

$pendingTasks = $pendingTasks ?? [
    ['task' => 'Valider 3 candidatures', 'service' => 'Scolarité', 'urgence' => 'haute'],
    ['task' => 'Vérifier format 2 rapports', 'service' => 'Communication', 'urgence' => 'normale'],
    ['task' => 'Constituer jury KONAN Yves', 'service' => 'Commission', 'urgence' => 'normale'],
    ['task' => 'Relancer encadreur Dr. SANOGO', 'service' => 'Admin', 'urgence' => 'basse'],
];

// Configuration page
$pageTitle = 'Tableau de bord';
$currentPage = 'dashboard';
$user = [
    'name' => 'Admin System',
    'role' => 'Administrateur',
    'initials' => 'AS'
];
$breadcrumbs = [
    ['label' => 'Accueil', 'url' => '/dashboard'],
    ['label' => 'Tableau de bord']
];

// Contenu de la page
ob_start();
?>

<div class="dashboard">
    <!-- Stats Cards Row -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['total_etudiants'] ?></span>
                <span class="stat-card-label">Étudiants M2</span>
            </div>
            <div class="stat-card-trend stat-card-trend--up">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                </svg>
                +8%
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--teal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['dossiers_actifs'] ?></span>
                <span class="stat-card-label">Dossiers en cours</span>
            </div>
            <div class="stat-card-badge stat-card-badge--warning">
                <?= $stats['rapports_attente'] ?> en attente
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['soutenances_mois'] ?></span>
                <span class="stat-card-label">Soutenances ce mois</span>
            </div>
            <div class="stat-card-badge stat-card-badge--info">
                <?= $stats['jurys_constituer'] ?> jurys à former
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= number_format($stats['paiements_total'], 0, ',', ' ') ?></span>
                <span class="stat-card-label">FCFA encaissés</span>
            </div>
            <div class="stat-card-badge stat-card-badge--danger">
                <?= number_format($stats['paiements_dus'], 0, ',', ' ') ?> dus
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Workflow Pipeline -->
        <div class="dashboard-card dashboard-card--wide">
            <div class="card-header">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Pipeline des dossiers
                </h3>
                <a href="/admin/workflow" class="card-action">Voir détails</a>
            </div>
            <div class="card-body">
                <div class="workflow-pipeline">
                    <?php foreach ($workflowStats as $stat): ?>
                    <div class="pipeline-item">
                        <div class="pipeline-bar" style="--pipeline-color: <?= $stat['color'] ?>; --pipeline-height: <?= min(100, $stat['count'] * 12) ?>%"></div>
                        <div class="pipeline-count" style="color: <?= $stat['color'] ?>"><?= $stat['count'] ?></div>
                        <div class="pipeline-label"><?= htmlspecialchars($stat['etat']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Taux de réussite -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    Taux de réussite
                </h3>
            </div>
            <div class="card-body card-body--center">
                <div class="gauge-container">
                    <svg class="gauge" viewBox="0 0 120 120">
                        <circle class="gauge-bg" cx="60" cy="60" r="50"/>
                        <circle class="gauge-fill" cx="60" cy="60" r="50" 
                                style="--gauge-percent: <?= $stats['taux_reussite'] ?>"/>
                    </svg>
                    <div class="gauge-value"><?= $stats['taux_reussite'] ?>%</div>
                </div>
                <p class="gauge-label">Taux de réussite aux soutenances</p>
            </div>
        </div>

        <!-- Activités récentes -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Activités récentes
                </h3>
                <a href="/admin/audit" class="card-action">Tout voir</a>
            </div>
            <div class="card-body card-body--scroll">
                <ul class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
                    <li class="activity-item">
                        <div class="activity-icon activity-icon--<?= $activity['status'] ?>">
                            <?php if ($activity['type'] === 'soutenance'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <?php elseif ($activity['type'] === 'paiement'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            <?php elseif ($activity['type'] === 'rapport'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <?php elseif ($activity['type'] === 'commission'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg>
                            <?php else: ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <?php endif; ?>
                        </div>
                        <div class="activity-content">
                            <p class="activity-message"><?= htmlspecialchars($activity['message']) ?></p>
                            <span class="activity-time"><?= htmlspecialchars($activity['time']) ?></span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Prochaines soutenances -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Prochaines soutenances
                </h3>
                <a href="/soutenance/planning" class="card-action">Planning complet</a>
            </div>
            <div class="card-body">
                <div class="soutenance-list">
                    <?php foreach ($upcomingSoutenances as $soutenance): ?>
                    <div class="soutenance-item">
                        <div class="soutenance-date">
                            <span class="soutenance-day"><?= explode('/', $soutenance['date'])[0] ?></span>
                            <span class="soutenance-month">Déc</span>
                        </div>
                        <div class="soutenance-info">
                            <h4 class="soutenance-student"><?= htmlspecialchars($soutenance['etudiant']) ?></h4>
                            <p class="soutenance-details">
                                <?= $soutenance['heure'] ?> - <?= $soutenance['salle'] ?>
                            </p>
                            <p class="soutenance-theme"><?= htmlspecialchars($soutenance['theme']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Tâches en attente -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                    Tâches en attente
                </h3>
                <span class="card-badge"><?= count($pendingTasks) ?></span>
            </div>
            <div class="card-body">
                <ul class="task-list">
                    <?php foreach ($pendingTasks as $task): ?>
                    <li class="task-item">
                        <div class="task-checkbox">
                            <input type="checkbox" id="task-<?= md5($task['task']) ?>">
                            <label for="task-<?= md5($task['task']) ?>"></label>
                        </div>
                        <div class="task-content">
                            <span class="task-text"><?= htmlspecialchars($task['task']) ?></span>
                            <span class="task-service"><?= htmlspecialchars($task['service']) ?></span>
                        </div>
                        <span class="task-urgence task-urgence--<?= $task['urgence'] ?>">
                            <?= ucfirst($task['urgence']) ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Alertes SLA -->
        <div class="dashboard-card dashboard-card--alert">
            <div class="card-header">
                <h3 class="card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    Alertes SLA
                </h3>
                <span class="card-badge card-badge--danger">3</span>
            </div>
            <div class="card-body">
                <div class="alert-list">
                    <div class="alert-item alert-item--danger">
                        <div class="alert-indicator"></div>
                        <div class="alert-content">
                            <strong>Délai dépassé</strong>
                            <p>OUATTARA Mariam - Avis encadreur en attente depuis 12 jours</p>
                        </div>
                        <button class="alert-action">Escalader</button>
                    </div>
                    <div class="alert-item alert-item--warning">
                        <div class="alert-indicator"></div>
                        <div class="alert-content">
                            <strong>80% du délai</strong>
                            <p>ASSI Marie-Claire - Constitution jury (2 jours restants)</p>
                        </div>
                        <button class="alert-action">Relancer</button>
                    </div>
                    <div class="alert-item alert-item--info">
                        <div class="alert-indicator"></div>
                        <div class="alert-content">
                            <strong>50% du délai</strong>
                            <p>ANOH Prisca - Vérification format (4 jours restants)</p>
                        </div>
                        <button class="alert-action">Voir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ═══════════════════════════════════════════════════════════════════════════
   DASHBOARD STYLES
   ═══════════════════════════════════════════════════════════════════════════ */

.dashboard {
    padding: var(--space-6);
}

/* Stats Cards */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
    margin-bottom: var(--space-6);
}

.stat-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--space-5);
    display: flex;
    align-items: flex-start;
    gap: var(--space-4);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--color-gray-100);
}

.stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-card-icon svg {
    width: 24px;
    height: 24px;
}

.stat-card-icon--blue {
    background: var(--color-primary-100);
    color: var(--color-primary-700);
}

.stat-card-icon--teal {
    background: var(--color-accent-100);
    color: var(--color-accent-600);
}

.stat-card-icon--amber {
    background: var(--color-warning-100);
    color: var(--color-warning-700);
}

.stat-card-icon--green {
    background: var(--color-success-100);
    color: var(--color-success-700);
}

.stat-card-content {
    flex: 1;
    min-width: 0;
}

.stat-card-value {
    display: block;
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-gray-900);
    line-height: 1.2;
}

.stat-card-label {
    display: block;
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    margin-top: var(--space-1);
}

.stat-card-trend {
    display: flex;
    align-items: center;
    gap: var(--space-1);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-medium);
}

.stat-card-trend svg {
    width: 16px;
    height: 16px;
}

.stat-card-trend--up {
    color: var(--color-success-600);
}

.stat-card-trend--down {
    color: var(--color-error-600);
}

.stat-card-badge {
    font-size: var(--font-size-xs);
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-full);
    font-weight: var(--font-weight-medium);
}

.stat-card-badge--warning {
    background: var(--color-warning-100);
    color: var(--color-warning-700);
}

.stat-card-badge--info {
    background: var(--color-info-100);
    color: var(--color-info-700);
}

.stat-card-badge--danger {
    background: var(--color-error-100);
    color: var(--color-error-700);
}

/* Dashboard Grid */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-4);
}

.dashboard-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--color-gray-100);
    overflow: hidden;
}

.dashboard-card--wide {
    grid-column: span 2;
}

.dashboard-card--alert {
    border-color: var(--color-warning-200);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-4) var(--space-5);
    border-bottom: 1px solid var(--color-gray-100);
}

.card-title {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
}

.card-title svg {
    width: 20px;
    height: 20px;
    color: var(--color-gray-500);
}

.card-action {
    font-size: var(--font-size-sm);
    color: var(--color-primary-600);
    text-decoration: none;
    font-weight: var(--font-weight-medium);
}

.card-action:hover {
    color: var(--color-primary-700);
    text-decoration: underline;
}

.card-badge {
    background: var(--color-gray-100);
    color: var(--color-gray-700);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-full);
}

.card-badge--danger {
    background: var(--color-error-100);
    color: var(--color-error-700);
}

.card-body {
    padding: var(--space-5);
}

.card-body--center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--space-6);
}

.card-body--scroll {
    max-height: 320px;
    overflow-y: auto;
}

/* Workflow Pipeline */
.workflow-pipeline {
    display: flex;
    gap: var(--space-2);
    height: 200px;
    align-items: flex-end;
}

.pipeline-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
}

.pipeline-bar {
    width: 100%;
    max-width: 60px;
    height: var(--pipeline-height);
    min-height: 20px;
    background: var(--pipeline-color);
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    opacity: 0.85;
}

.pipeline-count {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
}

.pipeline-label {
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    text-align: center;
    line-height: 1.3;
}

/* Gauge */
.gauge-container {
    position: relative;
    width: 150px;
    height: 150px;
}

.gauge {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.gauge-bg {
    fill: none;
    stroke: var(--color-gray-200);
    stroke-width: 10;
}

.gauge-fill {
    fill: none;
    stroke: var(--color-success-500);
    stroke-width: 10;
    stroke-linecap: round;
    stroke-dasharray: 314;
    stroke-dashoffset: calc(314 - (314 * var(--gauge-percent) / 100));
}

.gauge-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: var(--font-size-3xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-gray-900);
}

.gauge-label {
    margin-top: var(--space-4);
    font-size: var(--font-size-sm);
    color: var(--color-gray-500);
    text-align: center;
}

/* Activity List */
.activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.activity-item {
    display: flex;
    gap: var(--space-3);
    padding: var(--space-3) 0;
    border-bottom: 1px solid var(--color-gray-100);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-icon svg {
    width: 16px;
    height: 16px;
}

.activity-icon--success {
    background: var(--color-success-100);
    color: var(--color-success-600);
}

.activity-icon--info {
    background: var(--color-info-100);
    color: var(--color-info-600);
}

.activity-icon--pending {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
}

.activity-icon--warning {
    background: var(--color-warning-100);
    color: var(--color-warning-600);
}

.activity-icon--danger {
    background: var(--color-error-100);
    color: var(--color-error-600);
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-message {
    font-size: var(--font-size-sm);
    color: var(--color-gray-700);
    margin: 0;
}

.activity-time {
    font-size: var(--font-size-xs);
    color: var(--color-gray-400);
}

/* Soutenance List */
.soutenance-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.soutenance-item {
    display: flex;
    gap: var(--space-4);
}

.soutenance-date {
    width: 48px;
    height: 56px;
    background: var(--color-primary-100);
    border-radius: var(--radius-lg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.soutenance-day {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary-700);
    line-height: 1;
}

.soutenance-month {
    font-size: var(--font-size-xs);
    color: var(--color-primary-600);
    text-transform: uppercase;
}

.soutenance-info {
    flex: 1;
    min-width: 0;
}

.soutenance-student {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-gray-900);
    margin: 0 0 var(--space-1) 0;
}

.soutenance-details {
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    margin: 0;
}

.soutenance-theme {
    font-size: var(--font-size-xs);
    color: var(--color-gray-400);
    margin: var(--space-1) 0 0 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Task List */
.task-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.task-item {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3) 0;
    border-bottom: 1px solid var(--color-gray-100);
}

.task-item:last-child {
    border-bottom: none;
}

.task-checkbox {
    position: relative;
}

.task-checkbox input {
    appearance: none;
    width: 18px;
    height: 18px;
    border: 2px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.task-checkbox input:checked {
    background: var(--color-accent-500);
    border-color: var(--color-accent-500);
}

.task-checkbox input:checked::after {
    content: '✓';
    position: absolute;
    top: 0;
    left: 4px;
    color: white;
    font-size: 12px;
}

.task-content {
    flex: 1;
    min-width: 0;
}

.task-text {
    display: block;
    font-size: var(--font-size-sm);
    color: var(--color-gray-700);
}

.task-service {
    display: block;
    font-size: var(--font-size-xs);
    color: var(--color-gray-400);
}

.task-urgence {
    font-size: var(--font-size-xs);
    padding: var(--space-1) var(--space-2);
    border-radius: var(--radius-full);
    font-weight: var(--font-weight-medium);
}

.task-urgence--haute {
    background: var(--color-error-100);
    color: var(--color-error-700);
}

.task-urgence--normale {
    background: var(--color-warning-100);
    color: var(--color-warning-700);
}

.task-urgence--basse {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
}

/* Alert List */
.alert-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.alert-item {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3);
    border-radius: var(--radius-lg);
    background: var(--color-gray-50);
}

.alert-indicator {
    width: 8px;
    height: 8px;
    border-radius: var(--radius-full);
    flex-shrink: 0;
}

.alert-item--danger .alert-indicator {
    background: var(--color-error-500);
}

.alert-item--warning .alert-indicator {
    background: var(--color-warning-500);
}

.alert-item--info .alert-indicator {
    background: var(--color-info-500);
}

.alert-content {
    flex: 1;
    min-width: 0;
}

.alert-content strong {
    display: block;
    font-size: var(--font-size-xs);
    color: var(--color-gray-500);
    font-weight: var(--font-weight-semibold);
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.alert-content p {
    font-size: var(--font-size-sm);
    color: var(--color-gray-700);
    margin: var(--space-1) 0 0;
}

.alert-action {
    padding: var(--space-1-5) var(--space-3);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-medium);
    color: var(--color-primary-600);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-md);
    cursor: pointer;
}

.alert-action:hover {
    background: var(--color-primary-50);
    border-color: var(--color-primary-200);
}

/* Responsive */
@media (max-width: 1280px) {
    .dashboard-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .dashboard-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .dashboard-card--wide {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .dashboard {
        padding: var(--space-4);
    }
    
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-card--wide {
        grid-column: span 1;
    }
    
    .workflow-pipeline {
        flex-wrap: wrap;
        height: auto;
    }
    
    .pipeline-item {
        flex-basis: calc(25% - var(--space-2));
    }
}
</style>

<?php
$content = ob_get_clean();

// Include le layout principal
include __DIR__ . '/../layouts/app.php';
?>
