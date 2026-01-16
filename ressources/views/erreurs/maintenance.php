<?php
declare(strict_types=1);
$title = 'Maintenance en cours';
$pageTitle = 'Maintenance';
ob_start();
?>
<div class="error-page">
    <div class="error-container">
        <div class="error-icon">🔧</div>
        <div class="error-title">Maintenance en cours</div>
        <div class="error-message">Le système est temporairement indisponible pour maintenance. Merci de réessayer dans quelques instants.</div>
        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn btn-primary">Réessayer</a>
        </div>
    </div>
</div>
<style>
.error-page{min-height:60vh;display:flex;align-items:center;justify-content:center;padding:2rem}
.error-container{text-align:center;max-width:600px}
.error-icon{font-size:6rem;margin-bottom:1rem}
.error-title{font-size:2rem;font-weight:600;color:#1f2937;margin-bottom:1rem}
.error-message{font-size:1.125rem;color:#6b7280;margin-bottom:2rem}
.error-actions{display:flex;gap:1rem;justify-content:center}
.btn{display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;border-radius:0.5rem;font-weight:500;text-decoration:none}
.btn-primary{background-color:#3b82f6;color:white}
</style>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
