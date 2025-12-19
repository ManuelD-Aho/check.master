<?php

/**
 * Vue de changement de mot de passe CheckMaster
 * 
 * @var bool $isForced Indique si le changement est forcé (première connexion)
 */

declare(strict_types=1);

use Src\Support\CSRF;
use Src\Support\Auth;

// Récupérer messages flash
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $_SESSION['flash_errors'] ?? [];
$success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_errors'], $_SESSION['flash_success']);

$user = Auth::user();
$isForced = $isForced ?? ($user ? $user->doitChangerMotDePasse() : false);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Changement de mot de passe - CheckMaster UFHB</title>
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

        .password-container {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 480px;
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

        .password-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .password-header .icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--accent) 0%, #2c7a7b 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--white);
            font-size: 2rem;
        }

        .password-header h1 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .password-header p {
            color: var(--text-light);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .forced-notice {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 0.5rem;
            padding: 1rem;
            color: #92400e;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .forced-notice::before {
            content: "⚠️";
            font-size: 1.25rem;
        }

        .error-list {
            background: #fff5f5;
            border: 1px solid var(--error);
            border-radius: 0.5rem;
            padding: 1rem;
            color: var(--error);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .error-list ul {
            margin: 0.5rem 0 0 1rem;
        }

        .success-message {
            background: #f0fff4;
            border: 1px solid var(--success);
            border-radius: 0.5rem;
            padding: 1rem;
            color: var(--success);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success-message::before {
            content: "✓";
            font-weight: bold;
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

        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .password-requirements {
            margin-top: 0.75rem;
            padding: 0.75rem;
            background: #f7fafc;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-light);
        }

        .password-requirements ul {
            margin: 0.25rem 0 0 1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--accent) 0%, #2c7a7b 100%);
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
            box-shadow: 0 5px 20px rgba(56, 178, 172, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 1.25rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .cancel-link:hover {
            color: var(--primary);
        }

        @media (max-width: 480px) {
            .password-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="password-container">
        <div class="password-header">
            <div class="icon">🔐</div>
            <h1>Changement de mot de passe</h1>
            <p>
                <?php if ($isForced): ?>
                    Pour des raisons de sécurité, vous devez modifier votre mot de passe avant de continuer.
                <?php else: ?>
                    Saisissez votre mot de passe actuel et choisissez un nouveau mot de passe sécurisé.
                <?php endif; ?>
            </p>
        </div>

        <?php if ($isForced): ?>
            <div class="forced-notice">
                <div>
                    <strong>Première connexion détectée</strong><br>
                    Pour sécuriser votre compte, veuillez définir un nouveau mot de passe personnel.
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-list">
                <strong>Erreur(s) :</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="/change-password" method="POST" id="password-form">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password">
            </div>

            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                    minlength="8">
                <div class="password-strength">
                    <div class="password-strength-bar" id="strength-bar"></div>
                </div>
                <div class="password-requirements">
                    <strong>Exigences :</strong>
                    <ul>
                        <li>Minimum 8 caractères</li>
                        <li>Différent du mot de passe actuel</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password">
            </div>

            <button type="submit" class="btn-submit">Modifier le mot de passe</button>
        </form>

        <?php if (!$isForced): ?>
            <a href="/dashboard" class="cancel-link">Annuler et retourner au tableau de bord</a>
        <?php endif; ?>
    </div>

    <script>
        // Password strength indicator
        const newPassword = document.getElementById('new_password');
        const strengthBar = document.getElementById('strength-bar');

        newPassword.addEventListener('input', function() {
            const value = this.value;
            let strength = 0;

            if (value.length >= 8) strength += 25;
            if (value.length >= 12) strength += 15;
            if (/[a-z]/.test(value) && /[A-Z]/.test(value)) strength += 20;
            if (/[0-9]/.test(value)) strength += 20;
            if (/[^a-zA-Z0-9]/.test(value)) strength += 20;

            strengthBar.style.width = strength + '%';

            if (strength < 30) {
                strengthBar.style.background = '#e53e3e';
            } else if (strength < 60) {
                strengthBar.style.background = '#f59e0b';
            } else {
                strengthBar.style.background = '#38a169';
            }
        });
    </script>
</body>

</html>