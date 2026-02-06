<?php $title = 'Détails Session'; $layout = 'admin'; ?>
<div class="container">
    <h1>Détails de la Session</h1>
    <a href="/admin/commission/sessions" class="btn">&larr; Retour</a>
    <?php if (isset($session)): ?>
        <div class="card">
            <p><strong>ID :</strong> <?php echo htmlspecialchars((string)($session->getId() ?? '')); ?></p>
        </div>
    <?php else: ?>
        <p>Session introuvable.</p>
    <?php endif; ?>
</div>
