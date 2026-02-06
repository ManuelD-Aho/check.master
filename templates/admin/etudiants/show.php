<div class="page-header">
    <div class="header-left">
        <h1><?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?></h1>
        <p class="subtitle">Détails de l'étudiant</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo (int)$etudiant['id']; ?>/edit" class="btn btn-primary">Modifier</a>
        <a href="<?php echo BASE_URL; ?>/admin/etudiants" class="btn btn-secondary">Retour</a>
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
            <h2>Informations personnelles</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Numéro d'étudiant</label>
                    <p><code><?php echo htmlspecialchars($etudiant['numero'] ?? ''); ?></code></p>
                </div>
                <div class="info-item">
                    <label>Nom</label>
                    <p><?php echo htmlspecialchars($etudiant['nom']); ?></p>
                </div>
                <div class="info-item">
                    <label>Prénom</label>
                    <p><?php echo htmlspecialchars($etudiant['prenom']); ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><a href="mailto:<?php echo htmlspecialchars($etudiant['email']); ?>"><?php echo htmlspecialchars($etudiant['email']); ?></a></p>
                </div>
                <div class="info-item">
                    <label>Téléphone</label>
                    <p><?php echo htmlspecialchars($etudiant['telephone'] ?? 'Non renseigné'); ?></p>
                </div>
                <div class="info-item">
                    <label>Date de naissance</label>
                    <p><?php echo !empty($etudiant['date_naissance']) ? htmlspecialchars(date('d/m/Y', strtotime($etudiant['date_naissance']))) : 'Non renseignée'; ?></p>
                </div>
                <div class="info-item">
                    <label>Adresse</label>
                    <p><?php echo htmlspecialchars($etudiant['adresse'] ?? 'Non renseignée'); ?></p>
                </div>
                <div class="info-item">
                    <label>Ville</label>
                    <p><?php echo htmlspecialchars($etudiant['ville'] ?? 'Non renseignée'); ?></p>
                </div>
                <div class="info-item">
                    <label>Code postal</label>
                    <p><?php echo htmlspecialchars($etudiant['code_postal'] ?? 'Non renseigné'); ?></p>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h2>Informations académiques</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Filière</label>
                    <p><?php echo htmlspecialchars($etudiant['filiere'] ?? 'Non renseignée'); ?></p>
                </div>
                <div class="info-item">
                    <label>Année d'études</label>
                    <p><?php echo htmlspecialchars($etudiant['annee'] ?? 'Non renseignée'); ?></p>
                </div>
                <div class="info-item">
                    <label>Groupe</label>
                    <p><?php echo htmlspecialchars($etudiant['groupe'] ?? 'Non renseigné'); ?></p>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <span class="status-badge status-<?php echo htmlspecialchars($etudiant['statut'] ?? 'inactif'); ?>">
                        <?php echo htmlspecialchars(ucfirst($etudiant['statut'] ?? 'Inactif')); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Encadreur</label>
                    <p><?php echo !empty($etudiant['encadreur_nom']) ? htmlspecialchars($etudiant['encadreur_nom'] . ' ' . $etudiant['encadreur_prenom']) : 'Non assigné'; ?></p>
                </div>
                <div class="info-item">
                    <label>Sujet de PFE</label>
                    <p><?php echo htmlspecialchars($etudiant['sujet_pfe'] ?? 'Non défini'); ?></p>
                </div>
            </div>
        </section>

        <?php if (!empty($candidatures)): ?>
            <section class="detail-section">
                <h2>Candidatures</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidatures as $candidature): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($candidature['date_candidature'] ?? 'now'))); ?></td>
                                    <td><?php echo htmlspecialchars($candidature['type'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo htmlspecialchars($candidature['statut'] ?? 'en_attente'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($candidature['statut'] ?? 'En attente')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo (int)$candidature['id']; ?>" class="btn-icon btn-view" title="Voir">
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

        <?php if (!empty($rapports)): ?>
            <section class="detail-section">
                <h2>Rapports et documents</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapports as $rapport): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($rapport['date_depot'] ?? 'now'))); ?></td>
                                    <td><?php echo htmlspecialchars($rapport['type'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($rapport['titre'] ?? 'Sans titr
