<?php
$title = 'Jurys';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Jurys</h1>
        <p class="subtitle">Liste des jurys de soutenance</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance" class="btn btn-secondary">← Retour</a>
        <a href="<?php echo BASE_URL; ?>/admin/soutenance/compose-jury" class="btn btn-primary">+ Composer un jury</a>
    </div>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom complet</th>
                <th>Rôle</th>
                <th>Soutenance</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($jurys) && is_array($jurys) && !empty($jurys)): ?>
                <?php foreach ($jurys as $jury): ?>
                    <tr class="data-row">
                        <td><code><?php echo htmlspecialchars($jury['matricule'] ?? ''); ?></code></td>
                        <td><strong><?php echo htmlspecialchars($jury['nom'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($jury['role'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($jury['soutenance'] ?? 'N/A'); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/soutenance/jurys/<?php echo htmlspecialchars($jury['id'] ?? ''); ?>" class="btn-icon btn-view" title="Voir">👁</a>
                                <a href="<?php echo BASE_URL; ?>/admin/soutenance/jurys/<?php echo htmlspecialchars($jury['id'] ?? ''); ?>/delete" class="btn-icon btn-delete" title="Retirer" onclick="return confirm('Confirmer le retrait de ce membre ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state">
                        <p>Aucun jury trouvé</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
