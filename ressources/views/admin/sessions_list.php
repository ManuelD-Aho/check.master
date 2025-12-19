<?php

/**
 * Vue Admin - Gestion des Sessions Actives
 * 
 * @var array $sessions Liste des sessions actives
 */

declare(strict_types=1);

use Src\Support\CSRF;
use Src\Support\Auth;

// Messages flash
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$success = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$currentSessionToken = $_COOKIE['session_token'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions Actives - Admin CheckMaster</title>
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
            --warning: #f59e0b;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .header .back-link {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.9;
        }

        .header .back-link:hover {
            opacity: 1;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #f0fff4;
            border: 1px solid var(--success);
            color: var(--success);
        }

        .alert-error {
            background: #fff5f5;
            border: 1px solid var(--error);
            color: var(--error);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: var(--shadow);
        }

        .stat-card .label {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .sessions-table {
            background: var(--white);
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-header h2 {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .refresh-btn {
            background: var(--accent);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .refresh-btn:hover {
            background: #2c7a7b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-light);
        }

        tr:hover {
            background: #f8fafc;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-details .name {
            font-weight: 500;
        }

        .user-details .email {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-current {
            background: #dcfce7;
            color: #166534;
        }

        .badge-online {
            background: #dbeafe;
            color: #1e40af;
        }

        .ip-cell {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .device-cell {
            font-size: 0.875rem;
        }

        .time-cell {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .action-btn {
            background: var(--error);
            color: var(--white);
            border: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #c53030;
            transform: translateY(-1px);
        }

        .action-btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
        }

        .empty-state .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            th,
            td {
                padding: 0.75rem;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>🔐 Sessions Actives</h1>
        <a href="/dashboard" class="back-link">
            ← Retour au tableau de bord
        </a>
    </header>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success">
                ✓ <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                ⚠ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="stats-row">
            <div class="stat-card">
                <div class="label">Sessions actives</div>
                <div class="value"><?= count($sessions) ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Utilisateurs uniques</div>
                <div class="value"><?= count(array_unique(array_column($sessions, 'utilisateur_id'))) ?></div>
            </div>
        </div>

        <div class="sessions-table">
            <div class="table-header">
                <h2>Liste des sessions</h2>
                <button class="refresh-btn" onclick="location.reload()">
                    🔄 Actualiser
                </button>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Adresse IP</th>
                            <th>Appareil</th>
                            <th>Dernière activité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sessions)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="icon">📭</div>
                                        <p>Aucune session active pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($session['nom_utilisateur'], 0, 2)) ?>
                                            </div>
                                            <div class="user-details">
                                                <div class="name">
                                                    <?= htmlspecialchars($session['nom_utilisateur'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?php if ($session['utilisateur_id'] === Auth::id()): ?>
                                                        <span class="badge badge-current">Vous</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="email"><?= htmlspecialchars($session['login'], ENT_QUOTES, 'UTF-8') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="ip-cell"><?= htmlspecialchars($session['ip_adresse'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="device-cell"><?= htmlspecialchars($session['user_agent'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="time-cell">
                                        <?= htmlspecialchars($session['temps_relatif'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td>
                                        <?php if ($session['utilisateur_id'] !== Auth::id()): ?>
                                            <button
                                                class="action-btn"
                                                onclick="killSession(<?= $session['id'] ?>)"
                                                data-session-id="<?= $session['id'] ?>">
                                                🔴 Déconnecter
                                            </button>
                                        <?php else: ?>
                                            <button class="action-btn" disabled title="Vous ne pouvez pas vous déconnecter ici">
                                                Session courante
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        async function killSession(sessionId) {
            if (!confirm('Êtes-vous sûr de vouloir forcer la déconnexion de cette session ?')) {
                return;
            }

            const btn = document.querySelector(`[data-session-id="${sessionId}"]`);
            btn.disabled = true;
            btn.innerHTML = '⏳ En cours...';

            try {
                const response = await fetch(`/api/admin/sessions/${sessionId}/kill`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Impossible de terminer la session'));
                    btn.disabled = false;
                    btn.innerHTML = '🔴 Déconnecter';
                }
            } catch (error) {
                alert('Erreur de connexion');
                btn.disabled = false;
                btn.innerHTML = '🔴 Déconnecter';
            }
        }
    </script>
</body>

</html>