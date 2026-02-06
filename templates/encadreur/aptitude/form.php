<div class="page-header">
    <div class="header-left">
        <a href="<?php echo BASE_URL; ?>/encadreur/aptitude" class="btn-back">← Retour</a>
        <h1>Validation d'aptitude</h1>
        <p class="subtitle">Évaluez et validez l'aptitude de l'étudiant</p>
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
                <h2>Informations de l'aptitude</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Aptitude</label>
                        <p class="info-value"><?php echo htmlspecialchars($aptitude['nom']); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Étudiant</label>
                        <p class="info-value">
                            <a href="<?php echo BASE_URL; ?>/encadreur/etudiants/<?php echo (int)$aptitude['etudiant_id']; ?>">
                                <?php echo htmlspecialchars($aptitude['etudiant_nom'] . ' ' . $aptitude['etudiant_prenom']); ?>
                            </a>
                        </p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Description</label>
                        <p class="info-value"><?php echo htmlspecialchars($aptitude['description'] ?? 'Non fournie'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Niveau requis</label>
                        <p class="info-value"><?php echo htmlspecialchars($aptitude['niveau_requis'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Catégorie</label>
                        <p class="info-value"><?php echo htmlspecialchars($aptitude['categorie'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Date de création</label>
                        <p class="info-value"><?php echo date('d/m/Y', strtotime($aptitude['date_creation'] ?? '')); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>Preuves et documents</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($aptitude['documents'])): ?>
                    <div class="documents-list">
                        <?php foreach ($aptitude['documents'] as $document): ?>
                            <div class="document-item">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                                    <polyline points="13 2 13 9 20 9"></polyline>
                                </svg>
                                <div class="document-info">
                                    <p class="document-name"><?php echo htmlspecialchars(basename($document['nom'])); ?></p>
                                    <p class="document-meta"><?php echo htmlspecialchars($document['type']); ?> - <?php echo round($document['taille'] / 1024, 2); ?> KB</p>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/encadreur/aptitude/document/<?php echo (int)$document['id']; ?>/download" class="btn btn-sm btn-secondary">Télécharger</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-message">Aucun document joint</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="right-column">
        <section class="card">
            <div class="card-header">
                <h2>Formulaire de validation</h2>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/encadreur/aptitude/<?php echo (int)$aptitude['id']; ?>/submit" class="validation-form">

                    <div class="form-group">
                        <label for="statut" class="form-label">Décision</label>
                        <select id="statut" name="statut" class="form-control" required>
                            <option value="">-- Sélectionner une décision --</option>
                            <option value="validee">Valider l'aptitude</option>
                            <option value="rejetee">Rejeter l'aptitude</option>
                            <option value="en_attente">Laisser en attente</option>
                        </select>
                        <small class="form-help">Sélectionnez la décision concernant cette aptitude</small>
                    </div>

                    <div class="form-group">
                        <label for="niveau_atteint" class="form-label">Niveau atteint</label>
                        <select id="niveau_atteint" name="niveau_atteint" class="form-control" required>
                            <option value="">-- Sélectionner un niveau --</option>
                            <option value="debutant">Débutant</option>
                            <option value="intermediaire">Intermédiaire</option>
                            <option value="avance">Avancé</option>
                            <option value="expert">Expert</option>
                        </select>
                        <small class="form-help">Évaluez le niveau d'acquisition de l'aptitude</small>
                    </div>

                    <div class="form-group">
                        <label for="score" class="form-label">Score de validation (/100)</label>
                        <input type="number" id="score" name="score" class="form-control" min="0" max="100" required placeholder="0">
                        <small class="form-help">Entrez un score entre 0 et 100</small>
                    </div>

                    <div class="form-group">
                        <label for="commentaire" class="form-label">Commentaires d'évaluation</label>
                        <textarea id="commentaire" name="commentaire" class="form-control" rows="5" placeholder="Détaillez les points forts, points à améliorer, observations..."></textarea>
                        <small class="form-help">Vos commentaires seront visibles par l'étudiant</small>
                    </div>

                    <div class="form-group">
                        <label for="notes_internes" class="form-label">Notes internes (optionnel)</label>
                        <textarea id="notes_internes" name="notes_internes" class="form-control" rows="3" placeholder="Notes personnelles non visibles par l'étudiant..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="notify_etudiant" value="1" checked>
                            <span>Notifier l'étudiant de la validation</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Soumettre la validation</button>
                        <a href="<?php echo BASE_URL; ?>/encadreur/aptitude" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
