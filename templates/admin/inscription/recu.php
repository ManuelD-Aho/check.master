<?php if ($versement === null): ?>
<p>Versement introuvable.</p>
<?php else: ?>

<div class="recu-container" style="max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #000;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h1>Reçu de Paiement</h1>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <th style="text-align: left; padding: 8px 0;">N° Reçu</th>
            <td><?= htmlspecialchars($versement['recu_numero'] ?? '') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 8px 0;">Date</th>
            <td><?= htmlspecialchars($versement['date_versement'] ?? '') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 8px 0;">Étudiant</th>
            <td><?= htmlspecialchars($versement['etudiant'] ?? $versement['etudiant_matricule'] ?? '') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 8px 0;">Montant</th>
            <td><?= htmlspecialchars($versement['montant'] ?? '') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 8px 0;">Mode de paiement</th>
            <td><?= htmlspecialchars($versement['mode_paiement'] ?? '') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 8px 0;">Référence</th>
            <td><?= htmlspecialchars($versement['reference'] ?? '') ?></td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" class="btn btn-primary no-print">Imprimer</button>
    </div>
</div>

<?php endif; ?>
