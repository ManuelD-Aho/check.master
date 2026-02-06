<?php
$title = 'Nouvelle année académique';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Nouvelle année académique</h1>
        <p class="subtitle">Créer une période académique</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres/annees" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/annees" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Détails de l'année</h2>

            <div class="form-group">
                <label for="libelle">Libellé <span class="required">*</span></label>
                <input type="text" id="libelle" name="libelle" required class="form-control" placeholder="Ex: 2024-2025">
            </div>

            <div class="form-group">
                <label for="date_debut">Date de début <span class="required">*</span></label>
                <input type="date" id="date_debut" name="date_debut" required class="form-control">
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin <span class="required">*</span></label>
                <input type="date" id="date_fin" name="date_fin" required class="form-control">
            </div>

            <div class="form-group">
                <label for="courante">Définir comme année courante</label>
                <select id="courante" name="courante" class="form-control">
                    <option value="0">Non</option>
                    <option value="1">Oui</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer l'année</button>
            <a href="<?php echo BASE_URL; ?>/admin/parametres/annees" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
