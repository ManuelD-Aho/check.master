<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Tableau de bord</h1>
        <p class="subtitle">Bienvenue, <?php echo htmlspecialchars($student['prenom'] ?? ''); ?></p>
    </div>

    <div class="status-cards-grid">
        <div class="status-card candidature-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3>Candidature</h3>
            <div class="card-content">
                <p class="card-status <?php echo 'status-' . strtolower($candidature['statut'] ?? 'en_attente'); ?>">
                    <?php echo htmlspecialchars($candidature['statut'] ?? 'En attente'); ?>
                </p>
                <p class="card-date">
                    <?php echo $candidature['date_creation'] ? date('d/m/Y', strtotime($candidature['date_creation'])) : '-'; ?>
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/etudiant/candidature" class="card-link">Consulter →</a>
        </div>

        <div class="status-card inscription-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                    <polyline points="13 2 13 9 20 9"></polyline>
                </svg>
            </div>
            <h3>Inscription</h3>
            <div class="card-content">
                <p class="card-status <?php echo 'status-' . strtolower($inscription['statut'] ?? 'en_attente'); ?>">
                    <?php echo htmlspecialchars($inscription['statut'] ?? 'En attente'); ?>
                </p>
                <p class="card-label">Année universitaire</p>
                <p class="card-value"><?php echo htmlspecialchars($inscription['annee'] ?? '-'); ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/etudiant/scolarite" class="card-link">Gérer →</a>
        </div>

        <div class="status-card rapport-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                </svg>
            </div>
            <h3>Rapport</h3>
            <div class="card-content">
                <p class="card-status <?php echo 'status-' . strtolower($rapport['statut'] ?? 'en_attente'); ?>">
                    <?php echo htmlspecialchars($rapport['statut'] ?? 'En attente'); ?>
                </p>
                <p class="card-progress">
                    <span class="progress-bar">
                        <span class="progress-fill" style="width: <?php echo ($rapport['progres'] ?? 0); ?>%"></span>
                    </span>
                    <span class="progress-text"><?php echo ($rapport['progres'] ?? 0); ?>%</span>
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/etudiant/rapports" class="card-link">Consulter →</a>
        </div>

        <div class="status-card soutenance-card">
            <div class="card-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                </svg>
            </div>
            <h3>Soutenance</h3>
            <div class="card-content">
                <p class="card-status <?php echo 'status-' . strtolower($soutenance['statut'] ?? 'en_attente'); ?>">
                    <?php echo htmlspecialchars($soutenance['statut'] ?? 'En attente'); ?>
                </p>
                <p class="card-label">Date prévue</p>
                <p class="card-value"><?php echo $soutenance['date_soutenance'] ? date('d/m/Y', strtotime($soutenance['date_soutenance'])) : '-'; ?></p>
            </div>
            <a href="<?php echo BASE_URL; ?>/etudiant/soutenance" class="card-link">Consulter →</a>
        </div>
    </div>

    <div class="dashboard-sections">
        <section class="quick-actions">
            <h2>Actions rapides</h2>
            <div class="actions-grid">
                <a href="<?php echo BASE_URL; ?>/etudiant/candidature/new" class="action-button">
                    <span class="action-icon">+</span>
                    <span class="action-text">Nouvelle candidature</span>
                </a>
                <a href="<?php echo BASE_URL; ?>/etudiant/rapports/new" class="action-button">
                    <span class="action-icon">✎</span>
                    <span class="action-text">Rédiger un rapport</span>
                </a>
                <a href="<?php echo BASE_URL; ?>/etudiant/scolarite" class="action-button">
                    <span class="action-icon">💳</span>
                    <span class="action-text">Payer cotisation</span>
                </a>
                <a href="<?php echo BASE_URL; ?>/etudiant/profil" class="action-button">
                    <span class="action-icon">👤</span>
                    <span class="action-text">Mon profil</span>
                </a>
            </div>
        </section>

        <section class="recent-activities">
            <h2>Activités récentes</h2>
            <?php if (!empty($activities)): ?>
                <div class="activities-list">
                    <?php foreach ($activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-time">
                                <?php echo htmlspecialchars($activity['date']); ?>
                            </div>
                            <div class="activity-content">
                                <p><?php echo htmlspecialchars($activity['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">Aucune activité récente</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<style>
.dashboard-container { padding: 2rem; max-width: 1200px; margin: 0 auto; }
.dashboard-header { margin-bottom: 2.5rem; }
.dashboard-header h1 { font-size: 2.5rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #1a1a1a; }
.subtitle { color: #666; font-size: 1.1rem; margin: 0; }
.status-cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
.status-card { background: white; border: 1px solid #e5e5e5; border-radius: 12px; padding: 1.5rem; transition: all 0.3s ease; }
.status-card:hover { border-color: #333; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transform: translateY(-2px); }
.card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 10px; margin-bottom: 1rem; color: #333; }
.status-card h3 { font-size: 1.1rem; font-weight: 600; margin: 0 0 1rem 0; color: #1a1a1a; }
.card-content { margin-bottom: 1rem; }
.card-status { font-size: 0.9rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 6px; display: inline-block; margin-bottom: 0.75rem; }
.status-en_attente { background: #fff3cd; color: #856404; }
.status-approuvée { background: #d4edda; color: #155724; }
.status-rejetée { background: #f8d7da; color: #721c24; }
.status-en_cours { background: #d1ecf1; color: #0c5460; }
.card-date, .card-label { font-size: 0.85rem; color: #999; margin: 0; }
.card-value { font-
