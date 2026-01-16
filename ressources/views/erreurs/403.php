<?php
declare(strict_types=1);
$title = 'Accès interdit - 403';
$pageTitle = 'Accès interdit';
ob_start();
?>
<div class="error-page">
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-title">Accès interdit</div>
        <div class="error-message">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</div>
        <div class="error-actions">
            <a href="/dashboard" class="btn btn-primary">Tableau de bord</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>
<style>
.error-page{min-height:60vh;display:flex;align-items:center;justify-content:center;padding:2rem}
.error-container{text-align:center;max-width:600px}
.error-code{font-size:8rem;font-weight:700;color:#e11d48;line-height:1;margin-bottom:1rem}
.error-title{font-size:2rem;font-weight:600;color:#1f2937;margin-bottom:1rem}
.error-message{font-size:1.125rem;color:#6b7280;margin-bottom:2rem}
.error-actions{display:flex;gap:1rem;justify-content:center}
.btn{display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;border-radius:0.5rem;font-weight:500;text-decoration:none}
.btn-primary{background-color:#3b82f6;color:white}
.btn-secondary{background-color:#e5e7eb;color:#374151}
</style>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
