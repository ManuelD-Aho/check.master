<?php
/**
 * Dashboard Commission - CheckMaster
 * ==================================
 * Tableau de bord pour la commission d'évaluation
 */

declare(strict_types=1);

$stats = $stats ?? [
    'sessions_actives' => 2,
    'evaluations_en_cours' => 8,
    'votes_en_attente' => 3,
    'rapports_valides' => 15,
];

$sessions = $sessions ?? [
    ['nom' => 'Session #5 - Décembre 2024', 'etat' => 'En cours', 'rapports' => 5, 'votes' => 2],
    ['nom' => 'Session #4 - Novembre 2024', 'etat' => 'Clôturée', 'rapports' => 8, 'votes' => 8],
];

$pageTitle = 'Tableau de bord Commission';
$currentPage = 'commission';
$breadcrumbs = [
    ['label' => 'Commission', 'url' => '/commission'],
    ['label' => 'Tableau de bord']
];

ob_start();
?>

<div class="dashboard">
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['sessions_actives'] ?></span>
                <span class="stat-card-label">Sessions actives</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['evaluations_en_cours'] ?></span>
                <span class="stat-card-label">Évaluations en cours</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['votes_en_attente'] ?></span>
                <span class="stat-card-label">Votes en attente</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= $stats['rapports_valides'] ?></span>
                <span class="stat-card-label">Rapports validés</span>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Sessions récentes</h3>
                <a href="/commission/sessions" class="card-action">Toutes les sessions</a>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($sessions as $session): ?>
                    <div class="list-item">
                        <div class="list-item-content">
                            <div class="list-item-title"><?= htmlspecialchars($session['nom']) ?></div>
                            <div class="list-item-meta">
                                <?= $session['rapports'] ?> rapports • <?= $session['votes'] ?> votes
                            </div>
                        </div>
                        <span class="badge badge--<?= $session['etat'] === 'En cours' ? 'warning' : 'success' ?>">
                            <?= htmlspecialchars($session['etat']) ?>
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
                    <a href="/commission/sessions/create" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        <span>Nouvelle session</span>
                    </a>
                    <a href="/commission/evaluations" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 11 12 14 22 4"/>
                        </svg>
                        <span>Évaluer rapports</span>
                    </a>
                    <a href="/commission/votes" class="quick-action-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"/>
                        </svg>
                        <span>Voter</span>
                    </a>
                    <a href="/commission/archives" class="quick-action-btn">
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
