<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Authentification'); ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/auth.css">
    <?php if (isset($customCss)): ?>
        <?php foreach ($customCss as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="auth-layout">
    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php include TEMPLATE_DIR . '/components/flash.php'; ?>
    <?php endif; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Check Master</h1>
            </div>

            <?php echo $content ?? ''; ?>

            <div class="auth-footer">
                <p>&copy; <?php echo date('Y'); ?> Check Master</p>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <?php if (isset($customJs)): ?>
        <?php foreach ($customJs as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
