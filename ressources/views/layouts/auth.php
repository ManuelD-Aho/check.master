<?php

declare(strict_types=1);
/**
 * CheckMaster - Authentication Layout
 * =====================================
 * Layout pour les pages non authentifiées (connexion, mot de passe oublié)
 * 
 * Variables attendues:
 * @var string $title - Titre de la page
 */

$title = $title ?? 'Connexion';
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
    <link rel="stylesheet" href="/assets/css/app.css">

    <!-- CSRF Meta -->
    <?= CSRF::meta() ?? '' ?>
</head>

<body>
    <div class="auth-layout">
        <div class="auth-container">
            <?php if (isset($content)) echo $content; ?>
        </div>
    </div>
</body>

</html>