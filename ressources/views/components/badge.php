<?php

declare(strict_types=1);
/**
 * CheckMaster Component - Badge
 * ==============================
 * Composant badge réutilisable
 * 
 * Usage:
 * <?php include_component('badge', [
 *     'text' => 'Nouveau',
 *     'variant' => 'primary',
 *     'size' => 'md',
 *     'pill' => false,
 *     'dot' => false
 * ]); ?>
 */

// Configuration par défaut
$text = $text ?? '';
$variant = $variant ?? 'default';
$size = $size ?? 'md';
$pill = $pill ?? false;
$dot = $dot ?? false;
$state = $state ?? null;

// Classes CSS
$classes = ['badge'];

// Variant pour les états workflow
if ($state) {
    $classes[] = "badge-state-{$state}";
    $classes = ['badge-state', "badge-state-{$state}"];
} else {
    $classes[] = "badge-{$variant}";
}

if ($size !== 'md') {
    $classes[] = "badge-{$size}";
}

if ($pill) {
    $classes[] = 'badge-pill';
}

if ($dot && !$text) {
    $classes[] = 'badge-dot';
}

$classString = implode(' ', $classes);
?>

<span class="<?= $classString ?>"><?= htmlspecialchars($text) ?></span>