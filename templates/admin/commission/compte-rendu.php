<?php $title = 'Comptes Rendus'; $layout = 'admin'; ?>
<div class="container">
    <h1>Comptes Rendus de Commission</h1>
    <table class="table">
        <thead><tr><th>ID</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (isset($compteRendus) && is_array($compteRendus)): ?>
            <?php foreach ($compteRendus as $cr): ?>
                <tr><td><?php echo htmlspecialchars((string)($cr->getId() ?? '')); ?></td><td>-</td></tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
