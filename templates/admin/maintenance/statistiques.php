<?php
$title = 'Statistiques';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Statistiques</h1>
        <p class="subtitle">Tableau de bord des statistiques du système</p>
    </div>
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
            <h3><?php echo (int) ($stats['utilisateurs'] ?? 0); ?></h3>
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
            <h3><?php echo (int) ($stats['etudiants'] ?? 0); ?></h3>
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
            <h3><?php echo (int) ($stats['candidatures'] ?? 0); ?></h3>
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
            <h3><?php echo (int) ($stats['rapports'] ?? 0); ?></h3>
            <p>Rapports</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-submissions">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="stat-content">
            <h3><?php echo (int) ($stats['soutenances'] ?? 0); ?></h3>
            <p>Soutenances</p>
        </div>
    </div>
</div>
