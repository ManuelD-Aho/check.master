<?php

declare(strict_types=1);
/**
 * CheckMaster Component - Button
 * ===============================
 * Composant bouton réutilisable
 * 
 * Usage:
 * <?php include_component('button', [
 *     'text' => 'Enregistrer',
 *     'type' => 'submit',
 *     'variant' => 'primary',
 *     'size' => 'md',
 *     'icon' => '<svg>...</svg>',
 *     'iconPosition' => 'left',
 *     'block' => false,
 *     'disabled' => false,
 *     'loading' => false,
 *     'href' => null,
 *     'attributes' => ['data-action' => 'save']
 * ]); ?>
 */

// Configuration par défaut
$text = $text ?? '';
$type = $type ?? 'button';
$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$icon = $icon ?? null;
$iconPosition = $iconPosition ?? 'left';
$block = $block ?? false;
$disabled = $disabled ?? false;
$loading = $loading ?? false;
$href = $href ?? null;
$attributes = $attributes ?? [];

// Construire les classes CSS
$classes = ['btn'];
$classes[] = "btn-{$variant}";

if ($size !== 'md') {
    $classes[] = "btn-{$size}";
}

if ($block) {
    $classes[] = 'btn-block';
}

if ($loading) {
    $classes[] = 'is-loading';
}

if ($disabled) {
    $classes[] = 'is-disabled';
}

if ($icon && empty($text)) {
    $classes[] = 'btn-icon';
}

$classString = implode(' ', $classes);

// Construire les attributs additionnels
$attrString = '';
foreach ($attributes as $key => $value) {
    $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars((string)$value) . '"';
}

if ($disabled) {
    $attrString .= ' disabled';
}

// Contenu du bouton
$content = '';
if ($icon && $iconPosition === 'left') {
    $content .= '<span class="icon">' . $icon . '</span>';
}
if ($text) {
    $content .= '<span>' . htmlspecialchars($text) . '</span>';
}
if ($icon && $iconPosition === 'right') {
    $content .= '<span class="icon">' . $icon . '</span>';
}

// Rendu
if ($href && !$disabled):
?>
    <a href="<?= htmlspecialchars($href) ?>" class="<?= $classString ?>" <?= $attrString ?>><?= $content ?></a>
<?php else: ?>
    <button type="<?= $type ?>" class="<?= $classString ?>" <?= $attrString ?>><?= $content ?></button>
<?php endif; ?>