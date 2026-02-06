<div class="dashboard-header">
    <h1>Tableau de bord</h1>
    <p class="subtitle">Bienvenue dans l'espace d'administration</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-users">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?php echo htmlspecialchars($stats['users_total'] ?? 0); ?></h3>
            <p>Utilisateurs</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-students">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 10v6m0 0v6m0-6H2m0 0v6m0-6V4m0 6l10-5 10 5"></path>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?php echo htmlspecialchars($stats['students_total'] ?? 0); ?></h3>
            <p>Étudiants</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-submissions">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                <polyline points="13 2 13 9 20 9"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?php echo htmlspecialchars($stats['submissions_total'] ?? 0); ?></h3>
            <p>Candidatures</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-reports">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?php echo htmlspecialchars($stats['reports_total'] ?? 0); ?></h3>
            <p>Rapports</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Utilisateurs récents</h2>
            <a href="<?php echo BASE_URL; ?>/admin/users" class="btn-link">Voir tous</a>
        </div>
        <div class="table-container">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_users)): ?>
                        <?php foreach ($recent_users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge role-<?php echo htmlspecialchars($user['role']); ?>">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($user['created_at'] ?? 'now'))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty-state">Aucun utilisateur</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2>Activité récente</h2>
        </div>
        <div class="activity-log">
            <?php if (!empty($recent_activity)): ?>
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-marker"></div>
                        <div class="activity-content">
                            <p><?php echo htmlspecialchars($activity['description']); ?></p>
                            <small><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($activity['created_at'] ?? 'now'))); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-empty">
                    <p>Aucune activité récente</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
