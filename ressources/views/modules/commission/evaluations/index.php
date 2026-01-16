<?php

declare(strict_types=1);

$title = 'Évaluations de Rapports';
$pageTitle = 'Liste des Rapports à Évaluer';
$currentPage = 'commission-evaluations';
$breadcrumbs = [
    ['label' => 'Commission', 'url' => '/modules/commission'],
    ['label' => 'Évaluations', 'url' => '']
];

$rapports = [
    ['id' => 1, 'etudiant' => 'Alice MARTIN', 'matricule' => 'M2024001', 'titre' => 'Intelligence Artificielle et apprentissage profond', 'date_soumission' => '2024-01-12', 'statut' => 'En attente', 'priorite' => 'haute'],
    ['id' => 2, 'etudiant' => 'Bob DURAND', 'matricule' => 'M2024002', 'titre' => 'Blockchain et cryptomonnaies', 'date_soumission' => '2024-01-13', 'statut' => 'En cours', 'priorite' => 'normale'],
    ['id' => 3, 'etudiant' => 'Claire PETIT', 'matricule' => 'M2024003', 'titre' => 'Cloud Computing et conteneurisation', 'date_soumission' => '2024-01-12', 'statut' => 'Évalué', 'priorite' => 'normale'],
    ['id' => 4, 'etudiant' => 'David BERNARD', 'matricule' => 'M2024004', 'titre' => 'Sécurité informatique et cyberdéfense', 'date_soumission' => '2024-01-14', 'statut' => 'En attente', 'priorite' => 'haute'],
    ['id' => 5, 'etudiant' => 'Emma ROUX', 'matricule' => 'M2024005', 'titre' => 'Big Data et analyse prédictive', 'date_soumission' => '2024-01-11', 'statut' => 'Évalué', 'priorite' => 'normale'],
    ['id' => 6, 'etudiant' => 'François GARCIA', 'matricule' => 'M2024006', 'titre' => 'IoT et systèmes embarqués', 'date_soumission' => '2024-01-15', 'statut' => 'En attente', 'priorite' => 'normale'],
    ['id' => 7, 'etudiant' => 'Gabrielle SIMON', 'matricule' => 'M2024007', 'titre' => 'Machine Learning pour la santé', 'date_soumission' => '2024-01-13', 'statut' => 'En cours', 'priorite' => 'haute'],
    ['id' => 8, 'etudiant' => 'Henri MOREL', 'matricule' => 'M2024008', 'titre' => 'DevOps et intégration continue', 'date_soumission' => '2024-01-14', 'statut' => 'En attente', 'priorite' => 'normale'],
];

$stats = [
    'total' => count($rapports),
    'en_attente' => 4,
    'en_cours' => 2,
    'evalues' => 2,
    'delai_moyen' => 3.5
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">Évaluation des mémoires et rapports soumis</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exporter
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
        <div class="stat-icon bg-orange-100 text-orange-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">En Attente</div>
            <div class="stat-value"><?= $stats['en_attente'] ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-yellow-100 text-yellow-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">En Cours</div>
            <div class="stat-value"><?= $stats['en_cours'] ?></div>
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
</div>

<div class="dashboard-card mb-6">
    <div class="card-body">
        <form method="GET" action="" class="filters-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="statut" class="form-label">Statut</label>
                    <select id="statut" name="statut" class="form-control">
                        <option value="">Tous</option>
                        <option value="en_attente">En attente</option>
                        <option value="en_cours">En cours</option>
                        <option value="evalue">Évalué</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="priorite" class="form-label">Priorité</label>
                    <select id="priorite" name="priorite" class="form-control">
                        <option value="">Toutes</option>
                        <option value="haute">Haute</option>
                        <option value="normale">Normale</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Nom, titre...">
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
        <h2 class="card-title">Rapports à Évaluer</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Titre du Rapport</th>
                    <th>Date Soumission</th>
                    <th>Priorité</th>
                    <th>Statut</th>
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
                        <div class="text-sm text-gray-500">
                            <?= htmlspecialchars($rapport['matricule'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm text-gray-700 max-w-md">
                            <?= htmlspecialchars($rapport['titre'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </td>
                    <td>
                        <?= date('d/m/Y', strtotime($rapport['date_soumission'])) ?>
                    </td>
                    <td>
                        <?php if ($rapport['priorite'] === 'haute'): ?>
                            <span class="badge badge-danger">Haute</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Normale</span>
                        <?php endif; ?>
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
                    <td class="text-right">
                        <div class="action-buttons">
                            <a href="/modules/commission/evaluations/show?id=<?= $rapport['id'] ?>" 
                               class="btn-action" 
                               title="Évaluer">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/modules/commission/evaluations/annotations?id=<?= $rapport['id'] ?>" 
                               class="btn-action" 
                               title="Annotations">
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
        Affichage de <strong>1</strong> à <strong>8</strong> sur <strong>8</strong> rapports
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
