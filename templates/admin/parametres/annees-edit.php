<?php
$title = 'Modifier l\'année académique';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Modifier l'année académique</h1>
        <p class="subtitle"><?php echo htmlspecialchars($annee['libelle'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres/annees" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php if (!isset($annee) || empty($annee)): ?>
    <div class="alert alert-error"><span>Année académique introuvable.</span></div>
<?php else: ?>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/annees/<?php echo (int)$annee['id']; ?>/update" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Détails de l'année</h2>

            <div class="form-group">
                <label for="libelle">Libellé <span class="required">*</span></label>
                <input type="text" id="libelle" name="libelle" required class="form-control"
                       value="<?php echo htmlspecialchars($annee['libelle'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="date_debut">Date de début <span class="required">*</span></label>
                <input type="date" id="date_debut" name="date_debut" required class="form-control"
                       value="<?php echo htmlspecialchars($annee['date_debut'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin <span class="required">*</span></label>
                <input type="date" id="date_fin" name="date_fin" required class="form-control"
                       value="<?php echo htmlspecialchars($annee['date_fin'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="courante">Année courante</label>
                <select id="courante" name="courante" class="form-control">
                    <option value="0" <?php echo empty($annee['courante']) ? 'selected' : ''; ?>>Non</option>
                    <option value="1" <?php echo !empty($annee['courante']) ? 'selected' : ''; ?>>Oui</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/admin/parametres/annees" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php endif; ?>
