<?php
$title = 'Modifier l\'entreprise';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Modifier l'entreprise</h1>
        <p class="subtitle"><?php echo htmlspecialchars($entreprise['raison_sociale'] ?? ''); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres/entreprises" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<?php if (!isset($entreprise) || empty($entreprise)): ?>
    <div class="alert alert-error"><span>Entreprise introuvable.</span></div>
<?php else: ?>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/entreprises/<?php echo (int)$entreprise['id']; ?>/update" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Coordonnées de l'entreprise</h2>

            <div class="form-group">
                <label for="raison_sociale">Raison sociale <span class="required">*</span></label>
                <input type="text" id="raison_sociale" name="raison_sociale" required class="form-control"
                       value="<?php echo htmlspecialchars($entreprise['raison_sociale'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="secteur">Secteur d'activité</label>
                <input type="text" id="secteur" name="secteur" class="form-control"
                       value="<?php echo htmlspecialchars($entreprise['secteur'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="adresse_entreprise">Adresse</label>
                <textarea id="adresse_entreprise" name="adresse_entreprise" rows="2" class="form-control"><?php echo htmlspecialchars($entreprise['adresse'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville" class="form-control"
                       value="<?php echo htmlspecialchars($entreprise['ville'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="telephone_ent">Téléphone</label>
                <input type="tel" id="telephone_ent" name="telephone" class="form-control"
                       value="<?php echo htmlspecialchars($entreprise['telephone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email_ent">Email</label>
                <input type="email" id="email_ent" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($entreprise['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="site_web">Site web</label>
                <input type="url" id="site_web" name="site_web" class="form-control"
                       value="<?php echo htmlspecialchars($entreprise['site_web'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?php echo BASE_URL; ?>/admin/parametres/entreprises" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php endif; ?>
