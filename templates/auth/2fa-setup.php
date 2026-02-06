<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration 2FA — Check Master</title>
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
            --cm-info-bg: #eff6ff;
            --cm-info-text: #1e40af;
            --cm-info-border: #bfdbfe;
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
            max-width: 440px;
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
        .cm-qr {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        .cm-qr img {
            border: 1px solid var(--cm-border);
            border-radius: var(--cm-radius);
            padding: 0.5rem;
            background: var(--cm-white);
        }
        .cm-qr__caption {
            font-size: 0.75rem;
            color: var(--cm-text-light);
            margin-top: 0.5rem;
        }
        .cm-recovery {
            background: var(--cm-info-bg);
            border: 1px solid var(--cm-info-border);
            border-radius: var(--cm-radius);
            padding: 1rem;
            margin-bottom: 1.25rem;
        }
        .cm-recovery__heading {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--cm-info-text);
            margin-bottom: 0.5rem;
        }
        .cm-recovery__list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.25rem 1rem;
        }
        .cm-recovery__code {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 0.8125rem;
            color: var(--cm-info-text);
        }
        .cm-recovery__note {
            font-size: 0.75rem;
            color: var(--cm-text-light);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <main class="cm-auth-page">
        <section class="cm-auth-card">
            <h1 class="cm-auth-card__title">Authentification à deux facteurs</h1>
            <p class="cm-auth-card__subtitle">Scannez le QR code avec votre application d'authentification, puis saisissez le code généré.</p>

            <?php if (!empty($flashes['error'])): ?>
                <div class="cm-alert--error"><?= htmlspecialchars($flashes['error']) ?></div>
            <?php endif; ?>
            <?php if (!empty($flashes['success'])): ?>
                <div class="cm-alert--success"><?= htmlspecialchars($flashes['success']) ?></div>
            <?php endif; ?>

            <?php if (!empty($qr_uri)): ?>
                <div class="cm-qr">
                    <img src="<?= htmlspecialchars($qr_uri) ?>" alt="QR Code pour 2FA" width="200" height="200">
                    <p class="cm-qr__caption">Scannez ce code avec Google Authenticator ou une application similaire.</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($recovery_codes) && is_array($recovery_codes)): ?>
                <div class="cm-recovery">
                    <p class="cm-recovery__heading">Codes de récupération</p>
                    <ul class="cm-recovery__list">
                        <?php foreach ($recovery_codes as $code): ?>
                            <li class="cm-recovery__code"><?= htmlspecialchars($code) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="cm-recovery__note">Conservez ces codes en lieu sûr. Chaque code ne peut être utilisé qu'une seule fois.</p>
                </div>
            <?php endif; ?>

            <form method="post" action="/compte/2fa">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="cm-form__field">
                    <label class="cm-form__label" for="code">Code de vérification</label>
                    <input class="cm-form__input" type="text" id="code" name="code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" placeholder="000000">
                    <p class="cm-form__hint">Saisissez le code à 6 chiffres affiché dans votre application.</p>
                </div>

                <button type="submit" class="cm-btn--primary">Activer la 2FA</button>
            </form>
        </section>
    </main>
</body>
</html>
