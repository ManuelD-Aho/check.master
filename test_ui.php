<?php

/**
 * Page de test pour le Design System (Kitchen Sink)
 */

require_once __DIR__ . '/vendor/autoload.php';

use Src\Support\CSRF;

// Mock session setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables de layout
$title = "Test Design System";
$pageTitle = "Design System Kitchen Sink";
$currentPage = "test-ui";
$user = [
    'name' => 'Jean Dupont',
    'role' => 'Administrateur',
    'initials' => 'JD'
];

$breadcrumbs = [
    ['label' => 'Accueil', 'url' => '/'],
    ['label' => 'Design System', 'url' => '/test_ui.php']
];

// Helper pour inclure les composants (normalement dans un helper global)
if (!function_exists('include_component')) {
    function include_component($name, $params = [])
    {
        extract($params);
        $path = __DIR__ . "/ressources/views/components/{$name}.php";
        if (file_exists($path)) {
            include $path;
        } else {
            echo "<!-- Component {$name} not found -->";
        }
    }
}

// Capture du contenu
ob_start();
include __DIR__ . '/ressources/views/test_design_content.php';
$content = ob_get_clean();

// Inclusion du layout principal
include __DIR__ . '/ressources/views/layouts/app.php';
