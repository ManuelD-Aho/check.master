<?php $title = 'Informations du Rapport'; $layout = 'etudiant'; ?>
<div class="container">
    <h1>Informations du Rapport</h1>
    <a href="/etudiant/rapport" class="btn">&larr; Retour</a>
    <form method="POST" action="/etudiant/rapport/informations">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label>Titre du rapport</label>
            <input type="text" name="titre" class="form-control">
        </div>
        <div class="form-group">
            <label>Résumé</label>
            <textarea name="resume" class="form-control" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label>Mots-clés</label>
            <input type="text" name="mots_cles" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
