<?php $title = 'Assigner Encadrant'; $layout = 'admin'; ?>
<div class="container">
    <h1>Assigner un Encadrant</h1>
    <a href="/admin/commission/assignation" class="btn">&larr; Retour</a>
    <form method="POST" action="/admin/commission/assignation/<?php echo htmlspecialchars((string)($rapportId ?? '')); ?>">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
        <div class="form-group">
            <label>Encadrant</label>
            <input type="text" name="encadrant" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Assigner</button>
    </form>
</div>
