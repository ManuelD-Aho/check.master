<?php
$title = 'Modifier la soutenance';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Modifier la soutenance</h1>
        <p class="subtitle"><?php echo htmlspecialchars($soutenance['sujet'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)($soutenance['id'] ?? 0); ?>" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php if (!isset($soutenance) || empty($soutenance)): ?>
    <div class="alert alert-error">
        <span>Soutenance non trouvée.</span>
    </div>
<?php else: ?>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)$soutenance['id']; ?>/update" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Informations de la soutenance</h2>

            <div class="form-group">
                <label for="etudiant_id">Étudiant <span class="required">*</span></label>
                <select id="etudiant_id" name="etudiant_id" required class="form-control">
                    <option value="<?php echo htmlspecialchars($soutenance['etudiant_id'] ?? ''); ?>" selected>
                        <?php echo htmlspecialchars($soutenance['etudiant'] ?? ''); ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="sujet">Sujet <span class="required">*</span></label>
                <input type="text" id="sujet" name="sujet" required class="form-control"
                       value="<?php echo htmlspecialchars($soutenance['sujet'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="date_soutenance">Date et heure <span class="required">*</span></label>
                <input type="datetime-local" id="date_soutenance" name="date_soutenance" required class="form-control"
                       value="<?php echo htmlspecialchars($soutenance['date'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="salle_id">Salle <span class="required">*</span></label>
                <select id="salle_id" name="salle_id" required class="form-control">
                    <option value="<?php echo htmlspecialchars($soutenance['salle_id'] ?? ''); ?>" selected>
                        <?php echo htmlspecialchars($soutenance['salle'] ?? ''); ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea id="observations" name="observations" rows="4" class="form-control"><?php echo htmlspecialchars($soutenance['observations'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)$soutenance['id']; ?>" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php endif; ?>
