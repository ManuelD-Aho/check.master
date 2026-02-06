<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Tableau de bord Encadreur</h1>
        <p class="subtitle">Bienvenue, <?php echo htmlspecialchars($encadreur['prenom'] ?? ''); ?></p>
    </div>

    <div class="status-cards-grid">
        <div class="status-card students-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                </svg>
            </div>
            <h3>Étudiants assignés</h3>
            <div class="card-content">
                <p class="card-value"><?php echo (int)($studentCount ?? 0); ?></p>
                <p class="card-label">en total</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/encadreur/etudiants" class="card-link">Voir tous →</a>
        </div>

        <div class="status-card rapports-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                    <polyline points="13 2 13 9 20 9"></polyline>
                </svg>
            </div>
            <h3>Rapports en attente</h3>
            <div class="card-content">
                <p class="card-value"><?php echo (int)($rapportsEnAttente ?? 0); ?></p>
                <p class="card-label">à examiner</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/encadreur/rapports" class="card-link">Examiner →</a>
        </div>

        <div class="status-card aptitudes-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                </svg>
            </div>
            <h3>Aptitudes à valider</h3>
            <div class="card-content">
                <p class="card-value"><?php echo (int)($aptitudesEnAttente ?? 0); ?></p>
                <p class="card-label">en attente</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/encadreur/aptitude" class="card-link">Valider →</a>
        </div>
    </div>

    <div class="dashboard-sections">
        <section class="dashboard-section">
            <div class="section-header">
                <h2>Étudiants récents</h2>
                <a href="<?php echo BASE_URL; ?>/encadreur/etudiants" class="link-more">Voir tous →</a>
            </div>
            <?php if (!empty($studentsRecents)): ?>
                <div class="students-list">
                    <?php foreach ($studentsRecents as $student): ?>
                        <div class="student-item">
                            <div class="student-info">
                                <h4><?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?></h4>
                                <p class="student-meta">
                                    <span class="meta-label">Filière:</span>
                                    <span><?php echo htmlspecialchars($student['filiere'] ?? 'N/A'); ?></span>
                                </p>
                                <p class="student-meta">
                                    <span class="meta-label">Année:</span>
                                    <span><?php echo htmlspecialchars($student['annee'] ?? 'N/A'); ?></span>
                                </p>
                            </div>
                            <div class="student-action">
                                <a href="<?php echo BASE_URL; ?>/encadreur/etudiants/<?php echo (int)$student['id']; ?>" class="btn btn-sm btn-secondary">Détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-message">Aucun étudiant assigné</p>
            <?php endif; ?>
        </section>

        <section class="dashboard-section">
            <div class="section-header">
                <h2>Rapports récents</h2>
                <a href="<?php echo BASE_URL; ?>/encadreur/rapports" class="link-more">Voir tous →</a>
            </div>
            <?php if (!empty($rapportsRecents)): ?>
                <div class="rapports-list">
                    <?php foreach ($rapportsRecents as $rapport): ?>
                        <div class="rapport-item">
                            <div class="rapport-info">
                                <h4><?php echo htmlspecialchars($rapport['titre'] ?? 'Sans titre'); ?></h4>
                                <p class="rapport-meta">
                                    <span class="meta-label">Étudiant:</span>
                                    <span><?php echo htmlspecialchars($rapport['etudiant_nom'] ?? ''); ?></span>
                                </p>
                                <p class="rapport-meta">
                                    <span class="meta-label">Statut:</span>
                                    <span class="status-badge status-<?php echo htmlspecialchars($rapport['statut'] ?? 'en_attente'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($rapport['statut'] ?? 'En attente')); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="rapport-action">
                                <a href="<?php echo BASE_URL; ?>/encadreur/rapports/<?php echo (int)$rapport['id']; ?>" class="btn btn-sm btn-primary">Examiner</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-message">Aucun rapport à examiner</p>
            <?php endif; ?>
        </section>
    </div>
</div>
