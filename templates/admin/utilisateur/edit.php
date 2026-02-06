<?php if (!isset($utilisateur) || $utilisateur === null): ?>
<div class="page-header">
    <div class="header-left">
        <h1>Modification impossible</h1>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs" class="btn btn-secondary">← Revenir à la liste</a>
    </div>
</div>
<div class="alerts">
    <div class="alert alert-error">
        <span>L'utilisateur demandé est introuvable. Veuillez vérifier l'identifiant.</span>
    </div>
</div>
<?php else: ?>
<?php $idUtil = (int)($utilisateur['id'] ?? 0); ?>
<div class="page-header">
    <div class="header-left">
        <h1>Modifier l'utilisateur</h1>
        <p class="subtitle"><?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo $idUtil; ?>" class="btn btn-secondary">← Retour à la fiche</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo $idUtil; ?>" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Identité</h2>

            <div class="form-group">
                <label for="champ-nom">Nom <span class="required">*</span></label>
                <input type="text" id="champ-nom" name="nom" required maxlength="150"
                       value="<?php echo htmlspecialchars($utilisateur['nom'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="champ-prenom">Prénom <span class="required">*</span></label>
                <input type="text" id="champ-prenom" name="prenom" required maxlength="150"
                       value="<?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="champ-email">Email <span class="required">*</span></label>
                <input type="email" id="champ-email" name="email" required maxlength="255"
                       value="<?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="champ-mdp">Mot de passe</label>
                <input type="password" id="champ-mdp" name="mot_de_passe" minlength="8"
                       placeholder="Laisser vide pour ne pas modifier">
            </div>
        </div>

        <div class="form-section">
            <h2>Affectation</h2>

            <div class="form-group">
                <label for="champ-groupe">Groupe <span class="required">*</span></label>
                <select id="champ-groupe" name="groupe" required>
                    <option value="">-- Choisir un groupe --</option>
                    <?php if (!empty($groupes)): ?>
                        <?php $grpActuel = $utilisateur['groupe_id'] ?? $utilisateur['groupe'] ?? ''; ?>
                        <?php foreach ($groupes as $grp): ?>
                            <?php $valGrp = $grp['id'] ?? ''; ?>
                            <option value="<?php echo htmlspecialchars($valGrp); ?>"<?php echo ($valGrp == $grpActuel) ? ' selected' : ''; ?>>
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
                        <?php $typeActuel = $utilisateur['type_id'] ?? $utilisateur['type'] ?? ''; ?>
                        <?php foreach ($types as $tp): ?>
                            <?php $valTp = $tp['id'] ?? ''; ?>
                            <option value="<?php echo htmlspecialchars($valTp); ?>"<?php echo ($valTp == $typeActuel) ? ' selected' : ''; ?>>
                                <?php echo htmlspecialchars($tp['nom'] ?? $tp['libelle'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo $idUtil; ?>" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
<?php endif; ?>
