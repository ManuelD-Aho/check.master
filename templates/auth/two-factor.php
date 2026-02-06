<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification Deux Facteurs - Check Master</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .security-badge {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .code-input-wrapper {
            display: flex;
            gap: 8px;
        }

        input[type="text"].code-input {
            width: 50px;
            height: 50px;
            padding: 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 20px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #f9f9f9;
            color: #333;
            flex: 1;
        }

        input[type="text"].code-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input[type="text"].code-input.filled {
            border-color: #27ae60;
            background: #f0f9f6;
            color: #27ae60;
        }

        input[type="text"].full-code {
            width: 100%;
            height: auto;
            padding: 12px 15px;
            font-size: 15px;
            text-align: center;
            font-family: 'Courier New', monospace;
            letter-spacing: 4px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .backup-codes-link {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
        }

        .backup-codes-link a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .backup-codes-link a:hover {
            color: #667eea;
        }

        .csrf-field {
            display: none;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c33;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #3c3;
        }

        .timer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }

        .timer.warning {
            color: #f39c12;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="security-badge">🔐</div>
            <h1>Vérification en Deux Facteurs</h1>
            <p>Entrez le code de vérification reçu sur votre appareil</p>
        </div>

        <div class="info-box">
            Saisissez le code à 6 chiffres généré par votre application d'authentification ou reçu par SMS.
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($action ?? '/two-factor'); ?>">
            <div class="form-group">
                <label for="code">Code de Vérification</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="code-input full-code"
                    required 
                    placeholder="000000"
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    autocomplete="one-time-code"
                    value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>"
                >
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>" class="csrf-field">

            <button type="submit" class="submit-btn">Vérifier</button>

            <div class="timer" id="timer"></div>
        </form>

        <div class="backup-codes-link">
            <a href="<?php echo htmlspecialchars($backupLink ?? '#'); ?>">Utiliser un code de sauvegarde</a>
        </div>

        <div class="back-link">
            <a href="<?php echo htmlspecialchars($backLink ?? '/login'); ?>">← Retour à la connexion</a>
        </div>
    </div>

    <script>
        const codeInput = document.getElementById('code');
        const timerEl = document.getElementById('timer');

        if (codeInput) {
            codeInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                
                if (this.value.length === 6) {
                    this.classList.add('filled');
                } else {
                    this.classList.remove('filled');
                }
            });

            codeInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const cleanedText = pastedText.replace(/[^0-9]/g, '').slice(0, 6);
                this.value = cleanedText;
                
                if (this.value.length === 6) {
                    this.classList.add('filled');
                    document.querySelector('form').submit();
                } else {
                    this.classList.remove('filled');
                }
            });

            codeInput.focus();
        }

        function updateTimer() {
            const timestamp = localStorage.getItem('2fa_timestamp');
            if (!timestamp) return;

            const elapsed = Math.floor((Date.now() - parseInt(timestamp)) / 1000);
            const remaining = Math.max(0, 300 - elapsed);

            if (remaining > 0) {
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timerEl.textContent = `Code valide pendant ${minutes}:${String(seconds).padStart(2, '0')}`;
                
                if (remaining < 60) {
                    timerEl.classList.add('warning');
                }

                setTimeout(updateTimer, 1000);
            } else {
                timerEl.textContent = 'Code expiré, veuillez en demander un nouveau';
                codeInput.disabled = true;
            }
        }

        updateTimer();
    </script>
</body>
</html>
