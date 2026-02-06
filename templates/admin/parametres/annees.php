<?php
$title = 'Années académiques';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Années académiques</h1>
        <p class="subtitle">Gestion des périodes académiques</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/annees/create" class="btn btn-primary">+ Nouvelle année</a>
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
                <th>Libellé</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Courante</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($annees) && is_array($annees) && !empty($annees)): ?>
                <?php foreach ($annees as $anneeItem): ?>
                    <tr class="data-row">
                        <td><strong><?php echo htmlspecialchars($anneeItem['libelle'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($anneeItem['date_debut'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($anneeItem['date_fin'] ?? ''); ?></td>
                        <td>
                            <?php if (!empty($anneeItem['courante'])): ?>
                                <span class="status-badge status-actif">Oui</span>
                            <?php else: ?>
                                <span class="status-badge status-inactif">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/annees/<?php echo (int)($anneeItem['id'] ?? 0); ?>/edit" class="btn-icon btn-edit" title="Modifier">✏️</a>
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/annees/<?php echo (int)($anneeItem['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer cette année académique ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state"><p>Aucune année académique enregistrée</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
