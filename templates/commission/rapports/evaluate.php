<?php $title = 'Évaluer Rapport'; $layout = 'admin'; ?>
<div class="container">
    <h1>Évaluation du Rapport</h1>
    <a href="/commission/rapports" class="btn">&larr; Retour</a>
    <?php if (isset($rapport)): ?>
        <form method="POST" action="/commission/evaluer/<?php echo htmlspecialchars((string)($rapport->getId() ?? '')); ?>">
            <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
            <div class="form-group">
                <label>Décision</label>
                <select name="decision" class="form-control">
                    <option value="favorable">Favorable</option>
                    <option value="defavorable">Défavorable</option>
                    <option value="reserve">Avec réserves</option>
                </select>
            </div>
            <div class="form-group">
                <label>Commentaire</label>
                <textarea name="commentaire" class="form-control" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Soumettre l'évaluation</button>
        </form>
    <?php else: ?>
        <p>Rapport introuvable.</p>
    <?php endif; ?>
</div>
