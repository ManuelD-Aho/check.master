<?php
$title = 'Entreprises';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Entreprises</h1>
        <p class="subtitle">Entreprises partenaires et lieux de stage</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/entreprises/create" class="btn btn-primary">+ Ajouter une entreprise</a>
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

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Raison sociale</th>
                <th>Secteur</th>
                <th>Ville</th>
                <th>Contact</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($entreprises) && is_array($entreprises) && !empty($entreprises)): ?>
                <?php foreach ($entreprises as $ent): ?>
                    <tr class="data-row">
                        <td><strong><?php echo htmlspecialchars($ent['raison_sociale'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($ent['secteur'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($ent['ville'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($ent['telephone'] ?? 'N/A'); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/entreprises/<?php echo (int)($ent['id'] ?? 0); ?>/edit" class="btn-icon btn-edit" title="Modifier">✏️</a>
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/entreprises/<?php echo (int)($ent['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette entreprise ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state"><p>Aucune entreprise enregistrée</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
