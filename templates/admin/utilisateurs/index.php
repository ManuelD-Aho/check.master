<div class="page-header">
    <div class="header-left">
        <h1>Utilisateurs</h1>
        <p class="subtitle">Gestion des utilisateurs du système</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/users/create" class="btn btn-primary">+ Ajouter un utilisateur</a>
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
        <select name="role">
            <option value="">Tous les rôles</option>
            <option value="admin" <?php echo ($_GET['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="encadreur" <?php echo ($_GET['role'] ?? '') === 'encadreur' ? 'selected' : ''; ?>>Encadreur</option>
            <option value="jury" <?php echo ($_GET['role'] ?? '') === 'jury' ? 'selected' : ''; ?>>Jury</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Status</th>
                <th>Date d'inscription</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr class="data-row">
                        <td class="col-name">
                            <strong><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></strong>
                        </td>
                        <td class="col-email">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>
                        <td class="col-role">
                            <span class="badge role-<?php echo htmlspecialchars($user['role']); ?>">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo $user['actif'] ? 'active' : 'inactive'; ?>">
                                <?php echo $user['actif'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td class="col-date">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($user['created_at'] ?? 'now'))); ?>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/users/<?php echo (int)$user['id']; ?>/edit" class="btn-icon btn-edit" title="Modifier">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                </a>
                                <button class="btn-icon btn-delete" onclick="deleteUser(<?php echo (int)$user['id']; ?>)" title="Supprimer">
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
                        <p>Aucun utilisateur trouvé</p>
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
function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        fetch('<?php echo BASE_URL; ?>/admin/users/' + userId + '/delete', {
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
