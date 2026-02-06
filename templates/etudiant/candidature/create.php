<?php $title = 'Créer Candidature'; $layout = 'etudiant'; ?>
<div class="container">
    <h1>Nouvelle Candidature de Stage</h1>
    <a href="/etudiant/candidature" class="btn">&larr; Retour</a>
    <form method="POST" action="/etudiant/candidature/sauvegarder">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label>Sujet du stage</label>
            <input type="text" name="sujet" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Entreprise</label>
            <input type="text" name="entreprise" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Date de début</label>
            <input type="date" name="date_debut" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Date de fin</label>
            <input type="date" name="date_fin" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </form>
</div>
