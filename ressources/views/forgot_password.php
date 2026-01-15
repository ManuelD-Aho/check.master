<?php

declare(strict_types=1);

use Src\Support\CSRF;
use Src\Http\Request;
use App\Services\Core\ServiceSession;

// Récupérer les messages flash
$basePath = Request::basePath();
$success = ServiceSession::getFlashSuccess();
$error = ServiceSession::getFlashError();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Mot de passe oublié - CheckMaster UFHB</title>
    <?= CSRF::meta() ?>
    <style>
        :root {
            --primary: #1a365d;
            --primary-light: #2b4c7e;
            --accent: #38b2ac;
            --text: #2d3748;
            --text-light: #718096;
            --bg: #f7fafc;
            --white: #ffffff;
            --error: #e53e3e;
            --success: #38a169;
            --shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .container {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
        }
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.5rem; color: var(--primary); margin-bottom: 0.5rem; }
        .header p { color: var(--text-light); font-size: 0.9rem; }
        .message { padding: 0.875rem 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .error { background: #fff5f5; border: 1px solid var(--error); color: var(--error); }
        .success { background: #f0fff4; border: 1px solid var(--success); color: var(--success); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--text); margin-bottom: 0.5rem; }
        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            color: var(--text);
            outline: none;
        }
        .form-group input:focus { border-color: var(--accent); }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 0.75rem;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(26, 54, 93, 0.3); }
        .footer { text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; }
        .footer a { color: var(--accent); text-decoration: none; font-size: 0.9rem; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mot de passe oublié</h1>
            <p>Entrez votre email pour recevoir un lien de réinitialisation</p>
        </div>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="<?= $basePath . '/forgot-password' ?>" method="POST">
            <?= CSRF::field() ?>
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" placeholder="votre.email@ufhb.edu.ci" required autocomplete="email">
            </div>
            <button type="submit" class="btn-submit">Envoyer le lien</button>
        </form>

        <div class="footer">
            <a href="<?= $basePath . '/connexion' ?>">← Retour à la connexion</a>
        </div>
    </div>
</body>
</html>
