<div class="page-header">
    <div class="header-left">
        <h1>Gestion des Inscriptions</h1>
    </div>
    <div class="header-right">
        <a href="<?= BASE_URL ?>/admin/inscriptions/create" class="btn btn-primary">Nouvelle inscription</a>
    </div>
</div>

<?php if (!empty($flashes)): ?>
    <?php foreach ($flashes as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?= htmlspecialchars($type) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($inscriptions)): ?>
<table class="data-table">
    <thead>
        <tr>
            <th>Étudiant</th>
            <th>Année</th>
            <th>Filière</th>
            <th>Niveau</th>
            <th>Montant total</th>
            <th>Montant payé</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inscriptions as $inscription): ?>
        <tr>
            <td><?= htmlspecialchars($inscription['etudiant_matricule'] ?? $inscription['etudiant'] ?? '') ?></td>
            <td><?= htmlspecialchars($inscription['annee_academique'] ?? '') ?></td>
            <td><?= htmlspecialchars($inscription['filiere'] ?? '') ?></td>
            <td><?= htmlspecialchars($inscription['niveau'] ?? '') ?></td>
            <td><?= htmlspecialchars($inscription['montant_total'] ?? '') ?></td>
            <td><?= htmlspecialchars($inscription['montant_paye'] ?? '') ?></td>
            <td><?= htmlspecialchars($inscription['statut'] ?? '') ?></td>
            <td>
                <a href="<?= BASE_URL ?>/admin/inscriptions/<?= htmlspecialchars($inscription['id']) ?>">Voir</a>
                <a href="<?= BASE_URL ?>/admin/inscriptions/<?= htmlspecialchars($inscription['id']) ?>/versement">Versement</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>Aucune inscription trouvée.</p>
<?php endif; ?>
