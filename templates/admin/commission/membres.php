<div class="page-header">
    <div class="header-left">
        <h1>Membres de la Commission</h1>
        <p class="subtitle">Gestion des membres de la commission d'examen</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/commission/membres/create" class="btn btn-primary">+ Ajouter un membre</a>
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
        <input type="text" name="search" placeholder="Rechercher par nom..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="role">
            <option value="">Tous les rôles</option>
            <option value="president" <?php echo ($_GET['role'] ?? '') === 'president' ? 'selected' : ''; ?>>Président</option>
            <option value="rapporteur" <?php echo ($_GET['role'] ?? '') === 'rapporteur' ? 'selected' : ''; ?>>Rapporteur</option>
            <option value="examinateur" <?php echo ($_GET['role'] ?? '') === 'examinateur' ? 'selected' : ''; ?>>Examinateur</option>
            <option value="encadreur" <?php echo ($_GET['role'] ?? '') === 'encadreur' ? 'selected' : ''; ?>>Encadreur</option>
        </select>
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="actif" <?php echo ($_GET['statut'] ?? '') === 'actif' ? 'selected' : ''; ?>>Actif</option>
            <option value="inactif" <?php echo ($_GET['statut'] ?? '') === 'inactif' ? 'selected' : ''; ?>>Inactif</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Spécialité</th>
                <th>Statut</th>
                <th>Date d'ajout</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($membres)): ?>
                <?php foreach ($membres as $membre): ?>
                    <tr class="data-row">
                        <td class="col-name">
                            <strong><?php echo htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']); ?></strong>
                        </td>
                        <td class="col-email">
                            <a href="mailto:<?php echo htmlspecialchars($membre['email']); ?>"><?php echo htmlspecialchars($membre['email']); ?></a>
                        </td>
                        <td class="col-role">
                            <span class="badge role-<?php echo htmlspecialchars($membre['role'] ?? 'user'); ?>">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $membre['role'] ?? 'N/A'))); ?>
                            </span>
                        </td>
                        <td class="col-specialty">
                            <?php echo htmlspecialchars($membre['specialite'] ?? 'N/A'); ?>
                        </td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($membre['statut'] ?? 'actif'); ?>">
                                <?php echo htmlspecialchars(ucfirst($membre['statut'] ?? 'Actif')); ?>
                            </span>
                        </td>
                        <td class="col-date">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($membre['date_ajout'] ?? 'now'))); ?>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/commission/membres/<?php echo (int)$membre['id']; ?>/edit" class="btn-icon btn-edit" title="Modifier">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                </a>
                                <button class="btn-icon btn-delete" onclick="deleteMembre(<?php echo (int)$membre['id']; ?>)" title="Supprimer">
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
                    <td colspan="7" class="empty-state">
                        <p>Aucun membre trouvé</p>
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
function deleteMembre(membreId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')) {
        fetch('<?php echo BASE_URL; ?>/admin/commission/membres/' + membreId + '/delete', {
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
