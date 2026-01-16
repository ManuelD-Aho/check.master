<?php
declare(strict_types=1);

$title = 'Backup';
$pageTitle = 'Sauvegarde de Base de Données';
$currentPage = 'admin-backup';
$breadcrumbs = [
    ['label' => 'Admin', 'url' => '/admin'],
    ['label' => 'Backup', 'url' => '']
];

ob_start();
?>

<div class="page-header">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <p>Page en construction</p>
</div>

<?php
$content = ob_get_clean();
$parts = explode(DIRECTORY_SEPARATOR, __DIR__);
$modulesIndex = array_search('modules', $parts);
$depth = $modulesIndex !== false ? count($parts) - $modulesIndex : 3;
require __DIR__ . '/../layouts/app.php';
