<div class="page-header">
    <div class="header-left">
        <h1>Nouvel utilisateur</h1>
        <p class="subtitle">Remplissez le formulaire ci-dessous pour créer un compte</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs" class="btn btn-secondary">← Revenir à la liste</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/utilisateurs" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Identité</h2>

            <div class="form-group">
                <label for="champ-nom">Nom <span class="required">*</span></label>
                <input type="text" id="champ-nom" name="nom" required maxlength="150" placeholder="Entrez le nom de famille">
            </div>

            <div class="form-group">
                <label for="champ-prenom">Prénom <span class="required">*</span></label>
                <input type="text" id="champ-prenom" name="prenom" required maxlength="150" placeholder="Entrez le prénom">
            </div>

            <div class="form-group">
                <label for="champ-email">Email <span class="required">*</span></label>
                <input type="email" id="champ-email" name="email" required maxlength="255" placeholder="adresse@exemple.com">
            </div>

            <div class="form-group">
                <label for="champ-mdp">Mot de passe <span class="required">*</span></label>
                <input type="password" id="champ-mdp" name="mot_de_passe" required minlength="8" placeholder="Minimum 8 caractères">
            </div>
        </div>

        <div class="form-section">
            <h2>Affectation</h2>

            <div class="form-group">
                <label for="champ-groupe">Groupe <span class="required">*</span></label>
                <select id="champ-groupe" name="groupe" required>
                    <option value="">-- Choisir un groupe --</option>
                    <?php if (!empty($groupes)): ?>
                        <?php foreach ($groupes as $grp): ?>
                            <option value="<?php echo htmlspecialchars($grp['id'] ?? ''); ?>">
                                <?php echo htmlspecialchars($grp['nom'] ?? $grp['libelle'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="champ-type">Type <span class="required">*</span></label>
                <select id="champ-type" name="type" required>
                    <option value="">-- Choisir un type --</option>
                    <?php if (!empty($types)): ?>
                        <?php foreach ($types as $tp): ?>
                            <option value="<?php echo htmlspecialchars($tp['id'] ?? ''); ?>">
                                <?php echo htmlspecialchars($tp['nom'] ?? $tp['libelle'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer le compte</button>
            <a href="<?php echo BASE_URL; ?>/admin/utilisateurs" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
