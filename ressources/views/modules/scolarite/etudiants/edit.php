<?php

declare(strict_types=1);

$title = 'Modifier Étudiant';
$pageTitle = 'Modifier Étudiant';
$currentPage = 'scolarite-etudiants';
$breadcrumbs = [
    ['label' => 'Scolarite', 'url' => '/modules/scolarite'],
    ['label' => 'Etudiants', 'url' => '']
];

// Données de démonstration
$items = [
    ['id' => 1, 'nom' => 'Élément 1', 'statut' => 'Actif', 'date' => '2024-01-15', 'valeur' => '100'],
    ['id' => 2, 'nom' => 'Élément 2', 'statut' => 'En cours', 'date' => '2024-01-14', 'valeur' => '200'],
    ['id' => 3, 'nom' => 'Élément 3', 'statut' => 'Actif', 'date' => '2024-01-13', 'valeur' => '150'],
    ['id' => 4, 'nom' => 'Élément 4', 'statut' => 'Terminé', 'date' => '2024-01-12', 'valeur' => '300'],
    ['id' => 5, 'nom' => 'Élément 5', 'statut' => 'Actif', 'date' => '2024-01-11', 'valeur' => '250'],
    ['id' => 6, 'nom' => 'Élément 6', 'statut' => 'En attente', 'date' => '2024-01-10', 'valeur' => '180'],
];

$stats = [
    'total' => count($items),
    'actifs' => 3,
    'en_cours' => 1,
    'termines' => 1,
    'en_attente' => 1
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">Modification des informations étudiant</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exporter
        </button>
        <a href="/modules/scolarite/etudiants/create" class="btn btn-primary">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Terminés</div>
            <div class="stat-value"><?= $stats['termines'] ?></div>
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
                        <option value="en_cours">En cours</option>
                        <option value="termine">Terminé</option>
                        <option value="en_attente">En attente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" id="date_debut" name="date_debut" class="form-control">
                </div>
                <div class="form-group">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" id="date_fin" name="date_fin" class="form-control">
                </div>
                <div class="form-group">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Rechercher...">
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
        <h2 class="card-title">Liste des Éléments</h2>
        <div class="card-actions">
            <button class="btn-icon" title="Rafraîchir">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
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
                    <th>Valeur</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <span class="font-mono text-sm">#<?= str_pad((string)$item['id'], 4, '0', STR_PAD_LEFT) ?></span>
                    </td>
                    <td>
                        <div class="font-medium text-gray-900">
                            <?= htmlspecialchars($item['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <?php
                        $badgeClass = match($item['statut']) {
                            'Actif' => 'badge-success',
                            'En cours' => 'badge-warning',
                            'Terminé' => 'badge-info',
                            'En attente' => 'badge-secondary',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($item['statut'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td>
                        <span class="text-sm text-gray-700">
                            <?= date('d/m/Y', strtotime($item['date'])) ?>
                        </span>
                    </td>
                    <td>
                        <span class="font-medium text-blue-600">
                            <?= number_format((float)$item['valeur'], 0, ',', ' ') ?> FCFA
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="action-buttons">
                            <a href="/modules/scolarite/etudiants/show?id=<?= $item['id'] ?>" 
                               class="btn-action" 
                               title="Voir les détails">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/modules/scolarite/etudiants/edit?id=<?= $item['id'] ?>" 
                               class="btn-action" 
                               title="Modifier">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button class="btn-action text-red-600" 
                                    title="Supprimer"
                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) { window.location.href='/modules/scolarite/etudiants/delete?id=<?= $item['id'] ?>'; }">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
        Affichage de <strong>1</strong> à <strong><?= count($items) ?></strong> sur <strong><?= count($items) ?></strong> éléments
    </div>
    <div class="pagination">
        <button class="pagination-btn" disabled>
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Précédent
        </button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn">2</button>
        <button class="pagination-btn">3</button>
        <button class="pagination-btn">
            Suivant
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

<?php
$content = ob_get_clean();
$depth = substr_count('etudiants', '/') + 2;
require __DIR__ . '/' . str_repeat('../', $depth) . 'layouts/app.php';
