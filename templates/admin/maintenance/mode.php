<?php
$title = 'Mode maintenance';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Mode maintenance</h1>
        <p class="subtitle">Activer ou désactiver le mode maintenance de l'application</p>
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
        <h2>État actuel</h2>
        <p>
            Le mode maintenance est actuellement :
            <?php if (!empty($maintenance_mode)): ?>
                <span class="badge badge-danger">Activé</span>
            <?php else: ?>
                <span class="badge badge-success">Désactivé</span>
            <?php endif; ?>
        </p>
        <p>Lorsque le mode maintenance est activé, les utilisateurs voient une page de maintenance et ne peuvent pas accéder à l'application. Seuls les administrateurs connectés depuis les adresses IP autorisées peuvent continuer à naviguer.</p>

        <form method="POST" action="/admin/maintenance/mode/toggle" style="margin-top: 1rem;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <?php if (!empty($maintenance_mode)): ?>
                <input type="hidden" name="maintenance_mode" value="0">
                <button type="submit" class="btn btn-success" onclick="return confirm('Désactiver le mode maintenance ?');">
                    Désactiver le mode maintenance
                </button>
            <?php else: ?>
                <input type="hidden" name="maintenance_mode" value="1">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Activer le mode maintenance ? Les utilisateurs ne pourront plus accéder à l\'application.');">
                    Activer le mode maintenance
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>
