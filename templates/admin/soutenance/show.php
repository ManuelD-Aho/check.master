<?php
$title = 'Détails de la soutenance';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Détails de la soutenance</h1>
        <p class="subtitle"><?php echo htmlspecialchars($soutenance['sujet'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance" class="btn btn-secondary">← Retour</a>
        <?php if (isset($soutenance['id'])): ?>
            <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)$soutenance['id']; ?>/edit" class="btn btn-primary">Modifier</a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($soutenance) && !empty($soutenance)): ?>
    <div class="detail-card">
        <div class="detail-section">
            <h2>Informations générales</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Étudiant</span>
                    <span class="detail-value"><?php echo htmlspecialchars($soutenance['etudiant'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Sujet</span>
                    <span class="detail-value"><?php echo htmlspecialchars($soutenance['sujet'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date</span>
                    <span class="detail-value"><?php echo htmlspecialchars($soutenance['date'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Salle</span>
                    <span class="detail-value"><?php echo htmlspecialchars($soutenance['salle'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Statut</span>
                    <span class="status-badge status-<?php echo htmlspecialchars($soutenance['statut'] ?? 'en_attente'); ?>">
                        <?php echo htmlspecialchars(ucfirst($soutenance['statut'] ?? 'En attente')); ?>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Note finale</span>
                    <span class="detail-value"><?php echo htmlspecialchars($soutenance['note_finale'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <?php if (isset($soutenance['observations']) && !empty($soutenance['observations'])): ?>
            <div class="detail-section">
                <h2>Observations</h2>
                <p><?php echo htmlspecialchars($soutenance['observations']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($soutenance['jury']) && is_array($soutenance['jury'])): ?>
            <div class="detail-section">
                <h2>Jury</h2>
                <ul class="detail-list">
                    <?php foreach ($soutenance['jury'] as $membre): ?>
                        <li><?php echo htmlspecialchars($membre['nom'] ?? ''); ?> — <?php echo htmlspecialchars($membre['role'] ?? ''); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="alert alert-error">
        <span>Soutenance non trouvée.</span>
    </div>
<?php endif; ?>
