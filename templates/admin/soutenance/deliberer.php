<?php
$title = 'Délibérer';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Délibérer</h1>
        <p class="subtitle"><?php echo htmlspecialchars($soutenance['sujet'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance/deliberation" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php if (isset($soutenance) && !empty($soutenance)): ?>

<div class="detail-card">
    <div class="detail-section">
        <h2>Résumé de la soutenance</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Étudiant</span>
                <span class="detail-value"><?php echo htmlspecialchars($soutenance['etudiant'] ?? 'N/A'); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Date</span>
                <span class="detail-value"><?php echo htmlspecialchars($soutenance['date'] ?? 'N/A'); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Note</span>
                <span class="detail-value"><?php echo htmlspecialchars($soutenance['note'] ?? 'N/A'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)$soutenance['id']; ?>/deliberer" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Décision du jury</h2>

            <div class="form-group">
                <label for="decision">Décision <span class="required">*</span></label>
                <select id="decision" name="decision" required class="form-control">
                    <option value="">-- Sélectionner une décision --</option>
                    <option value="admis">Admis</option>
                    <option value="ajourne">Ajourné</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mention">Mention</label>
                <select id="mention" name="mention" class="form-control">
                    <option value="">-- Sans mention --</option>
                    <option value="passable">Passable</option>
                    <option value="assez_bien">Assez bien</option>
                    <option value="bien">Bien</option>
                    <option value="tres_bien">Très bien</option>
                    <option value="excellent">Excellent</option>
                </select>
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea id="observations" name="observations" rows="4" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Valider la délibération</button>
            <a href="<?php echo BASE_URL; ?>/admin/soutenance/deliberation" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php else: ?>
    <div class="alert alert-error">
        <span>Soutenance non trouvée.</span>
    </div>
<?php endif; ?>
