<div class="page-header">
    <div class="header-left">
        <h1>Gestion des Utilisateurs</h1>
        <p class="subtitle">Ensemble des comptes utilisateurs enregistrés</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs/nouveau" class="btn btn-primary">+ Ajouter</a>
    </div>
</div>

<?php if (!empty($flashes)): ?>
    <div class="alerts">
        <?php foreach ($flashes as $categorie => $listeMessages): ?>
            <?php foreach ($listeMessages as $msg): ?>
                <div class="alert alert-<?php echo htmlspecialchars($categorie); ?>">
                    <span><?php echo htmlspecialchars($msg); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="filter-bar">
    <form method="get" class="filter-form">
        <input type="text" name="q" placeholder="Filtrer par nom ou adresse email..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        <button type="submit" class="btn btn-secondary">Rechercher</button>
    </form>
</div>

<?php if (!empty($utilisateurs)): ?>
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Groupe</th>
                <th>Type</th>
                <th>Statut</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $compte): ?>
                <?php $idCompte = (int)($compte['id'] ?? 0); ?>
                <tr class="data-row">
                    <td class="col-name">
                        <strong><?php echo htmlspecialchars(($compte['nom'] ?? '') . ' ' . ($compte['prenom'] ?? '')); ?></strong>
                    </td>
                    <td class="col-email"><?php echo htmlspecialchars($compte['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($compte['groupe'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($compte['type'] ?? 'N/A'); ?></td>
                    <td class="col-status">
                        <span class="status-badge status-<?php echo htmlspecialchars($compte['statut'] ?? 'inactif'); ?>">
                            <?php echo htmlspecialchars(ucfirst($compte['statut'] ?? 'Inactif')); ?>
                        </span>
                    </td>
                    <td class="col-actions">
                        <div class="action-buttons">
                            <a href="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo $idCompte; ?>" class="btn-icon btn-view" title="Consulter">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo $idCompte; ?>/modifier" class="btn-icon btn-edit" title="Éditer">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                            </a>
                            <form method="POST" action="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo $idCompte; ?>/supprimer" class="inline-form" onsubmit="return confirm('Confirmez-vous la suppression de cet utilisateur ?');">
                                <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">
                                <button type="submit" class="btn-icon btn-delete" title="Retirer">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Groupe</th>
                <th>Type</th>
                <th>Statut</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="empty-state">
                    <p>Aucun utilisateur référencé pour le moment</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>
