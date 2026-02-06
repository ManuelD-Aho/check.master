<div class="page-header">
    <div class="header-left">
        <h1>Gestion des Candidatures</h1>
        <p class="subtitle">Liste de toutes les candidatures</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/candidatures/pending" class="btn btn-warning">Candidatures en attente</a>
    </div>
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

<?php if (!empty($candidatures)): ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Sujet</th>
                    <th>Entreprise</th>
                    <th>Date soumission</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidatures as $candidature): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(is_array($candidature['etudiant']) ? ($candidature['etudiant']['nom'] ?? '') : ($candidature['etudiant'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($candidature['sujet'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($candidature['entreprise'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($candidature['date_soumission'] ?? ''); ?></td>
                        <td><span class="badge badge-<?php echo htmlspecialchars($candidature['statut_candidature'] ?? $candidature['statut'] ?? ''); ?>"><?php echo htmlspecialchars($candidature['statut_candidature'] ?? $candidature['statut'] ?? ''); ?></span></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo htmlspecialchars($candidature['id']); ?>" class="btn btn-sm btn-info">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state">
        <p>Aucune candidature trouvée.</p>
    </div>
<?php endif; ?>
