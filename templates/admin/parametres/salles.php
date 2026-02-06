<?php
$title = 'Salles';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Salles</h1>
        <p class="subtitle">Salles disponibles pour les soutenances</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/salles/create" class="btn btn-primary">+ Ajouter une salle</a>
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
                <th>Nom</th>
                <th>Bâtiment</th>
                <th>Capacité</th>
                <th>Disponible</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($salles) && is_array($salles) && !empty($salles)): ?>
                <?php foreach ($salles as $salleItem): ?>
                    <tr class="data-row">
                        <td><strong><?php echo htmlspecialchars($salleItem['nom'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($salleItem['batiment'] ?? 'N/A'); ?></td>
                        <td><?php echo (int)($salleItem['capacite'] ?? 0); ?> places</td>
                        <td>
                            <?php if (!empty($salleItem['disponible'])): ?>
                                <span class="status-badge status-actif">Oui</span>
                            <?php else: ?>
                                <span class="status-badge status-inactif">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/salles/<?php echo (int)($salleItem['id'] ?? 0); ?>/edit" class="btn-icon btn-edit" title="Modifier">✏️</a>
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/salles/<?php echo (int)($salleItem['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette salle ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state"><p>Aucune salle enregistrée</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
