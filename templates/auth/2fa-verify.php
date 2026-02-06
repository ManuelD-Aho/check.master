<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA — Check Master</title>
    <style>
        :root {
            --cm-primary: #4f46e5;
            --cm-primary-hover: #4338ca;
            --cm-text: #1e293b;
            --cm-text-light: #64748b;
            --cm-bg: #f8fafc;
            --cm-white: #ffffff;
            --cm-border: #e2e8f0;
            --cm-error-bg: #fef2f2;
            --cm-error-text: #991b1b;
            --cm-error-border: #fecaca;
            --cm-success-bg: #f0fdf4;
            --cm-success-text: #166534;
            --cm-success-border: #bbf7d0;
            --cm-radius: 8px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--cm-bg);
            color: var(--cm-text);
            line-height: 1.6;
        }
        .cm-auth-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .cm-auth-card {
            background: var(--cm-white);
            border: 1px solid var(--cm-border);
            border-radius: var(--cm-radius);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        .cm-auth-card__title {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .cm-auth-card__subtitle {
            font-size: 0.875rem;
            color: var(--cm-text-light);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .cm-alert--error {
            background: var(--cm-error-bg);
            color: var(--cm-error-text);
            border: 1px solid var(--cm-error-border);
            border-radius: var(--cm-radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .cm-alert--success {
            background: var(--cm-success-bg);
            color: var(--cm-success-text);
            border: 1px solid var(--cm-success-border);
            border-radius: var(--cm-radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .cm-form__field {
            margin-bottom: 1rem;
        }
        .cm-form__label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.375rem;
        }
        .cm-form__input {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid var(--cm-border);
            border-radius: var(--cm-radius);
            font-size: 0.9375rem;
            color: var(--cm-text);
            outline: none;
            transition: border-color 0.15s;
            letter-spacing: 0.25em;
            text-align: center;
        }
        .cm-form__input:focus {
            border-color: var(--cm-primary);
        }
        .cm-form__hint {
            font-size: 0.75rem;
            color: var(--cm-text-light);
            margin-top: 0.25rem;
            text-align: center;
        }
        .cm-btn--primary {
            display: block;
            width: 100%;
            padding: 0.625rem;
            background: var(--cm-primary);
            color: var(--cm-white);
            border: none;
            border-radius: var(--cm-radius);
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .cm-btn--primary:hover {
            background: var(--cm-primary-hover);
        }
        .cm-auth-card__footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.875rem;
        }
        .cm-auth-card__footer a {
            color: var(--cm-primary);
            text-decoration: none;
        }
        .cm-auth-card__footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="cm-auth-page">
        <section class="cm-auth-card">
            <h1 class="cm-auth-card__title">Vérification 2FA</h1>
            <p class="cm-auth-card__subtitle">Saisissez le code généré par votre application d'authentification.</p>

            <?php if (!empty($flashes['error'])): ?>
                <div class="cm-alert--error"><?= htmlspecialchars($flashes['error']) ?></div>
            <?php endif; ?>
            <?php if (!empty($flashes['success'])): ?>
                <div class="cm-alert--success"><?= htmlspecialchars($flashes['success']) ?></div>
            <?php endif; ?>

            <form method="post" action="/login/2fa">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="cm-form__field">
                    <label class="cm-form__label" for="code">Code de vérification</label>
                    <input class="cm-form__input" type="text" id="code" name="code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" placeholder="000000">
                    <p class="cm-form__hint">Code à 6 chiffres</p>
                </div>

                <button type="submit" class="cm-btn--primary">Vérifier</button>
            </form>

            <div class="cm-auth-card__footer">
                <a href="/login">Retour à la connexion</a>
            </div>
        </section>
    </main>
</body>
</html>
