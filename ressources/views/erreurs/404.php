<?php
/**
 * Page Erreur 404 - Page non trouvée
 */

declare(strict_types=1);

$title = 'Page non trouvée - 404';
$pageTitle = 'Page non trouvée';

ob_start();
?>

<div class="error-page">
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-title">Page non trouvée</div>
        <div class="error-message">
            La page que vous recherchez n'existe pas ou a été déplacée.
        </div>
        <div class="error-actions">
            <a href="/dashboard" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Retour à l'accueil
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Page précédente
            </a>
        </div>
    </div>
</div>

<style>
.error-page {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.error-container {
    text-align: center;
    max-width: 600px;
}
.error-code {
    font-size: 8rem;
    font-weight: 700;
    color: #e11d48;
    line-height: 1;
    margin-bottom: 1rem;
}
.error-title {
    font-size: 2rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
}
.error-message {
    font-size: 1.125rem;
    color: #6b7280;
    margin-bottom: 2rem;
}
.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}
.btn svg {
    width: 1.25rem;
    height: 1.25rem;
}
.btn-primary {
    background-color: #3b82f6;
    color: white;
}
.btn-primary:hover {
    background-color: #2563eb;
}
.btn-secondary {
    background-color: #e5e7eb;
    color: #374151;
}
.btn-secondary:hover {
    background-color: #d1d5db;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
