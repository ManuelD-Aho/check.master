<?php

declare(strict_types=1);

$title = 'Rapports & Communications';
$pageTitle = 'Gestion des Rapports';
$currentPage = 'communication-rapports';
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '/modules/communication'],
    ['label' => 'Rapports', 'url' => '']
];

$rapports = [
    ['id' => 1, 'titre' => 'Intelligence Artificielle et Deep Learning', 'etudiant' => 'Alice MARTIN', 'date' => '2024-01-15', 'statut' => 'Publié', 'vues' => 245],
    ['id' => 2, 'titre' => 'Blockchain et Cryptomonnaies', 'etudiant' => 'Bob DURAND', 'date' => '2024-01-14', 'statut' => 'En révision', 'vues' => 128],
    ['id' => 3, 'titre' => 'Cloud Computing et Conteneurisation', 'etudiant' => 'Claire PETIT', 'date' => '2024-01-13', 'statut' => 'Publié', 'vues' => 189],
    ['id' => 4, 'titre' => 'Sécurité Informatique et Cyberdéfense', 'etudiant' => 'David BERNARD', 'date' => '2024-01-12', 'statut' => 'Brouillon', 'vues' => 0],
    ['id' => 5, 'titre' => 'Big Data et Analyse Prédictive', 'etudiant' => 'Emma ROUX', 'date' => '2024-01-11', 'statut' => 'Publié', 'vues' => 312],
];

$stats = [
    'total' => count($rapports),
    'publies' => 3,
    'en_revision' => 1,
    'brouillons' => 1,
    'vues_totales' => 874
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">Consultation et gestion des rapports de mémoire</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
            </svg>
            Exporter liste
        </button>
    </div>
</div>

<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-100 text-blue-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Rapports</div>
            <div class="stat-value"><?= $stats['total'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green-100 text-green-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Publiés</div>
            <div class="stat-value"><?= $stats['publies'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-orange-100 text-orange-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">En Révision</div>
            <div class="stat-value"><?= $stats['en_revision'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-100 text-purple-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Vues Totales</div>
            <div class="stat-value"><?= number_format($stats['vues_totales'], 0, ',', ' ') ?></div>
        </div>
    </div>
</div>

<div class="dashboard-card mb-6">
    <div class="card-body">
        <form method="GET" action="" class="filters-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="statut" class="form-label">Statut</label>
                    <select id="statut" name="statut" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="publie">Publié</option>
                        <option value="en_revision">En révision</option>
                        <option value="brouillon">Brouillon</option>
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
                    <input type="text" id="search" name="search" class="form-control" placeholder="Titre, auteur...">
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

<div class="dashboard-card">
    <div class="card-header">
        <h2 class="card-title">Rapports Disponibles</h2>
        <div class="card-actions">
            <button class="btn-icon" title="Vue grille">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </button>
            <button class="btn-icon" title="Vue liste">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Titre du Rapport</th>
                    <th>Auteur</th>
                    <th>Date Publication</th>
                    <th>Statut</th>
                    <th>Vues</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rapports as $rapport): ?>
                <tr>
                    <td>
                        <div class="font-medium text-gray-900">
                            <?= htmlspecialchars($rapport['titre'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm text-gray-700">
                            <?= htmlspecialchars($rapport['etudiant'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <?= date('d/m/Y', strtotime($rapport['date'])) ?>
                    </td>
                    <td>
                        <?php
                        $badgeClass = match($rapport['statut']) {
                            'Publié' => 'badge-success',
                            'En révision' => 'badge-warning',
                            'Brouillon' => 'badge-secondary',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($rapport['statut'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <?= $rapport['vues'] ?>
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="action-buttons">
                            <a href="/modules/communication/rapports/show?id=<?= $rapport['id'] ?>" 
                               class="btn-action" 
                               title="Consulter">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/modules/communication/rapports/checklist?id=<?= $rapport['id'] ?>" 
                               class="btn-action" 
                               title="Checklist">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </a>
                            <button class="btn-action" title="Télécharger PDF">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-container">
    <div class="pagination-info">
        Affichage de <strong>1</strong> à <strong><?= count($rapports) ?></strong> sur <strong><?= count($rapports) ?></strong> rapports
    </div>
    <div class="pagination">
        <button class="pagination-btn" disabled>Précédent</button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn" disabled>Suivant</button>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/app.php';
