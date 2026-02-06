<div class="page-header">
    <div class="header-left">
        <a href="<?php echo BASE_URL; ?>/encadreur/rapports" class="btn-back">← Retour</a>
        <h1><?php echo htmlspecialchars($rapport['titre'] ?? 'Rapport'); ?></h1>
        <p class="subtitle">Examen et commentaires du rapport</p>
    </div>
    <div class="header-right">
        <span class="status-badge status-<?php echo htmlspecialchars($rapport['statut'] ?? 'en_attente'); ?>">
            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $rapport['statut'] ?? 'En attente'))); ?>
        </span>
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

<div class="two-column-layout">
    <div class="left-column">
        <section class="card">
            <div class="card-header">
                <h2>Informations du rapport</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Étudiant</label>
                        <p class="info-value">
                            <a href="<?php echo BASE_URL; ?>/encadreur/etudiants/<?php echo (int)$rapport['etudiant_id']; ?>">
                                <?php echo htmlspecialchars($rapport['etudiant_nom'] . ' ' . $rapport['etudiant_prenom']); ?>
                            </a>
                        </p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Entreprise</label>
                        <p class="info-value"><?php echo htmlspecialchars($rapport['entreprise'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Date de début</label>
                        <p class="info-value"><?php echo date('d/m/Y', strtotime($rapport['date_debut'] ?? '')); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Date de fin</label>
                        <p class="info-value"><?php echo date('d/m/Y', strtotime($rapport['date_fin'] ?? '')); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Date de soumission</label>
                        <p class="info-value"><?php echo date('d/m/Y H:i', strtotime($rapport['date_soumission'] ?? '')); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Durée du stage</label>
                        <p class="info-value">
                            <?php 
                                $debut = new DateTime($rapport['date_debut'] ?? 'now');
                                $fin = new DateTime($rapport['date_fin'] ?? 'now');
                                $interval = $debut->diff($fin);
                                echo $interval->days . ' jours';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>Contenu du rapport</h2>
            </div>
            <div class="card-body">
                <div class="rapport-content">
                    <h3><?php echo htmlspecialchars($rapport['titre']); ?></h3>
                    <div class="content-text">
                        <?php echo nl2br(htmlspecialchars($rapport['description'] ?? '')); ?>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!empty($rapport['fichier'])): ?>
            <section class="card">
                <div class="card-header">
                    <h2>Document joint</h2>
                </div>
                <div class="card-body">
                    <div class="file-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                        <div class="file-info">
                            <p class="file-name"><?php echo htmlspecialchars(basename($rapport['fichier'])); ?></p>
                            <p class="file-size"><?php echo isset($rapport['fichier_taille']) ? round($rapport['fichier_taille'] / 1024, 2) . ' KB' : ''; ?></p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/encadreur/rapports/<?php echo (int)$rapport['id']; ?>/download" class="btn btn-secondary">Télécharger</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="right-column">
        <section class="card">
            <div class="card-header">
                <h2>Commentaires (<?php echo count($commentaires ?? []); ?>)</h2>
            </div>
            <div class="card-body">
                <div class="comments-section">
                    <?php if (!empty($commentaires)): ?>
                        <div class="comments-list">
                            <?php foreach ($commentaires as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <h4 class="comment-author"><?php echo htmlspecialchars($comment['auteur_nom']); ?></h4>
                                        <p class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['date_creation'] ?? '')); ?></p>
                                    </div>
                                    <div class="comment-body">
                                        <p><?php echo nl2br(htmlspecialchars($comment['contenu'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-message">Aucun commentaire pour le moment</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>Ajouter un commentaire</h2>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/encadreur/rapports/<?php echo (int)$rapport['id']; ?>/comment" class="comment-form">
                    <div class="form-group">
                        <label for="contenu" class="form-label">Votre commentaire</label>
                        <textarea id="contenu" name="contenu" class="form-control" rows="6" required placeholder="Entrez vos commentaires, suggestions ou remarques..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Ajouter un commentaire</button>
                    </div>
                </form>
            </div>
        </section>

        <?php if ($rapport['statut'] !== 'approuve' && $rapport['statut'] !== 'rejete'): ?>
            <section class="card">
                <div class="card-header">
                    <h2>Validation du rapport</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo BASE_URL; ?>/encadreur/rapports/<?php echo (int)$rapport['id']; ?>/validate" class="validation-form">
                        <div class="form-group">
                            <label for="decision" class="form-label">Décision</label>
                            <select id="decision" name="decision" class="form-control" required>
                                <option value="">-- Sélectionner une action --</option>
                                <option value="approuve">Approuver le rapport</option>
                                <option value="en_cours_review">Marquer comme en cours de révision</option>
                                <option value="rejete">Rejeter le rapport</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes_evaluation" class="form-label">Notes d'évaluation (optionnel)</label>
                            <textarea id="notes_evaluation" name="notes_evaluation" class="form-control" rows="4" placeholder="Entrez vos notes d'évaluation..."></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">Soumettre la décision</button>
                        </div>
                    </form>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
