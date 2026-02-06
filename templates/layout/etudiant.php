<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Espace Étudiant'); ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/etudiant.css">
    <?php if (isset($customCss)): ?>
        <?php foreach ($customCss as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="etudiant-layout">
    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php include TEMPLATE_DIR . '/components/flash.php'; ?>
    <?php endif; ?>

    <header class="etudiant-header">
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>/etudiant/dashboard">Check Master</a>
            </div>

            <nav class="etudiant-nav">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/etudiant/dashboard">Tableau de bord</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/etudiant/rapports">Mes rapports</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/etudiant/commissions">Commissions</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/etudiant/profil">Mon profil</a></li>
                </ul>
            </nav>

            <div class="header-user">
                <span><?php echo htmlspecialchars($currentUser['prenom'] ?? 'Étudiant'); ?></span>
                <a href="<?php echo BASE_URL; ?>/logout">Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="etudiant-content">
        <?php echo $content ?? ''; ?>
    </main>

    <footer class="etudiant-footer">
        <p>&copy; <?php echo date('Y'); ?> Check Master. Tous droits réservés.</p>
    </footer>

    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/etudiant.js"></script>
    <?php if (isset($customJs)): ?>
        <?php foreach ($customJs as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
