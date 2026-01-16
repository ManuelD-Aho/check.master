<?php

declare(strict_types=1);

$title = 'Paramètres Système';
$pageTitle = 'Paramètres Système';
$currentPage = 'admin-parametres';
$breadcrumbs = [
    ['label' => 'Admin', 'url' => '/modules/admin'],
    ['label' => 'Parametres', 'url' => '']
];

// Données de démonstration
$items = [
    ['id' => 1, 'nom' => 'Exemple 1', 'statut' => 'Actif', 'date' => '2024-01-15'],
    ['id' => 2, 'nom' => 'Exemple 2', 'statut' => 'Inactif', 'date' => '2024-01-14'],
    ['id' => 3, 'nom' => 'Exemple 3', 'statut' => 'Actif', 'date' => '2024-01-13'],
    ['id' => 4, 'nom' => 'Exemple 4', 'statut' => 'En attente', 'date' => '2024-01-12'],
    ['id' => 5, 'nom' => 'Exemple 5', 'statut' => 'Actif', 'date' => '2024-01-11'],
];

$stats = [
    'total' => count($items),
    'actifs' => 3,
    'inactifs' => 1,
    'en_attente' => 1
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">Configuration générale du système</p>
    </div>
    <div class="page-actions">
        <a href="/modules/admin/parametres/create" class="btn btn-primary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau
        </a>
    </div>
</div>

<div class="stats-grid mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-blue-100 text-blue-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total</div>
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
            <div class="stat-label">Actifs</div>
            <div class="stat-value"><?= $stats['actifs'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-gray-100 text-gray-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Inactifs</div>
            <div class="stat-value"><?= $stats['inactifs'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-orange-100 text-orange-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">En attente</div>
            <div class="stat-value"><?= $stats['en_attente'] ?></div>
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
                        <option value="">Tous</option>
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                        <option value="en_attente">En attente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Rechercher...">
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn btn-secondary">Filtrer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h2 class="card-title">Liste</h2>
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
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td>
                        <div class="font-medium text-gray-900">
                            <?= htmlspecialchars($item['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $badgeClass = match($item['statut']) {
                            'Actif' => 'badge-success',
                            'Inactif' => 'badge-secondary',
                            'En attente' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($item['statut'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($item['date'])) ?></td>
                    <td class="text-right">
                        <div class="action-buttons">
                            <a href="/modules/admin/parametres/show?id=<?= $item['id'] ?>" 
                               class="btn-action" title="Voir">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/modules/admin/parametres/edit?id=<?= $item['id'] ?>" 
                               class="btn-action" title="Modifier">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
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
        Affichage de <strong>1</strong> à <strong><?= count($items) ?></strong> sur <strong><?= count($items) ?></strong> éléments
    </div>
    <div class="pagination">
        <button class="pagination-btn" disabled>Précédent</button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn" disabled>Suivant</button>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/' . str_repeat('../', substr_count('parametres', '/') + 2) . 'layouts/app.php';
