<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Check Master'); ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <?php if (isset($customCss)): ?>
        <?php foreach ($customCss as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php include TEMPLATE_DIR . '/components/flash.php'; ?>
    <?php endif; ?>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <?php if (isset($customJs)): ?>
        <?php foreach ($customJs as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
