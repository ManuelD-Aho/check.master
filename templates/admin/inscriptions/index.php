<div class="page-header">
    <div class="header-left">
        <h1>Inscriptions</h1>
        <p class="subtitle">Liste des inscriptions étudiantes</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/inscriptions/create" class="btn btn-primary">+ Nouvelle inscription</a>
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
        <input type="text" name="search" placeholder="Rechercher par nom ou numéro..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="inscrit" <?php echo ($_GET['statut'] ?? '') === 'inscrit' ? 'selected' : ''; ?>>Inscrit</option>
            <option value="en_attente" <?php echo ($_GET['statut'] ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="rejeté" <?php echo ($_GET['statut'] ?? '') === 'rejeté' ? 'selected' : ''; ?>>Rejeté</option>
        </select>
        <select name="annee">
            <option value="">Toutes les années</option>
            <option value="1" <?php echo ($_GET['annee'] ?? '') === '1' ? 'selected' : ''; ?>>1ère année</option>
            <option value="2" <?php echo ($_GET['annee'] ?? '') === '2' ? 'selected' : ''; ?>>2ème année</option>
            <option value="3" <?php echo ($_GET['annee'] ?? '') === '3' ? 'selected' : ''; ?>>3ème année</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Étudiant</th>
                <th>Email</th>
                <th>Année</th>
                <th>Filière</th>
                <th>Statut</th>
                <th>Date d'inscription</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($inscriptions)): ?>
                <?php foreach ($inscriptions as $inscription): ?>
                    <tr class="data-row">
                        <td class="col-number">
                            <code><?php echo htmlspecialchars($inscription['numero'] ?? ''); ?></code>
                        </td>
                        <td class="col-name">
                            <strong><?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></strong>
                        </td>
                        <td class="col-email">
                            <?php echo htmlspecialchars($inscription['email'] ?? ''); ?>
                        </td>
                        <td class="col-year">
                            <?php echo htmlspecialchars($inscription['annee'] ?? 'N/A'); ?>
                        </td>
                        <td class="col-filiere">
                            <?php echo htmlspecialchars($inscription['filiere'] ?? 'N/A'); ?>
                        </td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($inscription['statut'] ?? 'en_attente'); ?>">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $inscription['statut'] ?? 'En attente'))); ?>
                            </span>
                        </td>
                        <td class="col-date">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($inscription['date_inscription'] ?? 'now'))); ?>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/inscriptions/<?php echo (int)$inscription['id']; ?>" class="btn-icon btn-view" title="Voir les détails">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/admin/inscriptions/<?php echo (int)$inscription['id']; ?>/edit" class="btn-icon btn-edit" title="Modifier">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                </a>
                                <button class="btn-icon btn-delete" onclick="deleteInscription(<?php echo (int)$inscription['id']; ?>)" title="Supprimer">
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
                    <td colspan="8" class="empty-state">
                        <p>Aucune inscription trouvée</p>
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
function deleteInscription(inscriptionId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')) {
        fetch('<?php echo BASE_URL; ?>/admin/inscriptions/' + inscriptionId + '/delete', {
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
