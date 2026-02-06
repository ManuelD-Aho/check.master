<?php
$title = 'Modifier la salle';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Modifier la salle</h1>
        <p class="subtitle"><?php echo htmlspecialchars($salle['nom'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres/salles" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php if (!isset($salle) || empty($salle)): ?>
    <div class="alert alert-error"><span>Salle introuvable.</span></div>
<?php else: ?>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/salles/<?php echo (int)$salle['id']; ?>/update" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Informations de la salle</h2>

            <div class="form-group">
                <label for="nom_salle">Nom de la salle <span class="required">*</span></label>
                <input type="text" id="nom_salle" name="nom_salle" required class="form-control"
                       value="<?php echo htmlspecialchars($salle['nom'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="batiment">Bâtiment</label>
                <input type="text" id="batiment" name="batiment" class="form-control"
                       value="<?php echo htmlspecialchars($salle['batiment'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="capacite">Capacité (places)</label>
                <input type="number" id="capacite" name="capacite" min="1" class="form-control"
                       value="<?php echo (int)($salle['capacite'] ?? 0); ?>">
            </div>

            <div class="form-group">
                <label for="disponible">Disponibilité</label>
                <select id="disponible" name="disponible" class="form-control">
                    <option value="1" <?php echo !empty($salle['disponible']) ? 'selected' : ''; ?>>Disponible</option>
                    <option value="0" <?php echo empty($salle['disponible']) ? 'selected' : ''; ?>>Indisponible</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description_salle">Description</label>
                <textarea id="description_salle" name="description_salle" rows="3" class="form-control"><?php echo htmlspecialchars($salle['description'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/admin/parametres/salles" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php endif; ?>
