<div class="page-header">
    <div class="header-left">
        <h1>Rapports et documents</h1>
        <p class="subtitle">Gestion des rapports et documents étudiants</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/rapports/create" class="btn btn-primary">+ Nouveau rapport</a>
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
        <input type="text" name="search" placeholder="Rechercher par titre ou auteur..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="type">
            <option value="">Tous les types</option>
            <option value="rapport_pfe" <?php echo ($_GET['type'] ?? '') === 'rapport_pfe' ? 'selected' : ''; ?>>Rapport PFE</option>
            <option value="rapport_stage" <?php echo ($_GET['type'] ?? '') === 'rapport_stage' ? 'selected' : ''; ?>>Rapport Stage</option>
            <option value="memoire" <?php echo ($_GET['type'] ?? '') === 'memoire' ? 'selected' : ''; ?>>Mémoire</option>
        </select>
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?php echo ($_GET['statut'] ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="accepte" <?php echo ($_GET['statut'] ?? '') === 'accepte' ? 'selected' : ''; ?>>Accepté</option>
            <option value="rejete" <?php echo ($_GET['statut'] ?? '') === 'rejete' ? 'selected' : ''; ?>>Rejeté</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Type</th>
                <th>Date de dépôt</th>
                <th>Statut</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rapports)): ?>
                <?php foreach ($rapports as $rapport): ?>
                    <tr class="data-row">
                        <td class="col-subject">
                            <strong><?php echo htmlspecialchars($rapport['titre'] ?? 'Sans titre'); ?></strong>
                        </td>
                        <td class="col-name">
                            <?php echo htmlspecialchars($rapport['nom'] . ' ' . $rapport['prenom']); ?>
                        </td>
                        <td class="col-type">
                            <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($rapport['type'] ?? 'N/A'))); ?>
                        </td>
                        <td class="col-date">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($rapport['date_depot'] ?? 'now'))); ?>
                        </td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($rapport['statut'] ?? 'en_attente'); ?>">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $rapport['statut'] ?? 'En attente'))); ?>
                            </span>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/rapports/<?php echo (int)$rapport['id']; ?>" class="btn-icon btn-view" title="Voir les détails">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="<?php echo htmlspecialchars($rapport['fichier'] ?? '#'); ?>" class="btn-icon btn-download" title="Télécharger" target="_blank">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </a>
                                <button class="btn-icon btn-delete" onclick="deleteRapport(<?php echo (int)$rapport['id']; ?>)" title="Supprimer">
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
                        <p>Aucun rapport trouvé</p>
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
function deleteRapport(rapportId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?')) {
        fetch('<?php echo BASE_URL; ?>/admin/rapports/' + rapportId + '/delete', {
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
