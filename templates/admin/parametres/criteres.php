<?php
$title = 'Critères d\'évaluation';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Critères d'évaluation</h1>
        <p class="subtitle">Barème et critères de notation des soutenances</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/criteres" class="form-inline-add">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label for="libelle_critere">Ajouter un critère</label>
            <input type="text" id="libelle_critere" name="libelle_critere" required class="form-control" placeholder="Ex: Qualité du rapport">
        </div>
        <div class="form-group">
            <label for="coefficient">Coefficient</label>
            <input type="number" id="coefficient" name="coefficient" min="1" max="10" step="1" class="form-control" value="1">
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
                <th>Coefficient</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($criteres) && is_array($criteres) && !empty($criteres)): ?>
                <?php foreach ($criteres as $critIdx => $crit): ?>
                    <tr class="data-row">
                        <td><?php echo (int)($critIdx + 1); ?></td>
                        <td><?php echo htmlspecialchars($crit['libelle'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($crit['coefficient'] ?? '1'); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/criteres/<?php echo (int)($crit['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce critère ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="empty-state"><p>Aucun critère défini</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
