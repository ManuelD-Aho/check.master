<?php

declare(strict_types=1);
/**
 * CheckMaster Component - Card
 * =============================
 * Composant carte réutilisable
 * 
 * Usage:
 * <?php include_component('card', [
 *     'title' => 'Titre de la carte',
 *     'subtitle' => 'Sous-titre optionnel',
 *     'headerActions' => '<button>...</button>',
 *     'body' => 'Contenu du corps',
 *     'footer' => 'Contenu du pied',
 *     'variant' => 'default',
 *     'padding' => true,
 *     'attributes' => []
 * ]); ?>
 */

// Configuration par défaut
$title = $title ?? null;
$subtitle = $subtitle ?? null;
$headerActions = $headerActions ?? null;
$body = $body ?? null;
$footer = $footer ?? null;
$variant = $variant ?? 'default';
$padding = $padding ?? true;
$attributes = $attributes ?? [];

// Classes CSS
$classes = ['card'];
if ($variant === 'bordered') {
    $classes[] = 'card-bordered';
} elseif ($variant === 'hover') {
    $classes[] = 'card-hover';
} elseif ($variant === 'flat') {
    $classes[] = 'card-flat';
}

$classString = implode(' ', $classes);

// Attributs
$attrString = '';
foreach ($attributes as $key => $value) {
    $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars((string)$value) . '"';
}
?>

<div class="<?= $classString ?>" <?= $attrString ?>>
    <?php if ($title || $headerActions): ?>
        <div class="card-header">
            <div>
                <?php if ($title): ?>
                    <h3 class="card-title"><?= htmlspecialchars($title) ?></h3>
                <?php endif; ?>
                <?php if ($subtitle): ?>
                    <p class="card-subtitle"><?= htmlspecialchars($subtitle) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($headerActions): ?>
                <div class="card-actions"><?= $headerActions ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($body !== null): ?>
        <div class="card-body<?= $padding ? '' : ' p-0' ?>">
            <?= $body ?>
        </div>
    <?php endif; ?>

    <?php if ($footer): ?>
        <div class="card-footer">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>