<div class="page-header">
    <div class="header-left">
        <h1>Enregistrer un versement</h1>
    </div>
    <div class="header-right">
        <a href="<?= BASE_URL ?>/admin/inscriptions" class="btn btn-secondary">Retour</a>
    </div>
</div>

<?php if ($inscription === null): ?>
<p>Inscription introuvable.</p>
<?php else: ?>

<div class="section">
    <h2>Résumé de l'inscription</h2>
    <table>
        <tr><th>Étudiant</th><td><?= htmlspecialchars($inscription['etudiant_matricule'] ?? $inscription['etudiant'] ?? '') ?></td></tr>
        <tr><th>Filière</th><td><?= htmlspecialchars($inscription['filiere'] ?? '') ?></td></tr>
        <tr><th>Niveau</th><td><?= htmlspecialchars($inscription['niveau'] ?? '') ?></td></tr>
        <tr><th>Montant total</th><td><?= htmlspecialchars($inscription['montant_total'] ?? '') ?></td></tr>
        <tr><th>Montant payé</th><td><?= htmlspecialchars($inscription['montant_paye'] ?? '') ?></td></tr>
        <tr><th>Statut</th><td><?= htmlspecialchars($inscription['statut'] ?? '') ?></td></tr>
    </table>
</div>

<div class="section">
    <h2>Versements existants</h2>
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
</div>

<div class="section">
    <h2>Nouveau versement</h2>
    <form method="POST" action="<?= BASE_URL ?>/admin/inscriptions/<?= htmlspecialchars($inscription['id']) ?>/versement">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label for="montant">Montant</label>
            <input type="number" id="montant" name="montant" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="mode_paiement">Mode de paiement</label>
            <select id="mode_paiement" name="mode_paiement" required>
                <option value="">-- Sélectionner --</option>
                <option value="Espèces">Espèces</option>
                <option value="Chèque">Chèque</option>
                <option value="Virement">Virement</option>
                <option value="Mobile Money">Mobile Money</option>
            </select>
        </div>

        <div class="form-group">
            <label for="reference">Référence</label>
            <input type="text" id="reference" name="reference">
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer le versement</button>
    </form>
</div>

<?php endif; ?>
