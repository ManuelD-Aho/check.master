<div class="page-header">
    <div class="header-left">
        <h1>Nouvelle inscription</h1>
    </div>
    <div class="header-right">
        <a href="<?= BASE_URL ?>/admin/inscriptions" class="btn btn-secondary">Retour</a>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/admin/inscriptions">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf) ?>">

    <div class="form-group">
        <label for="etudiant_matricule">Matricule étudiant</label>
        <input type="text" id="etudiant_matricule" name="etudiant_matricule" required>
    </div>

    <div class="form-group">
        <label for="annee_academique">Année académique</label>
        <input type="text" id="annee_academique" name="annee_academique" required>
    </div>

    <div class="form-group">
        <label for="filiere">Filière</label>
        <input type="text" id="filiere" name="filiere" required>
    </div>

    <div class="form-group">
        <label for="niveau">Niveau</label>
        <input type="text" id="niveau" name="niveau" required>
    </div>

    <div class="form-group">
        <label for="montant_total">Montant total</label>
        <input type="number" id="montant_total" name="montant_total" step="0.01" min="0" required>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
