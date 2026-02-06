<?php
$title = 'Soutenances';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Soutenances</h1>
        <p class="subtitle">Gestion des soutenances</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance/create" class="btn btn-primary">+ Programmer une soutenance</a>
    </div>
</div>

<?php if (isset($flashes) && !empty($flashes)): ?>
    <div class="alerts">
        <?php foreach ($flashes as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                    <span><?php echo htmlspecialchars($message); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Sujet</th>
                <th>Date</th>
                <th>Salle</th>
                <th>Statut</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($soutenances) && is_array($soutenances) && !empty($soutenances)): ?>
                <?php foreach ($soutenances as $soutenance): ?>
                    <tr class="data-row">
                        <td class="col-name">
                            <strong><?php echo htmlspecialchars($soutenance['etudiant'] ?? ''); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($soutenance['sujet'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['salle'] ?? 'N/A'); ?></td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($soutenance['statut'] ?? 'en_attente'); ?>">
                                <?php echo htmlspecialchars(ucfirst($soutenance['statut'] ?? 'En attente')); ?>
                            </span>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)($soutenance['id'] ?? 0); ?>" class="btn-icon btn-view" title="Voir">👁</a>
                                <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)($soutenance['id'] ?? 0); ?>/edit" class="btn-icon btn-edit" title="Modifier">✏️</a>
                                <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)($soutenance['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Confirmer la suppression ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <p>Aucune soutenance trouvée</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
