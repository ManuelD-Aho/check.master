<?php
/**
 * Dashboard Étudiant - CheckMaster
 * ================================
 * Tableau de bord pour l'étudiant
 */

declare(strict_types=1);

$stats = $stats ?? [
    'etat_dossier' => 'Rapport validé',
    'progression' => 75,
    'paiements_effectues' => 1500000,
    'paiements_restants' => 500000,
];

$timeline = $timeline ?? [
    ['etape' => 'Inscription', 'date' => '01/09/2024', 'statut' => 'Terminé'],
    ['etape' => 'Candidature validée', 'date' => '15/09/2024', 'statut' => 'Terminé'],
    ['etape' => 'Rapport soumis', 'date' => '10/11/2024', 'statut' => 'Terminé'],
    ['etape' => 'En commission', 'date' => '20/11/2024', 'statut' => 'En cours'],
    ['etape' => 'Soutenance', 'date' => 'À planifier', 'statut' => 'En attente'],
];

$pageTitle = 'Mon Tableau de bord';
$currentPage = 'dashboard';
$breadcrumbs = [
    ['label' => 'Tableau de bord']
];

ob_start();
?>

<div class="dashboard">
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-label">État du dossier</span>
                <span class="stat-card-value stat-card-value--small"><?= htmlspecialchars($stats['etat_dossier']) ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['progression'] ?>%</span>
                <span class="stat-card-label">Progression</span>
            </div>
            <div class="stat-card-progress">
                <div class="progress-bar" style="width: <?= $stats['progression'] ?>%"></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= number_format($stats['paiements_effectues'], 0, ',', ' ') ?></span>
                <span class="stat-card-label">FCFA payés</span>
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
                <span class="stat-card-value"><?= number_format($stats['paiements_restants'], 0, ',', ' ') ?></span>
                <span class="stat-card-label">FCFA restants</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card dashboard-card--wide">
            <div class="card-header">
                <h3 class="card-title">Parcours du dossier</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($timeline as $index => $step): ?>
                    <div class="timeline-item timeline-item--<?= strtolower(str_replace(' ', '-', $step['statut'])) ?>">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-title"><?= htmlspecialchars($step['etape']) ?></div>
                            <div class="timeline-meta"><?= htmlspecialchars($step['date']) ?></div>
                        </div>
                        <span class="badge badge--<?= $step['statut'] === 'Terminé' ? 'success' : ($step['statut'] === 'En cours' ? 'warning' : 'secondary') ?>">
                            <?= htmlspecialchars($step['statut']) ?>
                        </span>
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
                    <a href="/etudiant/rapport/editeur" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        <span>Mon rapport</span>
                    </a>
                    <a href="/etudiant/finances" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        <span>Mes paiements</span>
                    </a>
                    <a href="/etudiant/resultats/notes" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        </svg>
                        <span>Mes résultats</span>
                    </a>
                    <a href="/etudiant/candidature/statut" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        <span>Suivi candidature</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
