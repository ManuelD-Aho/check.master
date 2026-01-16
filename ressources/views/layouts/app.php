<?php

declare(strict_types=1);
use Src\Support\CSRF;
/**
 * CheckMaster - Main Application Layout
 * =======================================
 * Layout principal avec sidebar et header
 * 
 * Variables attendues:
 * @var string $title         - Titre de la page
 * @var string $pageTitle     - Titre affiché dans le header
 * @var array  $breadcrumbs   - Fil d'Ariane [['label' => '', 'url' => '']]
 * @var string $currentPage   - Page courante pour le menu actif
 * @var array  $user          - Utilisateur connecté
 */

// Valeurs par défaut
$title = $title ?? 'CheckMaster';
$pageTitle = $pageTitle ?? '';
$breadcrumbs = $breadcrumbs ?? [];
$currentPage = $currentPage ?? '';
$user = $user ?? ['name' => 'Utilisateur', 'role' => 'Rôle', 'initials' => 'U'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | CheckMaster</title>

    <!-- Preconnect for fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <!-- CSRF Meta -->
    <?= CSRF::meta() ?? '' ?>

    <!-- Additional head content -->
    <?php if (isset($headContent)) echo $headContent; ?>
</head>

<body>
    <div class="app-layout">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Sidebar backdrop (mobile) -->
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <!-- Main Content Area -->
        <main class="app-main">
            <!-- Header -->
            <?php include __DIR__ . '/../partials/header.php'; ?>

            <!-- Page Content -->
            <div class="page-content">
                <!-- Flash Messages -->
                <?php include __DIR__ . '/../partials/flash-messages.php'; ?>

                <!-- Main Content -->
                <?php if (isset($content)) echo $content; ?>
            </div>

            <!-- Footer -->
            <?php include __DIR__ . '/../partials/footer.php'; ?>
        </main>
    </div>

    <!-- Scripts -->
    <script src="<?= asset('js/app.js') ?>" defer></script>

    <script>
        (function() {
            var basePath = '<?= \Src\Http\Request::basePath(); ?>';
            if (!basePath) return;

            var prefix = basePath.charAt(0) === '/' ? basePath : '/' + basePath;
            var shouldPrefix = function(url) {
                if (!url) return false;
                if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//')) return false;
                if (!url.startsWith('/')) return false;
                if (url === prefix || url.startsWith(prefix + '/')) return false;
                return true;
            };

            var elements = document.querySelectorAll('a[href], link[href], form[action]');
            elements.forEach(function(el) {
                var attr = el.tagName === 'FORM' ? 'action' : 'href';
                var value = el.getAttribute(attr);
                if (!shouldPrefix(value)) return;
                el.setAttribute(attr, prefix + value);
            });
        })();
    </script>

    <!-- Additional scripts -->
    <?php if (isset($scripts)) echo $scripts; ?>
</body>

</html>
