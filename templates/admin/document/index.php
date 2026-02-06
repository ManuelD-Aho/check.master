<?php
$title = 'Gestion des documents';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Gestion des documents</h1>
        <p class="subtitle">Génération et gestion centralisée des documents PDF</p>
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
        <h2>Types de documents disponibles</h2>
        <div class="settings-grid">
            <?php if (!empty($types)): ?>
                <?php foreach ($types as $type): ?>
                    <div class="settings-card">
                        <h2><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $type))); ?></h2>
                        <p>Générer un document de type : <?php echo htmlspecialchars($type); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun générateur de document disponible.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
