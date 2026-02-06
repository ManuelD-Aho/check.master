<?php $title = 'Modifier Candidature'; $layout = 'etudiant'; ?>
<div class="container">
    <h1>Modifier la Candidature</h1>
    <a href="/etudiant/candidature" class="btn">&larr; Retour</a>
    <form method="POST" action="/etudiant/candidature/sauvegarder">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label>Sujet du stage</label>
            <input type="text" name="sujet" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </form>
</div>
