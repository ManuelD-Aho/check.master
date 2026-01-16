<?php
declare(strict_types=1);

$title = 'Permissions';
$pageTitle = 'Gestion des Permissions';
$currentPage = 'admin-permissions';
$breadcrumbs = [
    ['label' => 'Admin', 'url' => '/admin'],
    ['label' => 'Permissions', 'url' => '']
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
