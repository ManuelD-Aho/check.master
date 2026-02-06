<?php
$title = 'Gestion du cache';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Gestion du cache</h1>
        <p class="subtitle">Videz le cache du système pour appliquer les dernières modifications</p>
    </div>
</div>

<?php if (isset($flashes) && !empty($flashes)): ?>
    <div class="alerts">
        <?php foreach ($flashes as $flashType => $flashList): ?>
            <?php foreach ($flashList as $flashMsg): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flashType); ?>">
                    <span><?php echo htmlspecialchars($flashMsg); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="detail-card">
    <div class="detail-section">
        <h2>Cache du système</h2>
        <p>Le cache améliore les performances de l'application en stockant temporairement des données fréquemment utilisées. Videz le cache si vous rencontrez des problèmes d'affichage ou après une mise à jour de la configuration.</p>

        <form method="POST" action="/admin/maintenance/cache/clear" style="margin-top: 1rem;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir vider le cache ?');">
                Vider le cache
            </button>
        </form>
    </div>
</div>
