<div class="page-header">
    <div class="header-left">
        <a href="<?php echo BASE_URL; ?>/encadreur/etudiants" class="btn-back">← Retour</a>
        <h1><?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?></h1>
        <p class="subtitle">Détails et suivi de l'étudiant</p>
    </div>
</div>

<div class="two-column-layout">
    <div class="left-column">
        <section class="card">
            <div class="card-header">
                <h2>Informations personnelles</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">Numéro étudiant</label>
                        <p class="info-value"><code><?php echo htmlspecialchars($etudiant['numero']); ?></code></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Nom complet</label>
                        <p class="info-value"><?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Email</label>
                        <p class="info-value"><a href="mailto:<?php echo htmlspecialchars($etudiant['email']); ?>"><?php echo htmlspecialchars($etudiant['email']); ?></a></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Téléphone</label>
                        <p class="info-value"><?php echo htmlspecialchars($etudiant['telephone'] ?? 'Non fourni'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Filière</label>
                        <p class="info-value"><?php echo htmlspecialchars($etudiant['filiere'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Année académique</label>
                        <p class="info-value"><?php echo htmlspecialchars($etudiant['annee'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Adresse</label>
                        <p class="info-value"><?php echo htmlspecialchars($etudiant['adresse'] ?? 'Non fournie'); ?></p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Date d'inscription</label>
                        <p class="info-value"><?php echo $etudiant['date_inscription'] ? date('d/m/Y', strtotime($etudiant['date_inscription'])) : 'N/A'; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>Progression académique</h2>
            </div>
            <div class="card-body">
                <div class="progress-section">
                    <div class="progress-item">
                        <label>Candidature</label>
                        <div class="progress-bar">
                            <div class="progress-fill completed"></div>
                        </div>
                        <span class="progress-status">Complétée</span>
                    </div>
                    <div class="progress-item">
                        <label>Inscription</label>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo (int)($etudiant['inscription_progress'] ?? 0); ?>%"></div>
                        </div>
                        <span class="progress-status"><?php echo (int)($etudiant['inscription_progress'] ?? 0); ?>%</span>
                    </div>
                    <div class="progress-item">
                        <label>Rapport de stage</label>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo (int)($etudiant['rapport_progress'] ?? 0); ?>%"></div>
                        </div>
                        <span class="progress-status"><?php echo (int)($etudiant['rapport_progress'] ?? 0); ?>%</span>
                    </div>
                    <div class="progress-item">
                        <label>Soutenance</label>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo (int)($etudiant['soutenance_progress'] ?? 0); ?>%"></div>
                        </div>
                        <span class="progress-status"><?php echo (int)($etudiant['soutenance_progress'] ?? 0); ?>%</span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="right-column">
        <section class="card">
            <div class="card-header">
                <h2>Actions rapides</h2>
            </div>
            <div class="card-body">
                <div class="action-list">
                    <a href="<?php echo BASE_URL; ?>/encadreur/rapports?etudiant=<?php echo (int)$etudiant['id']; ?>" class="action-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                        <span>Voir les rapports</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/encadreur/aptitude?etudiant=<?php echo (int)$etudiant['id']; ?>" class="action-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                        <span>Valider les aptitudes</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <a href="mailto:<?php echo htmlspecialchars($etudiant['email']); ?>" class="action-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <path d="M22 4l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 4"></path>
                        </svg>
                        <span>Envoyer un email</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>Dernière activité</h2>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php if (!empty($activites)): ?>
                        <?php foreach ($activites as $activite): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <p class="timeline-title"><?php echo htmlspecialchars($activite['titre']); ?></p>
                                    <p class="timeline-date"><?php echo date('d/m/Y H:i', strtotime($activite['date'] ?? '')); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-message">Aucune activité récente</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>
