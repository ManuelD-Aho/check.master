<?php

declare(strict_types=1);

$title = 'Checklist Rapport';
$pageTitle = 'Checklist de Validation';
$currentPage = 'communication-rapports';
$breadcrumbs = [
    ['label' => 'Communication', 'url' => '/modules/communication'],
    ['label' => 'Rapports', 'url' => '/modules/communication/rapports'],
    ['label' => 'Checklist', 'url' => '']
];

$rapport = [
    'id' => 1,
    'titre' => 'Intelligence Artificielle et Deep Learning',
    'etudiant' => 'Alice MARTIN',
    'statut' => 'En révision'
];

$checklist = [
    'Structure' => [
        ['item' => 'Page de garde conforme', 'valide' => true, 'commentaire' => 'Tous les éléments présents'],
        ['item' => 'Table des matières complète', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Résumé et abstract', 'valide' => true, 'commentaire' => 'Bilingue français/anglais'],
        ['item' => 'Introduction claire', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Développement structuré', 'valide' => true, 'commentaire' => '3 chapitres bien organisés'],
        ['item' => 'Conclusion pertinente', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Bibliographie complète', 'valide' => false, 'commentaire' => 'Manque 2 références citées'],
        ['item' => 'Annexes numérotées', 'valide' => true, 'commentaire' => ''],
    ],
    'Contenu' => [
        ['item' => 'Problématique clairement définie', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Revue de littérature exhaustive', 'valide' => true, 'commentaire' => '42 références'],
        ['item' => 'Méthodologie détaillée', 'valide' => true, 'commentaire' => 'Approche expérimentale bien décrite'],
        ['item' => 'Résultats présentés clairement', 'valide' => true, 'commentaire' => 'Graphiques et tableaux'],
        ['item' => 'Discussion approfondie', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Apport scientifique démontré', 'valide' => true, 'commentaire' => 'Contribution originale'],
    ],
    'Forme' => [
        ['item' => 'Orthographe et grammaire correctes', 'valide' => false, 'commentaire' => 'Quelques fautes à corriger'],
        ['item' => 'Style académique approprié', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Figures et tableaux légendés', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Citations conformes aux normes', 'valide' => true, 'commentaire' => 'Norme APA respectée'],
        ['item' => 'Pagination continue', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Mise en page professionnelle', 'valide' => true, 'commentaire' => ''],
    ],
    'Aspects Techniques' => [
        ['item' => 'Fichier PDF conforme', 'valide' => true, 'commentaire' => 'PDF/A-1b'],
        ['item' => 'Taille fichier acceptable', 'valide' => true, 'commentaire' => '8.2 Mo'],
        ['item' => 'Métadonnées renseignées', 'valide' => true, 'commentaire' => ''],
        ['item' => 'Liens hypertextes fonctionnels', 'valide' => true, 'commentaire' => ''],
    ],
];

// Calcul du pourcentage
$total_items = 0;
$valides = 0;
foreach ($checklist as $items) {
    foreach ($items as $item) {
        $total_items++;
        if ($item['valide']) $valides++;
    }
}
$pourcentage = round(($valides / $total_items) * 100);

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">
            Rapport: <?= htmlspecialchars($rapport['titre'], ENT_QUOTES, 'UTF-8') ?> 
            par <?= htmlspecialchars($rapport['etudiant'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <div class="page-actions">
        <a href="/modules/communication/rapports/show?id=<?= $rapport['id'] ?>" class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au rapport
        </a>
        <button class="btn btn-primary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Valider la checklist
        </button>
    </div>
</div>

<!-- Progression globale -->
<div class="dashboard-card mb-6">
    <div class="card-body">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-gray-900">Progression Globale</h3>
            <span class="text-2xl font-bold text-blue-600"><?= $pourcentage ?>%</span>
        </div>
        <div class="progress-bar h-4">
            <div class="progress-fill" style="width: <?= $pourcentage ?>%"></div>
        </div>
        <div class="mt-3 flex justify-between text-sm text-gray-600">
            <span><?= $valides ?> / <?= $total_items ?> éléments validés</span>
            <span><?= $total_items - $valides ?> élément(s) à corriger</span>
        </div>
    </div>
</div>

<!-- Checklist détaillée -->
<?php foreach ($checklist as $categorie => $items): ?>
<div class="dashboard-card mb-6">
    <div class="card-header">
        <h2 class="card-title"><?= htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="text-sm text-gray-600">
            <?php
            $cat_valides = array_filter($items, fn($i) => $i['valide']);
            $cat_total = count($items);
            $cat_pourcent = round((count($cat_valides) / $cat_total) * 100);
            ?>
            <?= count($cat_valides) ?> / <?= $cat_total ?> (<?= $cat_pourcent ?>%)
        </div>
    </div>
    <div class="card-body">
        <div class="space-y-3">
            <?php foreach ($items as $index => $item): ?>
            <div class="flex items-start p-3 rounded-lg <?= $item['valide'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' ?>">
                <div class="flex-shrink-0 mr-3 mt-0.5">
                    <?php if ($item['valide']): ?>
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <?php else: ?>
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <div class="font-medium <?= $item['valide'] ? 'text-green-900' : 'text-red-900' ?>">
                        <?= htmlspecialchars($item['item'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php if (!empty($item['commentaire'])): ?>
                        <div class="text-sm <?= $item['valide'] ? 'text-green-700' : 'text-red-700' ?> mt-1">
                            <?= htmlspecialchars($item['commentaire'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-shrink-0 ml-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500" 
                               <?= $item['valide'] ? 'checked' : '' ?>
                               onchange="toggleChecklistItem(<?= $rapport['id'] ?>, '<?= htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8') ?>', <?= $index ?>)">
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Commentaires généraux -->
<div class="dashboard-card">
    <div class="card-header">
        <h2 class="card-title">Commentaires Généraux</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="/modules/communication/rapports/checklist/save">
            <input type="hidden" name="rapport_id" value="<?= $rapport['id'] ?>">
            <div class="form-group">
                <label for="commentaire_global" class="form-label">Observations et recommandations</label>
                <textarea id="commentaire_global" 
                          name="commentaire_global" 
                          class="form-control" 
                          rows="6"
                          placeholder="Saisir vos commentaires généraux sur le rapport...">Le rapport présente une bonne qualité globale. Quelques corrections mineures sont nécessaires au niveau de la bibliographie et de l'orthographe. Le contenu scientifique est solide et bien structuré.</textarea>
            </div>
            <div class="form-actions">
                <button type="submit" name="action" value="save" class="btn btn-secondary">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Enregistrer
                </button>
                <button type="submit" name="action" value="validate" class="btn btn-primary">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Valider et publier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleChecklistItem(rapportId, categorie, index) {
    console.log('Toggle item:', rapportId, categorie, index);
    // TODO: Appel AJAX pour sauvegarder l'état
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/app.php';
