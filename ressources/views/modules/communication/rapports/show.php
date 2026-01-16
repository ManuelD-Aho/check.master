<?php

declare(strict_types=1);

$title = 'Détails du Rapport';
$pageTitle = 'Intelligence Artificielle et Deep Learning';
$currentPage = 'communication-rapports';
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '/modules/communication'],
    ['label' => 'Rapports', 'url' => '/modules/communication/rapports'],
    ['label' => 'Détails', 'url' => '']
];

$rapport = [
    'id' => 1,
    'titre' => 'Intelligence Artificielle et Deep Learning',
    'etudiant' => 'Alice MARTIN',
    'matricule' => 'M2024001',
    'promotion' => 'Master 2 - 2024',
    'directeur' => 'Prof. Martin DUBOIS',
    'date_soumission' => '2024-01-15',
    'date_publication' => '2024-01-18',
    'statut' => 'Publié',
    'note' => 16.5,
    'vues' => 245,
    'telechargements' => 87,
    'resume' => 'Ce mémoire explore les avancées récentes en intelligence artificielle, avec un focus particulier sur les architectures de deep learning. Il présente une analyse approfondie des réseaux de neurones convolutifs et leur application dans le traitement d\'images.',
    'mots_cles' => ['IA', 'Deep Learning', 'CNN', 'Neural Networks', 'Computer Vision']
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($rapport['titre'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">
            Par <?= htmlspecialchars($rapport['etudiant'], ENT_QUOTES, 'UTF-8') ?> 
            (<?= htmlspecialchars($rapport['matricule'], ENT_QUOTES, 'UTF-8') ?>) 
            • <?= htmlspecialchars($rapport['promotion'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <div class="page-actions">
        <a href="/modules/communication/rapports/checklist?id=<?= $rapport['id'] ?>" class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Checklist
        </a>
        <button class="btn btn-primary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger PDF
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Résumé -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2 class="card-title">Résumé</h2>
            </div>
            <div class="card-body">
                <p class="text-gray-700 leading-relaxed">
                    <?= htmlspecialchars($rapport['resume'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>

        <!-- Mots-clés -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Mots-clés</h3>
            </div>
            <div class="card-body">
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($rapport['mots_cles'] as $mot): ?>
                        <span class="badge badge-info">
                            <?= htmlspecialchars($mot, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Visualiseur PDF (placeholder) -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Aperçu du Document</h3>
            </div>
            <div class="card-body">
                <div class="bg-gray-100 rounded-lg p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 mb-4">Visualiseur PDF intégré</p>
                    <button class="btn btn-primary">
                        Charger le document
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne latérale (1/3) -->
    <div class="space-y-6">
        <!-- Informations -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Informations</h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <div class="info-item">
                        <span class="info-label">Statut</span>
                        <span class="badge badge-success"><?= htmlspecialchars($rapport['statut'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Note</span>
                        <span class="font-bold text-green-600 text-lg"><?= $rapport['note'] ?>/20</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date soumission</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($rapport['date_soumission'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date publication</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($rapport['date_publication'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Directeur</span>
                        <span class="info-value"><?= htmlspecialchars($rapport['directeur'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Statistiques</h3>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Vues</span>
                        </div>
                        <span class="font-bold text-blue-600"><?= $rapport['vues'] ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Téléchargements</span>
                        </div>
                        <span class="font-bold text-green-600"><?= $rapport['telechargements'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Actions</h3>
            </div>
            <div class="card-body">
                <div class="space-y-2">
                    <button class="btn btn-secondary w-full">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Partager
                    </button>
                    <button class="btn btn-secondary w-full">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Imprimer
                    </button>
                    <button class="btn btn-secondary w-full">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Envoyer par email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/app.php';
