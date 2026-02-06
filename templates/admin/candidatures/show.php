<div class="page-header">
    <div class="header-left">
        <h1><?php echo htmlspecialchars($candidature['sujet'] ?? 'Candidature'); ?></h1>
        <p class="subtitle">Détails de la candidature</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo (int)$candidature['id']; ?>/edit" class="btn btn-primary">Modifier</a>
        <a href="<?php echo BASE_URL; ?>/admin/candidatures" class="btn btn-secondary">Retour</a>
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
            <h2>Informations générales</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Étudiant</label>
                    <p><strong><?php echo htmlspecialchars($candidature['nom'] . ' ' . $candidature['prenom']); ?></strong></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><a href="mailto:<?php echo htmlspecialchars($candidature['email']); ?>"><?php echo htmlspecialchars($candidature['email']); ?></a></p>
                </div>
                <div class="info-item">
                    <label>Type de candidature</label>
                    <p><?php echo htmlspecialchars(ucfirst($candidature['type'] ?? 'N/A')); ?></p>
                </div>
                <div class="info-item">
                    <label>Sujet</label>
                    <p><?php echo htmlspecialchars($candidature['sujet'] ?? 'N/A'); ?></p>
                </div>
                <div class="info-item">
                    <label>Date de candidature</label>
                    <p><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($candidature['date_candidature'] ?? 'now'))); ?></p>
                </div>
                <div class="info-item">
                    <label>Statut</label>
                    <span class="status-badge status-<?php echo htmlspecialchars($candidature['statut'] ?? 'en_attente'); ?>">
                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $candidature['statut'] ?? 'En attente'))); ?>
                    </span>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h2>Description et documents</h2>
            <div class="info-grid">
                <div class="info-item full-width">
                    <label>Description</label>
                    <p><?php echo nl2br(htmlspecialchars($candidature['description'] ?? 'N/A')); ?></p>
                </div>
                <?php if (!empty($candidature['document_cv'])): ?>
                    <div class="info-item">
                        <label>CV</label>
                        <p><a href="<?php echo htmlspecialchars($candidature['document_cv']); ?>" target="_blank" class="btn-link">Télécharger CV</a></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($candidature['document_lettre'])): ?>
                    <div class="info-item">
                        <label>Lettre de motivation</label>
                        <p><a href="<?php echo htmlspecialchars($candidature['document_lettre']); ?>" target="_blank" class="btn-link">Télécharger lettre</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="detail-section">
            <h2>Validation</h2>
            <form method="post" action="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo (int)$candidature['id']; ?>/validate" class="form-inline">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <div class="form-group">
                    <label for="validation_statut">Décision</label>
                    <select id="validation_statut" name="statut" required class="form-control">
                        <option value="">Sélectionner une décision</option>
                        <option value="acceptee">Accepter</option>
                        <option value="rejetee">Rejeter</option>
                        <option value="en_attente">Remettre en attente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="validation_commentaire">Commentaire (optionnel)</label>
                    <textarea id="validation_commentaire" name="commentaire" class="form-control" rows="4"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Valider la décision</button>
            </form>
        </section>

        <?php if (!empty($candidature['commentaires'])): ?>
            <section class="detail-section">
                <h2>Historique de validation</h2>
                <div class="timeline">
                    <?php foreach ($candidature['commentaires'] as $commentaire): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h4><?php echo htmlspecialchars($commentaire['validateur'] ?? 'Admin'); ?></h4>
                                <p><?php echo htmlspecialchars($commentaire['commentaire']); ?></p>
                                <small><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($commentaire['date_validation'] ?? 'now'))); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
