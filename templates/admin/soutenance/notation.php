<?php
$title = 'Notation de la soutenance';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Notation de la soutenance</h1>
        <p class="subtitle"><?php echo htmlspecialchars($soutenance['sujet'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)($soutenance['id'] ?? 0); ?>" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php if (isset($soutenance) && !empty($soutenance)): ?>

<div class="detail-card">
    <div class="detail-section">
        <h2>Informations</h2>
        <p><strong>Étudiant :</strong> <?php echo htmlspecialchars($soutenance['etudiant'] ?? 'N/A'); ?></p>
        <p><strong>Date :</strong> <?php echo htmlspecialchars($soutenance['date'] ?? 'N/A'); ?></p>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)$soutenance['id']; ?>/notation" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Notes</h2>

            <?php if (isset($notes) && is_array($notes)): ?>
                <?php foreach ($notes as $index => $note): ?>
                    <div class="form-group">
                        <label for="note_<?php echo (int)$index; ?>"><?php echo htmlspecialchars($note['critere'] ?? 'Critère ' . ($index + 1)); ?></label>
                        <input type="number" id="note_<?php echo (int)$index; ?>" name="notes[<?php echo (int)$index; ?>]" min="0" max="20" step="0.25" class="form-control"
                               value="<?php echo htmlspecialchars($note['valeur'] ?? ''); ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="form-group">
                    <label for="note_finale">Note finale <span class="required">*</span></label>
                    <input type="number" id="note_finale" name="note_finale" min="0" max="20" step="0.25" required class="form-control">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="remarques">Remarques du jury</label>
                <textarea id="remarques" name="remarques" rows="4" class="form-control"><?php echo htmlspecialchars($soutenance['remarques'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer les notes</button>
            <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)$soutenance['id']; ?>" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php else: ?>
    <div class="alert alert-error">
        <span>Soutenance non trouvée.</span>
    </div>
<?php endif; ?>
