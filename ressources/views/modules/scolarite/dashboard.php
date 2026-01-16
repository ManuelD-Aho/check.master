<?php
/**
 * Dashboard Scolarité - CheckMaster
 * =================================
 * Tableau de bord service scolarité
 */

declare(strict_types=1);

$stats = $stats ?? [
    'total_etudiants' => 40,
    'candidatures_en_attente' => 5,
    'inscriptions_validees' => 35,
    'paiements_a_verifier' => 3,
];

$recentActivities = $recentActivities ?? [
    ['action' => 'Nouvelle candidature', 'etudiant' => 'DIALLO Aminata', 'time' => 'Il y a 30 min'],
    ['action' => 'Paiement reçu', 'etudiant' => 'TRAORE Ibrahim', 'time' => 'Il y a 1h'],
    ['action' => 'Inscription validée', 'etudiant' => 'KOFFI Ange', 'time' => 'Il y a 2h'],
];

$pageTitle = 'Tableau de bord Scolarité';
$currentPage = 'scolarite';
$breadcrumbs = [
    ['label' => 'Scolarité', 'url' => '/scolarite'],
    ['label' => 'Tableau de bord']
];

ob_start();
?>

<div class="dashboard">
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
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['candidatures_en_attente'] ?></span>
                <span class="stat-card-label">Candidatures à traiter</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['inscriptions_validees'] ?></span>
                <span class="stat-card-label">Inscriptions validées</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['paiements_a_verifier'] ?></span>
                <span class="stat-card-label">Paiements à vérifier</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Activités récentes</h3>
            </div>
            <div class="card-body">
                <div class="activity-list">
                    <?php foreach ($recentActivities as $activity): ?>
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
                                <?= htmlspecialchars($activity['etudiant']) ?> • <?= htmlspecialchars($activity['time']) ?>
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
                    <a href="/scolarite/etudiants/create" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/>
                            <line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                        <span>Nouvel étudiant</span>
                    </a>
                    <a href="/scolarite/candidatures" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        </svg>
                        <span>Candidatures</span>
                    </a>
                    <a href="/scolarite/inscriptions" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"/>
                        </svg>
                        <span>Inscriptions</span>
                    </a>
                    <a href="/scolarite/paiements" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        </svg>
                        <span>Paiements</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
