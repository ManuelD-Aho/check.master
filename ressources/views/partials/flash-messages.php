<?php

declare(strict_types=1);
/**
 * CheckMaster - Flash Messages Partial
 * ======================================
 * Affichage des messages flash de session
 */

// Types de messages supportés
$flashTypes = ['success', 'error', 'warning', 'info'];

// Récupérer et effacer les messages flash
$flashMessages = [];
foreach ($flashTypes as $type) {
    $key = "flash_{$type}";
    if (isset($_SESSION[$key])) {
        $flashMessages[$type] = $_SESSION[$key];
        unset($_SESSION[$key]);
    }
}

// Ne rien afficher s'il n'y a pas de messages
if (empty($flashMessages)) {
    return;
}

// Mapping des icônes par type
$icons = [
    'success' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
    'error' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    'warning' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    'info' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
];
?>

<div class="flash-messages">
    <?php foreach ($flashMessages as $type => $message): ?>
        <div class="alert alert-<?= $type ?>" role="alert">
            <span class="alert-icon">
                <?= $icons[$type] ?? $icons['info'] ?>
            </span>
            <div class="alert-content">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <button type="button" class="alert-dismiss" aria-label="Fermer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
    <?php endforeach; ?>
</div>