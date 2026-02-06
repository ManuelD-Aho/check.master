<?php $title = 'Créer Rapport'; $layout = 'etudiant'; ?>
<div class="container">
    <h1>Choisir un Modèle de Rapport</h1>
    <a href="/etudiant/rapport" class="btn">&larr; Retour</a>
    <form method="POST" action="/etudiant/rapport/creer">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label>Modèle</label>
            <select name="modele" class="form-control">
                <option value="standard">Rapport Standard</option>
                <option value="recherche">Rapport de Recherche</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Créer le rapport</button>
    </form>
</div>
