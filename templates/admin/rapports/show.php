<div class="page-header">
    <div class="header-left">
        <h1><?php echo htmlspecialchars($rapport['titre'] ?? 'Rapport'); ?></h1>
        <p class="subtitle">Détails du rapport</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/rapports/<?php echo (int)$rapport['id']; ?>/edit" class="btn btn-primary">Modifier</a>
        <a href="<?php echo BASE_URL; ?>/admin/rapports" class="btn btn-secondary">Retour</a>
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
            <h2>Informations du rapport</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Titre</label>
                    <p><?php echo htmlspecialchars($rapport['titre'] ?? 'N/A'); ?></p>
                </div>
                <div class="info-item">
                    <label>Étudiant</label>
                    <p><?php echo htmlspecialchars($rapport['nom'] . ' ' . $rapport['prenom']); ?></p>
                </div>
                <div class="info-item">
                    <label>Type de document</label>
                    <p><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($rapport['type'] ?? 'N/A'))); ?></p>
                </div>
                <div class="info-item">
                    <label>Date de dépôt</label>
                    <p><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($rapport['date_depot'] ?? 'now'))); ?></p>
                </div>
                <div class="info-item">
                    <label>Statut</label>
                    <span class="status-badge status-<?php echo htmlspecialchars($rapport['statut'] ?? 'en_attente'); ?>">
                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $rapport['statut'] ?? 'En attente'))); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Fichier</label>
                    <p>
                        <?php if (!empty($rapport['fichier'])): ?>
                            <a href="<?php echo htmlspecialchars($rapport['fichier']); ?>" class="btn btn-secondary btn-sm" target="_blank">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg> Télécharger
                            </a>
                        <?php else: ?>
                            <em>Non disponible</em>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h2>Description et contexte</h2>
            <div class="info-grid">
                <div class="info-item full-width">
                    <label>Description</label>
                    <p><?php echo nl2br(htmlspecialchars($rapport['description'] ?? 'N/A')); ?></p>
                </div>
                <div class="info-item full-width">
                    <label>Mots-clés</label>
                    <p><?php echo htmlspecialchars($rapport['mots_cles'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h2>Validation et actions</h2>
            <form method="post" action="<?php echo BASE_URL; ?>/admin/rapports/<?php echo (int)$rapport['id']; ?>/validate" class="form-inline">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <div class="form-group">
                    <label for="rapport_statut">Décision</label>
                    <select id="rapport_statut" name="statut" required class="form-control">
                        <option value="">Sélectionner une décision</option>
                        <option value="accepte">Accepter</option>
                        <option value="rejete">Rejeter</option>
                        <option value="en_attente">Remettre en attente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rapport_observation">Observations (optionnel)</label>
                    <textarea id="rapport_observation" name="observation" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Valider le rapport</button>
            </form>
        </section>

        <?php if (!empty($rapport['evaluations'])): ?>
            <section class="detail-section">
                <h2>Évaluations</h2>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Évaluateur</th>
                                <th>Note</th>
                                <th>Date</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapport['evaluations'] as $eval): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($eval['evaluateur'] ?? 'N/A'); ?></td>
                                    <td class="text-center"><strong><?php echo htmlspecialchars($eval['note'] ?? '-'); ?>/20</strong></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($eval['date_evaluation'] ?? 'now'))); ?></td>
                                    <td><?php echo htmlspecialchars($eval['commentaire'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
