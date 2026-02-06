<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Admin'); ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
    <?php if (isset($customCss)): ?>
        <?php foreach ($customCss as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-layout">
    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php include TEMPLATE_DIR . '/components/flash.php'; ?>
    <?php endif; ?>

    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h1>Check Master</h1>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/admin/dashboard">Tableau de bord</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/users">Utilisateurs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/etudiants">Étudiants</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/encadreurs">Encadreurs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/commissions">Commissions</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/rapports">Rapports</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/settings">Paramètres</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <p><?php echo htmlspecialchars($currentUser['nom'] ?? 'Admin'); ?></p>
                <a href="<?php echo BASE_URL; ?>/logout">Déconnexion</a>
            </div>
        </aside>

        <main class="admin-content">
            <?php echo $content ?? ''; ?>
        </main>
    </div>

    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
    <?php if (isset($customJs)): ?>
        <?php foreach ($customJs as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
