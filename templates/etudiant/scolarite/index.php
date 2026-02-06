<div class="scolarite-container">
    <div class="scolarite-header">
        <h1>Gestion Scolaire</h1>
        <p>Inscription et paiements</p>
    </div>

    <div class="scolarite-content">
        <section class="inscription-section">
            <h2>État d'inscription</h2>
            <?php if (!empty($inscriptions)): ?>
                <div class="inscriptions-list">
                    <?php foreach ($inscriptions as $inscription): ?>
                        <div class="inscription-card">
                            <div class="inscription-header-card">
                                <div class="year-badge"><?php echo htmlspecialchars($inscription['annee'] ?? ''); ?></div>
                                <span class="inscription-status <?php echo 'status-' . strtolower($inscription['statut'] ?? 'en_attente'); ?>">
                                    <?php echo htmlspecialchars($inscription['statut'] ?? 'En attente'); ?>
                                </span>
                            </div>
                            <div class="inscription-details">
                                <div class="detail-item">
                                    <span class="detail-label">Date d'inscription</span>
                                    <span class="detail-value"><?php echo $inscription['date_inscription'] ? date('d/m/Y', strtotime($inscription['date_inscription'])) : '-'; ?></span>
                                </div>
                                <div class="detail-item"><span class="detail-label">Filière</span><span class="detail-value"><?php echo htmlspecialchars($inscription['filiere'] ?? '-'); ?></span></div>
                                <div class="detail-item"><span class="detail-label">Montant</span><span class="detail-value"><?php echo htmlspecialchars($inscription['montant'] ?? '-'); ?> €</span></div>
                                <div class="detail-item"><span class="detail-label">Payé</span><span class="detail-value"><?php echo htmlspecialchars($inscription['montant_paye'] ?? '0'); ?> €</span></div>
                            </div>
                            <div class="inscription-actions">
                                <a href="<?php echo BASE_URL; ?>/etudiant/scolarite/<?php echo $inscription['id']; ?>" class="btn-secondary">Détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state"><p>Aucune inscription trouvée</p></div>
            <?php endif; ?>
        </section>

        <section class="payments-section">
            <h2>Paiements</h2>
            <div class="payment-summary">
                <div class="summary-card"><div class="summary-label">Montant total dû</div><div class="summary-value"><?php echo htmlspecialchars($totalDue ?? '0'); ?> €</div></div>
                <div class="summary-card"><div class="summary-label">Montant payé</div><div class="summary-value paid"><?php echo htmlspecialchars($totalPaid ?? '0'); ?> €</div></div>
                <div class="summary-card"><div class="summary-label">Solde restant</div><div class="summary-value remaining"><?php echo htmlspecialchars($balanceRemaining ?? '0'); ?> €</div></div>
            </div>

            <?php if (!empty($payments)): ?>
                <div class="payments-list">
                    <table>
                        <thead>
                            <tr><th>Date</th><th>Montant</th><th>Mode de paiement</th><th>Référence</th><th>Statut</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo $payment['date_paiement'] ? date('d/m/Y', strtotime($payment['date_paiement'])) : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($payment['montant'] ?? '-'); ?> €</td>
                                    <td><?php echo htmlspecialchars($payment['mode_paiement'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($payment['reference'] ?? '-'); ?></td>
                                    <td><span class="payment-status <?php echo 'status-' . strtolower($payment['statut'] ?? 'en_attente'); ?>"><?php echo htmlspecialchars($payment['statut'] ?? 'En attente'); ?></span></td>
                                    <td><a href="<?php echo BASE_URL; ?>/etudiant/scolarite/paiement/<?php echo $payment['id']; ?>" class="link-view">Voir</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state"><p>Aucun paiement enregistré</p></div>
            <?php endif; ?>

            <?php if ((float)$balanceRemaining > 0): ?>
                <div class="payment-action">
                    <a href="<?php echo BASE_URL; ?>/etudiant/scolarite/paiement/nouveau" class="btn-primary">Effectuer un paiement</a>
                </div>
            <?php endif; ?>
        </section>

        <section class="documents-section">
            <h2>Documents</h2>
            <div class="documents-list">
                <a href="<?php echo BASE_URL; ?>/etudiant/scolarite/attestation" class="document-item"><div class="doc-icon">📄</div><div class="doc-info"><div class="doc-name">Attestation de scolarité</div><div class="doc-size">PDF</div></div><div class="doc-download">→</div></a>
                <a href="<?php echo BASE_URL; ?>/etudiant/scolarite/recu" class="document-item"><div class="doc-icon">📋</div><div class="doc-info"><div class="doc-name">Reçu de paiement</div><div class="doc-size">PDF</div></div><div class="doc-download">→</div></a>
            </div>
        </section>
    </div>
</div>

<style>
.scolarite-container { padding: 2rem; max-width: 1000px; margin: 0 auto; }
.scolarite-header { margin-bottom: 2rem; }
.scolarite-header h1 { font-size: 2.5rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #1a1a1a; }
.scolarite-header p { color: #666; margin: 0; font-size: 1.1rem; }
.scolarite-content { display: flex; flex-direction: column; gap: 2.5rem; }
.scolarite-content h2 { font-size: 1.3rem; font-weight: 700; margin: 0 0 1.5rem 0; color: #1a1a1a; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem; }
.inscriptions-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
.inscription-card { border: 1px solid #e5e5e5; border-radius: 12px; padding: 1.5rem; background: white; transition: all 0.3s ease; }
.inscription-card:hover { border-color: #667eea; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1); }
.inscription-header-card { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.year-badge { background: #667eea; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; font-size: 0.9rem; }
.inscription-status { padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
.inscription-status.status-en_attente { background: #fff3cd; color: #856404; }
.inscription-status.status-payée { background: #d4edda; color: #155724; }
.inscription-details { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
.detail-item { display: flex; flex-direction: column; }
.detail-label { font-size: 0.85rem; color: #999; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
.detail-value { font-size: 0.95rem; color: #333; font-weight: 600; }
.payment-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.summary-card { background: white; border: 1px solid #e5e5e5; border-radius: 12px; padding: 1.5rem; text-align: center; }
.summary-label { font-size: 0.85rem; color: #999; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.75rem; }
.summary-value { font-siz
