<div class="page-header">
    <div class="header-left">
        <h1>Détails de l'inscription</h1>
    </div>
    <div class="header-right">
        <a href="<?= BASE_URL ?>/admin/inscriptions" class="btn btn-secondary">Retour</a>
    </div>
</div>

<?php if ($inscription === null): ?>
<p>Inscription introuvable.</p>
<?php else: ?>

<div class="section">
    <h2>Détails de l'inscription</h2>
    <table>
        <tr><th>ID</th><td><?= htmlspecialchars($inscription['id'] ?? '') ?></td></tr>
        <tr><th>Étudiant</th><td><?= htmlspecialchars($inscription['etudiant_matricule'] ?? $inscription['etudiant'] ?? '') ?></td></tr>
        <tr><th>Année académique</th><td><?= htmlspecialchars($inscription['annee_academique'] ?? '') ?></td></tr>
        <tr><th>Filière</th><td><?= htmlspecialchars($inscription['filiere'] ?? '') ?></td></tr>
        <tr><th>Niveau</th><td><?= htmlspecialchars($inscription['niveau'] ?? '') ?></td></tr>
        <tr><th>Montant total</th><td><?= htmlspecialchars($inscription['montant_total'] ?? '') ?></td></tr>
        <tr><th>Montant payé</th><td><?= htmlspecialchars($inscription['montant_paye'] ?? '') ?></td></tr>
        <tr><th>Statut</th><td><?= htmlspecialchars($inscription['statut'] ?? '') ?></td></tr>
        <tr><th>Date d'inscription</th><td><?= htmlspecialchars($inscription['date_inscription'] ?? '') ?></td></tr>
    </table>
</div>

<div class="section">
    <h2>Historique des versements</h2>
    <?php if (!empty($versements)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Montant</th>
                <th>Mode de paiement</th>
                <th>Référence</th>
                <th>N° Reçu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($versements as $versement): ?>
            <tr>
                <td><?= htmlspecialchars($versement['date_versement'] ?? '') ?></td>
                <td><?= htmlspecialchars($versement['montant'] ?? '') ?></td>
                <td><?= htmlspecialchars($versement['mode_paiement'] ?? '') ?></td>
                <td><?= htmlspecialchars($versement['reference'] ?? '') ?></td>
                <td><?= htmlspecialchars($versement['recu_numero'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>Aucun versement enregistré.</p>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/admin/inscriptions/<?= htmlspecialchars($inscription['id']) ?>/versement" class="btn btn-primary">Enregistrer un versement</a>
</div>

<?php endif; ?>
