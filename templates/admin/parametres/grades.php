<?php
$title = 'Grades';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Grades</h1>
        <p class="subtitle">Niveaux de grade académique</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/grades" class="form-inline-add">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label for="libelle_grade">Ajouter un grade</label>
            <input type="text" id="libelle_grade" name="libelle_grade" required class="form-control" placeholder="Ex: Professeur titulaire">
        </div>
        <div class="form-group">
            <label for="abreviation_grade">Abréviation</label>
            <input type="text" id="abreviation_grade" name="abreviation_grade" class="form-control" placeholder="Ex: Pr">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Abréviation</th>
                <th>Libellé</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($grades) && is_array($grades) && !empty($grades)): ?>
                <?php foreach ($grades as $gradeItem): ?>
                    <tr class="data-row">
                        <td><code><?php echo htmlspecialchars($gradeItem['abreviation'] ?? ''); ?></code></td>
                        <td><?php echo htmlspecialchars($gradeItem['libelle'] ?? ''); ?></td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/parametres/grades/<?php echo (int)($gradeItem['id'] ?? 0); ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce grade ?');">🗑</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="empty-state"><p>Aucun grade enregistré</p></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
