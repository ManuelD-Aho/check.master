<?php

declare(strict_types=1);

/**
 * CheckMaster - Détails d'une Session de Commission
 */

$title = 'Détails Session';
$pageTitle = 'Session Master 2 - Janvier 2024';
$currentPage = 'commission-sessions';
$breadcrumbs = [
    ['label' => 'Commission', 'url' => '/modules/commission'],
    ['label' => 'Sessions', 'url' => '/modules/commission/sessions'],
    ['label' => 'Détails', 'url' => '']
];

// Données de la session
$session = [
    'id' => 1,
    'nom' => 'Session Master 2 - Janvier 2024',
    'date_debut' => '2024-01-15',
    'date_fin' => '2024-01-31',
    'etat' => 'En cours',
    'president' => 'Prof. Martin DUBOIS',
    'description' => 'Évaluation des mémoires de Master 2 - Promotion 2024',
    'note_minimale' => 10,
    'quorum_vote' => 50,
    'notification_auto' => true,
    'vote_anonyme' => false,
    'date_creation' => '2024-01-10 14:30:00'
];

// Rapports assignés
$rapports = [
    ['id' => 1, 'etudiant' => 'Alice MARTIN', 'titre' => 'Intelligence Artificielle et apprentissage profond', 'statut' => 'Évalué', 'note' => 15.5, 'date_soumission' => '2024-01-12'],
    ['id' => 2, 'etudiant' => 'Bob DURAND', 'titre' => 'Blockchain et cryptomonnaies', 'statut' => 'En cours', 'note' => null, 'date_soumission' => '2024-01-13'],
    ['id' => 3, 'etudiant' => 'Claire PETIT', 'titre' => 'Cloud Computing et conteneurisation', 'statut' => 'Évalué', 'note' => 14.0, 'date_soumission' => '2024-01-12'],
    ['id' => 4, 'etudiant' => 'David BERNARD', 'titre' => 'Sécurité informatique et cyberdéfense', 'statut' => 'En attente', 'note' => null, 'date_soumission' => '2024-01-14'],
    ['id' => 5, 'etudiant' => 'Emma ROUX', 'titre' => 'Big Data et analyse prédictive', 'statut' => 'Évalué', 'note' => 16.0, 'date_soumission' => '2024-01-11'],
];

// Membres de la commission
$membres = [
    ['id' => 1, 'nom' => 'Prof. Martin DUBOIS', 'role' => 'Président', 'specialite' => 'Informatique', 'rapports_assignes' => 5, 'rapports_evalues' => 3],
    ['id' => 2, 'nom' => 'Prof. Sophie LAURENT', 'role' => 'Membre', 'specialite' => 'Mathématiques', 'rapports_assignes' => 4, 'rapports_evalues' => 2],
    ['id' => 3, 'nom' => 'Dr. Jean BERNARD', 'role' => 'Membre', 'specialite' => 'Physique', 'rapports_assignes' => 3, 'rapports_evalues' => 1],
    ['id' => 4, 'nom' => 'Dr. Marie PETIT', 'role' => 'Membre', 'specialite' => 'Chimie', 'rapports_assignes' => 4, 'rapports_evalues' => 2],
];

// Statistiques
$stats = [
    'total_rapports' => 24,
    'evalues' => 15,
    'en_cours' => 5,
    'en_attente' => 4,
    'taux_completion' => 62.5,
    'note_moyenne' => 14.2
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($session['nom'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description"><?= htmlspecialchars($session['description'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="page-actions">
        <?php if ($session['etat'] === 'En cours'): ?>
        <button class="btn btn-warning">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Clôturer la session
        </button>
        <?php endif; ?>
        <a href="/modules/commission/sessions" class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la liste
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-100 text-blue-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Rapports</div>
            <div class="stat-value"><?= $stats['total_rapports'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-green-100 text-green-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Évalués</div>
            <div class="stat-value"><?= $stats['evalues'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange-100 text-orange-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">En Cours</div>
            <div class="stat-value"><?= $stats['en_cours'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-purple-100 text-purple-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Note Moyenne</div>
            <div class="stat-value"><?= number_format($stats['note_moyenne'], 1) ?>/20</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Informations générales -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="card-title">Informations de la Session</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Président</span>
                        <span class="info-value"><?= htmlspecialchars($session['president'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">État</span>
                        <span class="badge badge-success"><?= htmlspecialchars($session['etat'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de début</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($session['date_debut'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de fin</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($session['date_fin'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Note minimale</span>
                        <span class="info-value"><?= $session['note_minimale'] ?>/20</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Quorum vote</span>
                        <span class="info-value"><?= $session['quorum_vote'] ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rapports assignés -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="card-title">Rapports Assignés (<?= count($rapports) ?>)</h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Titre du Rapport</th>
                            <th>Date Soumission</th>
                            <th>Statut</th>
                            <th>Note</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rapports as $rapport): ?>
                        <tr>
                            <td>
                                <div class="font-medium text-gray-900">
                                    <?= htmlspecialchars($rapport['etudiant'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-700">
                                    <?= htmlspecialchars($rapport['titre'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($rapport['date_soumission'])) ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($rapport['statut']) {
                                    'Évalué' => 'badge-success',
                                    'En cours' => 'badge-warning',
                                    'En attente' => 'badge-secondary',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($rapport['statut'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($rapport['note'] !== null): ?>
                                    <span class="font-medium text-green-600"><?= number_format($rapport['note'], 1) ?>/20</span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <a href="/modules/commission/evaluations/show?id=<?= $rapport['id'] ?>" 
                                   class="btn-action" 
                                   title="Voir">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Colonne latérale (1/3) -->
    <div class="space-y-6">
        <!-- Progression -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Progression</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600">Évaluations complétées</span>
                        <span class="text-sm font-medium"><?= round($stats['taux_completion']) ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $stats['taux_completion'] ?>%"></div>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Évalués</span>
                        <span class="font-medium text-green-600"><?= $stats['evalues'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">En cours</span>
                        <span class="font-medium text-orange-600"><?= $stats['en_cours'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">En attente</span>
                        <span class="font-medium text-gray-600"><?= $stats['en_attente'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membres de la commission -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Membres de la Commission</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <?php foreach ($membres as $membre): ?>
                    <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">
                                    <?= htmlspecialchars($membre['nom'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= htmlspecialchars($membre['specialite'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php if ($membre['role'] === 'Président'): ?>
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                    Président
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-between text-xs text-gray-600">
                            <span><?= $membre['rapports_evalues'] ?>/<?= $membre['rapports_assignes'] ?> évalués</span>
                            <span><?= round(($membre['rapports_evalues'] / $membre['rapports_assignes']) * 100) ?>%</span>
                        </div>
                        <div class="mt-1 progress-bar h-1">
                            <div class="progress-fill" style="width: <?= round(($membre['rapports_evalues'] / $membre['rapports_assignes']) * 100) ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/app.php';
