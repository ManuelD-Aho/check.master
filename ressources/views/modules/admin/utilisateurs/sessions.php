<?php

/**
 * Vue de gestion des sessions utilisateurs (Admin)
 * 
 * Permet aux administrateurs de voir et forcer la déconnexion des sessions.
 * 
 * @var array $sessions Liste des sessions
 * @var array $user Utilisateur cible
 */

declare(strict_types=1);

use Src\Support\CSRF;
?>

<div class="content-header">
    <h1>Sessions actives</h1>
    <p>Gestion des sessions de l'utilisateur : <?= htmlspecialchars($user['nom'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Sessions en cours</h3>
    </div>
    <div class="card-body">
        <?php if (empty($sessions)): ?>
            <div class="alert alert-info">
                Aucune session active pour cet utilisateur.
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Adresse IP</th>
                        <th>Appareil</th>
                        <th>Dernière activité</th>
                        <th>Expire le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td><?= htmlspecialchars($session['ip_adresse'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="user-agent" title="<?= htmlspecialchars($session['user_agent'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(substr($session['user_agent'] ?? '-', 0, 50), ENT_QUOTES, 'UTF-8') ?>...
                                </span>
                            </td>
                            <td><?= htmlspecialchars($session['derniere_activite'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($session['expire_a'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form action="/admin/sessions/kill" method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir terminer cette session ?');">
                                    <?= CSRF::field() ?>
                                    <input type="hidden" name="session_id" value="<?= (int) $session['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Déconnecter
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="actions">
    <a href="/admin/utilisateurs" class="btn btn-secondary">Retour à la liste</a>
</div>

<style>
    .inline-form {
        display: inline;
    }

    .user-agent {
        cursor: help;
        font-size: 0.875rem;
        color: #718096;
    }

    .btn-danger {
        background-color: #e53e3e;
        color: white;
        border: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .btn-danger:hover {
        background-color: #c53030;
    }

    .alert-info {
        background-color: #ebf8ff;
        border: 1px solid #90cdf4;
        color: #2b6cb0;
        padding: 1rem;
        border-radius: 0.5rem;
    }
</style>