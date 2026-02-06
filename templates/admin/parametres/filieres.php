<?php
$title = 'Filières';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Filières</h1>
        <p class="subtitle">Programmes de formation disponibles</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/filieres" class="form-inline-add">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label for="libelle_filiere">Ajouter une filière</label>
            <input type="text" id="libelle_filiere" name="libelle_filiere" required class="form-control" placeholder="Nom de la filière">
        </div>
        <div class="form-group">
            <label for="code_filiere">Code</label>
            <input type="text" id="code_filiere" name="code_filiere" class="form-control" placeholder="Ex: INFO">
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
            <?php if (isset($filieres) && is_array($filieres) && !empty($filieres)): ?>
                <?php foreach ($filieres as $fil): ?>
                    <tr class="data-row">
                        <td><code><?php echo htmlspecialchars($fil['code'] ?? ''); ?></code></td>
                        <td><?php echo htmlspecialchars($fil['libelle'] ?? ''); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/filieres/<?php echo (int)($fil['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette filière ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="empty-state"><p>Aucune filière enregistrée</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
