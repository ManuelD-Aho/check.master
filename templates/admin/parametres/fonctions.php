<?php
$title = 'Fonctions';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Fonctions</h1>
        <p class="subtitle">Types de fonctions du personnel</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/fonctions" class="form-inline-add">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label for="libelle_fonction">Ajouter une fonction</label>
            <input type="text" id="libelle_fonction" name="libelle_fonction" required class="form-control" placeholder="Ex: Directeur de mémoire">
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
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($fonctions) && is_array($fonctions) && !empty($fonctions)): ?>
                <?php foreach ($fonctions as $idx => $fonc): ?>
                    <tr class="data-row">
                        <td><?php echo (int)($idx + 1); ?></td>
                        <td><?php echo htmlspecialchars($fonc['libelle'] ?? ''); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/fonctions/<?php echo (int)($fonc['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette fonction ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="empty-state"><p>Aucune fonction enregistrée</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
