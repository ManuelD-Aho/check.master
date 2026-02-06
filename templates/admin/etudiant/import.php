<div class="page-header">
    <div class="header-left">
        <h1>Importer des étudiants</h1>
        <p>Importation par fichier CSV</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/etudiants" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<form method="POST" action="<?php echo BASE_URL; ?>/admin/etudiants/import" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

    <fieldset>
        <legend>Fichier d'importation</legend>

        <div class="form-group">
            <label for="champ-fichier-csv">Sélectionner un fichier CSV <span class="required">*</span></label>
            <input type="file" id="champ-fichier-csv" name="fichier_csv" class="form-control" accept=".csv" required>
        </div>

        <div class="import-instructions">
            <h3>Format attendu du fichier CSV</h3>
            <p>Le fichier doit respecter les conditions suivantes :</p>
            <ul>
                <li>Encodage UTF-8 avec séparateur point-virgule (<code>;</code>) ou virgule (<code>,</code>)</li>
                <li>La première ligne doit contenir les en-têtes de colonnes</li>
                <li>Colonnes attendues : <code>matricule</code>, <code>nom</code>, <code>prenom</code>, <code>email</code>, <code>telephone</code>, <code>date_naissance</code>, <code>lieu_naissance</code>, <code>genre</code>, <code>nationalite</code>, <code>filiere</code>, <code>promotion</code></li>
                <li>Les champs <code>nom</code>, <code>prenom</code> et <code>email</code> sont obligatoires</li>
                <li>Le genre accepte les valeurs <code>M</code> ou <code>F</code></li>
            </ul>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Lancer l'importation</button>
        <a href="<?php echo BASE_URL; ?>/admin/etudiants" class="btn btn-secondary">Annuler</a>
    </div>
</form>
