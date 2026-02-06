<div class="page-header">
    <div class="header-left">
        <h1>Gestion des Rapports</h1>
        <p class="subtitle">Liste de tous les rapports</p>
    </div>
    <div class="header-right"></div>
</div>

<?php if (!empty($flashes)): ?>
    <?php foreach ($flashes as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($rapports)): ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Étudiant</th>
                    <th>Date soumission</th>
                    <th>Version</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rapports as $rapport): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(is_array($rapport['etudiant']) ? ($rapport['etudiant']['nom'] ?? '') : ($rapport['etudiant'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($rapport['date_soumission'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($rapport['version'] ?? ''); ?></td>
                        <td><span class="badge badge-<?php echo htmlspecialchars($rapport['statut_rapport'] ?? $rapport['statut'] ?? ''); ?>"><?php echo htmlspecialchars($rapport['statut_rapport'] ?? $rapport['statut'] ?? ''); ?></span></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/admin/rapports/<?php echo htmlspecialchars($rapport['id']); ?>" class="btn btn-sm btn-info">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state">
        <p>Aucun rapport trouvé.</p>
    </div>
<?php endif; ?>
