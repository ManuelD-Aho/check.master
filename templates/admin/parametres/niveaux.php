<?php
$title = 'Niveaux d\'étude';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Niveaux d'étude</h1>
        <p class="subtitle">Configuration des niveaux académiques</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/niveaux" class="form-inline-add">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label for="libelle_niveau">Ajouter un niveau</label>
            <input type="text" id="libelle_niveau" name="libelle_niveau" required class="form-control" placeholder="Ex: Licence 3">
        </div>
        <div class="form-group">
            <label for="code_niveau">Code</label>
            <input type="text" id="code_niveau" name="code_niveau" class="form-control" placeholder="Ex: L3">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Libellé</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($niveaux) && is_array($niveaux) && !empty($niveaux)): ?>
                <?php foreach ($niveaux as $niv): ?>
                    <tr class="data-row">
                        <td><code><?php echo htmlspecialchars($niv['code'] ?? ''); ?></code></td>
                        <td><?php echo htmlspecialchars($niv['libelle'] ?? ''); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/niveaux/<?php echo (int)($niv['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce niveau ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="empty-state"><p>Aucun niveau enregistré</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
