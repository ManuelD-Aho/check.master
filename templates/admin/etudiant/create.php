<div class="page-header">
    <div class="header-left">
        <h1>Nouvel étudiant</h1>
        <p>Enregistrer un nouvel étudiant dans le système</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/etudiants" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<form method="POST" action="<?php echo BASE_URL; ?>/admin/etudiants" class="admin-form">
    <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

    <fieldset>
        <legend>Identité</legend>

        <div class="form-group">
            <label for="champ-nom">Nom <span class="required">*</span></label>
            <input type="text" id="champ-nom" name="nom" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="champ-prenom">Prénom <span class="required">*</span></label>
            <input type="text" id="champ-prenom" name="prenom" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="champ-email">Email <span class="required">*</span></label>
            <input type="email" id="champ-email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="champ-telephone">Téléphone</label>
            <input type="text" id="champ-telephone" name="telephone" class="form-control">
        </div>

        <div class="form-group">
            <label for="champ-date-naissance">Date de naissance</label>
            <input type="date" id="champ-date-naissance" name="date_naissance" class="form-control">
        </div>

        <div class="form-group">
            <label for="champ-lieu-naissance">Lieu de naissance</label>
            <input type="text" id="champ-lieu-naissance" name="lieu_naissance" class="form-control">
        </div>

        <div class="form-group">
            <label for="champ-genre">Genre</label>
            <select id="champ-genre" name="genre" class="form-control">
                <option value="">-- Sélectionner --</option>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="champ-nationalite">Nationalité</label>
            <input type="text" id="champ-nationalite" name="nationalite" class="form-control">
        </div>
    </fieldset>

    <fieldset>
        <legend>Informations académiques</legend>

        <div class="form-group">
            <label for="champ-filiere">Filière <span class="required">*</span></label>
            <select id="champ-filiere" name="filiere" class="form-control" required>
                <option value="">-- Choisir une filière --</option>
                <?php if (!empty($filieres)): ?>
                    <?php foreach ($filieres as $fil): ?>
                        <option value="<?php echo htmlspecialchars($fil['id'] ?? ''); ?>">
                            <?php echo htmlspecialchars($fil['libelle'] ?? $fil['nom'] ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="champ-promotion">Promotion</label>
            <input type="text" id="champ-promotion" name="promotion" class="form-control">
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer l'étudiant</button>
        <a href="<?php echo BASE_URL; ?>/admin/etudiants" class="btn btn-secondary">Annuler</a>
    </div>
</form>
