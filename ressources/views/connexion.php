<?php

/**
 * Vue de connexion CheckMaster
 * 
 * @var string|null $error Message d'erreur flash
 */

declare(strict_types=1);

use Src\Support\CSRF;
use Src\Http\Request;
use App\Services\Core\ServiceSession;

if (!defined('BASE_PATH')) {
    require dirname(__DIR__, 2) . '/app/config/bootstrap.php';
}

// Récupérer les messages flash
$basePath = Request::basePath();
$error = ServiceSession::getFlashError();
$success = ServiceSession::getFlashSuccess();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion - CheckMaster UFHB</title>
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
            --shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--white);
            font-size: 2rem;
            font-weight: bold;
        }

        .login-header h1 {
            font-size: 1.75rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .error-message {
            background: #fff5f5;
            border: 1px solid var(--error);
            border-radius: 0.5rem;
            padding: 0.875rem 1rem;
            color: var(--error);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-message::before {
            content: "⚠";
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            color: var(--text);
            transition: all 0.2s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(56, 178, 172, 0.1);
        }

        .form-group input::placeholder {
            color: var(--text-light);
        }

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
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(26, 54, 93, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-footer a {
            color: var(--accent);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">CM</div>
            <h1>CheckMaster</h1>
            <p>Système de gestion des mémoires UFHB</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="<?= $basePath . '/connexion' ?>" method="POST" id="login-form">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="votre.email@ufhb.edu.ci"
                    required
                    autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password">
            </div>

            <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <div class="login-footer">
            <a href="<?= $basePath . '/forgot-password' ?>">Mot de passe oublié ?</a>
        </div>
    </div>
</body>

</html>
