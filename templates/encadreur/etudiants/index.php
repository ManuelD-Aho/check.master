<div class="page-header">
    <div class="header-left">
        <h1>Étudiants assignés</h1>
        <p class="subtitle">Liste des étudiants que vous encadrez</p>
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
        <select name="filiere">
            <option value="">Toutes les filières</option>
            <?php if (!empty($filieres)): ?>
                <?php foreach ($filieres as $filiere): ?>
                    <option value="<?php echo htmlspecialchars($filiere); ?>" <?php echo ($_GET['filiere'] ?? '') === $filiere ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($filiere); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Filière</th>
                <th>Année</th>
                <th>Progression</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($etudiants)): ?>
                <?php foreach ($etudiants as $etudiant): ?>
                    <tr class="data-row">
                        <td class="col-number">
                            <code><?php echo htmlspecialchars($etudiant['numero'] ?? ''); ?></code>
                        </td>
                        <td class="col-name">
                            <strong><?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?></strong>
                        </td>
                        <td class="col-email">
                            <?php echo htmlspecialchars($etudiant['email'] ?? ''); ?>
                        </td>
                        <td class="col-filiere">
                            <?php echo htmlspecialchars($etudiant['filiere'] ?? 'N/A'); ?>
                        </td>
                        <td class="col-year">
                            <?php echo htmlspecialchars($etudiant['annee'] ?? 'N/A'); ?>
                        </td>
                        <td class="col-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo (int)($etudiant['progression'] ?? 0); ?>%"></div>
                            </div>
                            <span class="progress-text"><?php echo (int)($etudiant['progression'] ?? 0); ?>%</span>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/encadreur/etudiants/<?php echo (int)$etudiant['id']; ?>" class="btn-icon btn-view" title="Voir les détails">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="empty-row">
                    <td colspan="7" class="empty-message">Aucun étudiant à afficher</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <?php include BASE_PATH . '/templates/components/pagination.php'; ?>
<?php endif; ?>
