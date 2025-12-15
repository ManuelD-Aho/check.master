<?php

declare(strict_types=1);
/**
 * CheckMaster Component - Modal
 * ==============================
 * Composant modale réutilisable
 * 
 * Usage:
 * <?php include_component('modal', [
 *     'id' => 'confirm-modal',
 *     'title' => 'Confirmer l\'action',
 *     'body' => '<p>Êtes-vous sûr ?</p>',
 *     'footer' => '<button class="btn btn-secondary">Annuler</button>',
 *     'size' => 'md',
 *     'type' => 'default',
 *     'closeButton' => true
 * ]); ?>
 */

// Configuration par défaut
$id = $id ?? 'modal-' . uniqid();
$title = $title ?? '';
$body = $body ?? '';
$footer = $footer ?? null;
$size = $size ?? 'md';
$type = $type ?? 'default';
$closeButton = $closeButton ?? true;
$icon = $icon ?? null;
$iconType = $iconType ?? 'info';

// Classes du modal
$modalClasses = ['modal'];
if ($size !== 'md') {
    $modalClasses[] = "modal-{$size}";
}
if ($type === 'confirm') {
    $modalClasses[] = 'modal-confirm';
}

$modalClassString = implode(' ', $modalClasses);

// Icônes pour les modales de confirmation
$confirmIcons = [
    'danger' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    'warning' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    'success' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
    'info' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
];
?>

<div class="modal-backdrop" id="<?= htmlspecialchars($id) ?>" role="dialog" aria-modal="true" aria-labelledby="<?= htmlspecialchars($id) ?>-title">
    <div class="<?= $modalClassString ?>">
        <?php if ($title || $closeButton): ?>
            <div class="modal-header">
                <h2 class="modal-title" id="<?= htmlspecialchars($id) ?>-title">
                    <?= htmlspecialchars($title) ?>
                </h2>
                <?php if ($closeButton): ?>
                    <button type="button" class="modal-close" data-modal-close aria-label="Fermer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="modal-body">
            <?php if ($type === 'confirm' && $iconType): ?>
                <div class="modal-confirm-icon icon-<?= htmlspecialchars($iconType) ?>">
                    <?= $confirmIcons[$iconType] ?? $confirmIcons['info'] ?>
                </div>
            <?php endif; ?>

            <?= $body ?>
        </div>

        <?php if ($footer): ?>
            <div class="modal-footer">
                <?= $footer ?>
            </div>
        <?php endif; ?>
    </div>
</div>