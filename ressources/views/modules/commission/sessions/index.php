<?php

declare(strict_types=1);

/**
 * CheckMaster - Liste des Sessions de Commission
 * 
 * Affiche la liste complète des sessions de commission avec leurs informations
 * et permet de gérer (créer, consulter, archiver) les sessions.
 */

// Variables pour le layout
$title = 'Sessions de Commission';
$pageTitle = 'Gestion des Sessions';
$currentPage = 'commission-sessions';
$breadcrumbs = [
    ['label' => 'Commission', 'url' => '/modules/commission'],
    ['label' => 'Sessions', 'url' => '']
];

// Données de démonstration - Sessions
$sessions = [
    [
        'id' => 1,
        'nom' => 'Session Master 2 - Janvier 2024',
        'date_debut' => '2024-01-15',
        'date_fin' => '2024-01-31',
        'etat' => 'En cours',
        'nb_rapports' => 24,
        'nb_evalues' => 15,
        'nb_votes' => 8,
        'president' => 'Prof. Martin DUBOIS',
        'description' => 'Évaluation des mémoires de Master 2 - Promotion 2024'
    ],
    [
        'id' => 2,
        'nom' => 'Session Master 1 - Janvier 2024',
        'date_debut' => '2024-01-20',
        'date_fin' => '2024-02-10',
        'etat' => 'Planifiée',
        'nb_rapports' => 32,
        'nb_evalues' => 0,
        'nb_votes' => 0,
        'president' => 'Prof. Sophie LAURENT',
        'description' => 'Évaluation des mémoires de Master 1 - Promotion 2024'
    ],
    [
        'id' => 3,
        'nom' => 'Session Doctorat - Décembre 2023',
        'date_debut' => '2023-12-01',
        'date_fin' => '2023-12-20',
        'etat' => 'Clôturée',
        'nb_rapports' => 8,
        'nb_evalues' => 8,
        'nb_votes' => 8,
        'president' => 'Prof. Jean BERNARD',
        'description' => 'Soutenance des thèses de doctorat - Session automne 2023'
    ],
    [
        'id' => 4,
        'nom' => 'Session Rattrapage - Novembre 2023',
        'date_debut' => '2023-11-15',
        'date_fin' => '2023-11-30',
        'etat' => 'Clôturée',
        'nb_rapports' => 5,
        'nb_evalues' => 5,
        'nb_votes' => 5,
        'president' => 'Prof. Marie PETIT',
        'description' => 'Session de rattrapage pour les étudiants ajournés'
    ],
    [
        'id' => 5,
        'nom' => 'Session Master 2 - Juin 2023',
        'date_debut' => '2023-06-01',
        'date_fin' => '2023-06-30',
        'etat' => 'Archivée',
        'nb_rapports' => 28,
        'nb_evalues' => 28,
        'nb_votes' => 28,
        'president' => 'Prof. Martin DUBOIS',
        'description' => 'Évaluation des mémoires de Master 2 - Promotion 2023'
    ]
];

// Statistiques globales
$stats = [
    'total_sessions' => count($sessions),
    'sessions_actives' => 2,
    'rapports_en_attente' => 41,
    'evaluations_completees' => 28
];

// Démarrer la capture du contenu
ob_start();
?>

<!-- En-tête de page -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">Gestion et suivi des sessions d'évaluation de la commission</p>
    </div>
    <div class="page-actions">
        <a href="/modules/commission/sessions/create" class="btn btn-primary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Créer une session
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-blue-100 text-blue-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Sessions Totales</div>
            <div class="stat-value"><?= $stats['total_sessions'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-green-100 text-green-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Sessions Actives</div>
            <div class="stat-value"><?= $stats['sessions_actives'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange-100 text-orange-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Rapports en Attente</div>
            <div class="stat-value"><?= $stats['rapports_en_attente'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-purple-100 text-purple-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Évaluations Complétées</div>
            <div class="stat-value"><?= $stats['evaluations_completees'] ?></div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="dashboard-card mb-6">
    <div class="card-body">
        <form method="GET" action="" class="filters-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="etat" class="form-label">État</label>
                    <select id="etat" name="etat" class="form-control">
                        <option value="">Tous les états</option>
                        <option value="planifiee">Planifiée</option>
                        <option value="en_cours">En cours</option>
                        <option value="cloturee">Clôturée</option>
                        <option value="archivee">Archivée</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="annee" class="form-label">Année</label>
                    <select id="annee" name="annee" class="form-control">
                        <option value="">Toutes les années</option>
                        <option value="2024" selected>2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Nom de session...">
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn btn-secondary">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste des sessions -->
<div class="dashboard-card">
    <div class="card-header">
        <h2 class="card-title">Sessions de Commission</h2>
        <div class="card-actions">
            <button class="btn-icon" title="Exporter">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Session</th>
                    <th>Période</th>
                    <th>État</th>
                    <th>Rapports</th>
                    <th>Évalués</th>
                    <th>Votes</th>
                    <th>Président</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                <tr>
                    <td>
                        <div class="font-medium text-gray-900">
                            <?= htmlspecialchars($session['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="text-sm text-gray-500">
                            <?= htmlspecialchars($session['description'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm">
                            <div><?= date('d/m/Y', strtotime($session['date_debut'])) ?></div>
                            <div class="text-gray-500">au <?= date('d/m/Y', strtotime($session['date_fin'])) ?></div>
                        </div>
                    </td>
                    <td>
                        <?php
                        $badgeClass = match($session['etat']) {
                            'En cours' => 'badge-success',
                            'Planifiée' => 'badge-info',
                            'Clôturée' => 'badge-warning',
                            'Archivée' => 'badge-secondary',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($session['etat'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td>
                        <span class="font-medium"><?= $session['nb_rapports'] ?></span>
                    </td>
                    <td>
                        <div class="flex items-center">
                            <span class="font-medium"><?= $session['nb_evalues'] ?></span>
                            <span class="text-gray-500 text-sm ml-1">/ <?= $session['nb_rapports'] ?></span>
                        </div>
                        <?php 
                        $pourcentage = $session['nb_rapports'] > 0 
                            ? round(($session['nb_evalues'] / $session['nb_rapports']) * 100) 
                            : 0;
                        ?>
                        <div class="progress-bar mt-1">
                            <div class="progress-fill" style="width: <?= $pourcentage ?>%"></div>
                        </div>
                    </td>
                    <td>
                        <span class="font-medium"><?= $session['nb_votes'] ?></span>
                    </td>
                    <td>
                        <div class="text-sm text-gray-900">
                            <?= htmlspecialchars($session['president'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="action-buttons">
                            <a href="/modules/commission/sessions/show?id=<?= $session['id'] ?>" 
                               class="btn-action" 
                               title="Détails">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <?php if ($session['etat'] !== 'Archivée'): ?>
                            <a href="/modules/commission/evaluations/index?session=<?= $session['id'] ?>" 
                               class="btn-action" 
                               title="Évaluations">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="pagination-container">
    <div class="pagination-info">
        Affichage de <strong>1</strong> à <strong>5</strong> sur <strong>5</strong> sessions
    </div>
    <div class="pagination">
        <button class="pagination-btn" disabled>Précédent</button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn" disabled>Suivant</button>
    </div>
</div>

<?php
// Capturer le contenu
$content = ob_get_clean();

// Inclure le layout principal
require __DIR__ . '/../../../layouts/app.php';
