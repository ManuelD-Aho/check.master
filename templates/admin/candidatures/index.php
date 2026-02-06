<div class="page-header">
    <div class="header-left">
        <h1>Candidatures</h1>
        <p class="subtitle">Gestion des candidatures étudiantes</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/candidatures/create" class="btn btn-primary">+ Nouvelle candidature</a>
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

<div class="filter-bar">
    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Rechercher par nom ou email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?php echo ($_GET['statut'] ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="acceptee" <?php echo ($_GET['statut'] ?? '') === 'acceptee' ? 'selected' : ''; ?>>Acceptée</option>
            <option value="rejetee" <?php echo ($_GET['statut'] ?? '') === 'rejetee' ? 'selected' : ''; ?>>Rejetée</option>
        </select>
        <select name="type">
            <option value="">Tous les types</option>
            <option value="pfe" <?php echo ($_GET['type'] ?? '') === 'pfe' ? 'selected' : ''; ?>>PFE</option>
            <option value="stage" <?php echo ($_GET['type'] ?? '') === 'stage' ? 'selected' : ''; ?>>Stage</option>
            <option value="mobilite" <?php echo ($_GET['type'] ?? '') === 'mobilite' ? 'selected' : ''; ?>>Mobilité</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Type</th>
                <th>Sujet</th>
                <th>Date</th>
                <th>Statut</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($candidatures)): ?>
                <?php foreach ($candidatures as $candidature): ?>
                    <tr class="data-row">
                        <td class="col-name">
                            <strong><?php echo htmlspecialchars($candidature['nom'] . ' ' . $candidature['prenom']); ?></strong><br>
                            <small><?php echo htmlspecialchars($candidature['email'] ?? ''); ?></small>
                        </td>
                        <td class="col-type">
                            <?php echo htmlspecialchars(ucfirst($candidature['type'] ?? 'N/A')); ?>
                        </td>
                        <td class="col-subject">
                            <?php echo htmlspecialchars($candidature['sujet'] ?? 'N/A'); ?>
                        </td>
                        <td class="col-date">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($candidature['date_candidature'] ?? 'now'))); ?>
                        </td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($candidature['statut'] ?? 'en_attente'); ?>">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $candidature['statut'] ?? 'En attente'))); ?>
                            </span>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo (int)$candidature['id']; ?>" class="btn-icon btn-view" title="Voir les détails">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/candidatures/<?php echo (int)$candidature['id']; ?>/edit" class="btn-icon btn-edit" title="Modifier">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                </a>
                                <button class="btn-icon btn-delete" onclick="deleteCandidature(<?php echo (int)$candidature['id']; ?>)" title="Supprimer">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <p>Aucune candidature trouvée</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
    <div class="pagination">
        <?php if ($pagination['current_page'] > 1): ?>
            <a href="?page=1" class="btn-pagination">Première</a>
            <a href="?page=<?php echo $pagination['current_page'] - 1; ?>" class="btn-pagination">Précédente</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="btn-pagination <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <a href="?page=<?php echo $pagination['current_page'] + 1; ?>" class="btn-pagination">Suivante</a>
            <a href="?page=<?php echo $pagination['total_pages']; ?>" class="btn-pagination">Dernière</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
function deleteCandidature(candidatureId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?')) {
        fetch('<?php echo BASE_URL; ?>/admin/candidatures/' + candidatureId + '/delete', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        });
    }
}
</script>
