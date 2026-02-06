<div class="page-header">
    <div class="header-left">
        <h1>Détail de la Candidature</h1>
        <p class="subtitle">Informations complètes</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/candidatures" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<?php if ($candidature === null): ?>
    <div class="empty-state">
        <p>Candidature introuvable.</p>
    </div>
<?php else: ?>
    <div class="section">
        <h2>Informations</h2>
        <div class="detail-grid">
            <div class="detail-row">
                <span class="detail-label">ID</span>
                <span class="detail-value"><?php echo htmlspecialchars($candidature['id']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Étudiant</span>
                <span class="detail-value"><?php echo htmlspecialchars(is_array($candidature['etudiant']) ? ($candidature['etudiant']['nom'] ?? '') : ($candidature['etudiant'] ?? '')); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Sujet</span>
                <span class="detail-value"><?php echo htmlspecialchars($candidature['sujet'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Entreprise</span>
                <span class="detail-value"><?php echo htmlspecialchars($candidature['entreprise'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date de soumission</span>
                <span class="detail-value"><?php echo htmlspecialchars($candidature['date_soumission'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Statut</span>
                <span class="detail-value"><span class="badge badge-<?php echo htmlspecialchars($candidature['statut_candidature'] ?? $candidature['statut'] ?? ''); ?>"><?php echo htmlspecialchars($candidature['statut_candidature'] ?? $candidature['statut'] ?? ''); ?></span></span>
            </div>
            <?php if (!empty($candidature['date_traitement'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Date de traitement</span>
                    <span class="detail-value"><?php echo htmlspecialchars($candidature['date_traitement']); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($candidature['validateur'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Validateur</span>
                    <span class="detail-value"><?php echo htmlspecialchars($candidature['validateur']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="actions">
        <form action="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo htmlspecialchars($candidature['id']); ?>/valider" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir valider cette candidature ?');" style="display:inline;">
            <button type="submit" class="btn btn-success">Valider</button>
        </form>
        <form action="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo htmlspecialchars($candidature['id']); ?>/rejeter" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cette candidature ?');" style="display:inline;">
            <button type="submit" class="btn btn-danger">Rejeter</button>
        </form>
    </div>
<?php endif; ?>
