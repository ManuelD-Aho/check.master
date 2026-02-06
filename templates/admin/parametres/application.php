<?php
$title = 'Paramètres de l\'application';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Paramètres de l'application</h1>
        <p class="subtitle">Configuration générale</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/application" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Informations de l'établissement</h2>

            <div class="form-group">
                <label for="nom_etablissement">Nom de l'établissement <span class="required">*</span></label>
                <input type="text" id="nom_etablissement" name="nom_etablissement" required class="form-control"
                       value="<?php echo htmlspecialchars($settings['nom_etablissement'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="sigle">Sigle</label>
                <input type="text" id="sigle" name="sigle" class="form-control"
                       value="<?php echo htmlspecialchars($settings['sigle'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="adresse_etablissement">Adresse</label>
                <textarea id="adresse_etablissement" name="adresse_etablissement" rows="3" class="form-control"><?php echo htmlspecialchars($settings['adresse_etablissement'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="email_contact">Email de contact</label>
                <input type="email" id="email_contact" name="email_contact" class="form-control"
                       value="<?php echo htmlspecialchars($settings['email_contact'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="telephone_contact">Téléphone</label>
                <input type="tel" id="telephone_contact" name="telephone_contact" class="form-control"
                       value="<?php echo htmlspecialchars($settings['telephone_contact'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-section">
            <h2>Configuration académique</h2>

            <div class="form-group">
                <label for="annee_courante">Année académique courante</label>
                <input type="text" id="annee_courante" name="annee_courante" class="form-control"
                       value="<?php echo htmlspecialchars($settings['annee_courante'] ?? ''); ?>" placeholder="2024-2025">
            </div>

            <div class="form-group">
                <label for="note_passage">Note de passage</label>
                <input type="number" id="note_passage" name="note_passage" min="0" max="20" step="0.5" class="form-control"
                       value="<?php echo htmlspecialchars($settings['note_passage'] ?? '10'); ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Sauvegarder</button>
            <a href="<?php echo BASE_URL; ?>/admin/parametres" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
