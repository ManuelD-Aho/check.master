<div class="page-header">
    <div class="header-left">
        <h1><?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></h1>
        <p class="subtitle">Détails de l'inscription</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/inscriptions/<?php echo (int)$inscription['id']; ?>/edit" class="btn btn-primary">Modifier</a>
        <a href="<?php echo BASE_URL; ?>/admin/inscriptions" class="btn btn-secondary">Retour</a>
    </div>
</div>

<?php if (isset($flashMessages) && !empty($flashMessages)): ?>
    <div class="alerts">
        <?php foreach ($flashMessages as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                    <span><?php echo htmlspecialchars($message); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="detail-container">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Informations d'inscription</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Numéro d'étudiant</label>
                    <p><code><?php echo htmlspecialchars($inscription['numero'] ?? ''); ?></code></p>
                </div>
                <div class="info-item">
                    <label>Nom complet</label>
                    <p><?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><a href="mailto:<?php echo htmlspecialchars($inscription['email']); ?>"><?php echo htmlspecialchars($inscription['email']); ?></a></p>
                </div>
                <div class="info-item">
                    <label>Téléphone</label>
                    <p><?php echo htmlspecialchars($inscription['telephone'] ?? 'Non renseigné'); ?></p>
                </div>
                <div class="info-item">
                    <label>Année académique</label>
                    <p><?php echo htmlspecialchars($inscription['annee'] ?? 'N/A'); ?></p>
                </div>
                <div class="info-item">
                    <label>Filière</label>
                    <p><?php echo htmlspecialchars($inscription['filiere'] ?? 'N/A'); ?></p>
                </div>
                <div class="info-item">
                    <label>Statut d'inscription</label>
                    <span class="status-badge status-<?php echo htmlspecialchars($inscription['statut'] ?? 'en_attente'); ?>">
                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $inscription['statut'] ?? 'En attente'))); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Date d'inscription</label>
                    <p><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($inscription['date_inscription'] ?? 'now'))); ?></p>
                </div>
            </div>
        </section>

        <?php if (!empty($payments)): ?>
            <section class="detail-section">
                <h2>Paiements</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Référence</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($payment['date_paiement'] ?? 'now'))); ?></td>
                                    <td class="text-right">
                                        <strong><?php echo htmlspecialchars(number_format($payment['montant'] ?? 0, 2, ',', ' ')); ?> DZD</strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['type_paiement'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo htmlspecialchars($payment['statut'] ?? 'en_attente'); ?>">
                                            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['statut'] ?? 'En attente'))); ?>
                                        </span>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($payment['reference'] ?? 'N/A'); ?></code></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/admin/paiements/<?php echo (int)$payment['id']; ?>" class="btn-icon btn-view" title="Voir">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <section class="detail-section">
            <h2>Résumé financier</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Montant total des frais</label>
                    <p class="text-large"><strong><?php echo htmlspecialchars(number_format($inscription['montant_total'] ?? 0, 2, ',', ' ')); ?> DZD</strong></p>
                </div>
                <div class="info-item">
                    <label>Montant payé</label>
                    <p class="text-large"><strong><?php echo htmlspecialchars(number_format($inscription['montant_paye'] ?? 0, 2, ',', ' ')); ?> DZD</strong></p>
                </div>
                <div class="info-item">
                    <label>Montant restant</label>
                    <p class="text-large"><strong><?php echo htmlspecialchars(number_format(($inscription['montant_total'] ?? 0) - ($inscription['montant_paye'] ?? 0), 2, ',', ' ')); ?> DZD</strong></p>
                </div>
            </div>
        </section>
    </div>
</div>
