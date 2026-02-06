<div class="page-header">
    <div class="header-left">
        <h1>Détail du Rapport</h1>
        <p class="subtitle">Informations complètes</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/rapports" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<?php if ($rapport === null): ?>
    <div class="empty-state">
        <p>Rapport introuvable.</p>
    </div>
<?php else: ?>
    <div class="section">
        <h2>Détails du rapport</h2>
        <div class="detail-grid">
            <div class="detail-row">
                <span class="detail-label">ID</span>
                <span class="detail-value"><?php echo htmlspecialchars($rapport['id']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Titre</span>
                <span class="detail-value"><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Étudiant</span>
                <span class="detail-value"><?php echo htmlspecialchars(is_array($rapport['etudiant']) ? ($rapport['etudiant']['nom'] ?? '') : ($rapport['etudiant'] ?? '')); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date de soumission</span>
                <span class="detail-value"><?php echo htmlspecialchars($rapport['date_soumission'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Version</span>
                <span class="detail-value"><?php echo htmlspecialchars($rapport['version'] ?? ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Statut</span>
                <span class="detail-value"><span class="badge badge-<?php echo htmlspecialchars($rapport['statut_rapport'] ?? $rapport['statut'] ?? ''); ?>"><?php echo htmlspecialchars($rapport['statut_rapport'] ?? $rapport['statut'] ?? ''); ?></span></span>
            </div>
            <?php if (!empty($rapport['date_validation'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Date de validation</span>
                    <span class="detail-value"><?php echo htmlspecialchars($rapport['date_validation']); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($rapport['valide_par'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Validé par</span>
                    <span class="detail-value"><?php echo htmlspecialchars($rapport['valide_par']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="actions">
        <form action="<?php echo BASE_URL; ?>/admin/rapports/<?php echo htmlspecialchars($rapport['id']); ?>/approuver" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir approuver ce rapport ?');" style="display:inline;">
            <button type="submit" class="btn btn-success">Approuver</button>
        </form>
        <form action="<?php echo BASE_URL; ?>/admin/rapports/<?php echo htmlspecialchars($rapport['id']); ?>/retourner" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir retourner ce rapport ?');" style="display:inline;">
            <button type="submit" class="btn btn-warning">Retourner</button>
        </form>
        <form action="<?php echo BASE_URL; ?>/admin/rapports/<?php echo htmlspecialchars($rapport['id']); ?>/commission" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir envoyer ce rapport en commission ?');" style="display:inline;">
            <button type="submit" class="btn btn-info">Envoyer en commission</button>
        </form>
    </div>
<?php endif; ?>
