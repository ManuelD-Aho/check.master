<?php
$title = 'Rôles de jury';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Rôles de jury</h1>
        <p class="subtitle">Définition des rôles au sein des jurys</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/roles-jury" class="form-inline-add">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label for="libelle_role">Ajouter un rôle</label>
            <input type="text" id="libelle_role" name="libelle_role" required class="form-control" placeholder="Ex: Rapporteur">
        </div>
        <div class="form-group">
            <label for="description_role">Description</label>
            <input type="text" id="description_role" name="description_role" class="form-control" placeholder="Description courte">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Libellé</th>
                <th>Description</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($rolesJury) && is_array($rolesJury) && !empty($rolesJury)): ?>
                <?php foreach ($rolesJury as $roleIdx => $roleItem): ?>
                    <tr class="data-row">
                        <td><?php echo (int)($roleIdx + 1); ?></td>
                        <td><strong><?php echo htmlspecialchars($roleItem['libelle'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($roleItem['description'] ?? ''); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/roles-jury/<?php echo (int)($roleItem['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce rôle de jury ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="empty-state"><p>Aucun rôle de jury défini</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
