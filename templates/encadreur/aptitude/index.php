<div class="page-header">
    <div class="header-left">
        <h1>Validation des aptitudes</h1>
        <p class="subtitle">Aptitudes à valider pour vos étudiants</p>
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
        <input type="text" name="search" placeholder="Rechercher par étudiant..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?php echo ($_GET['statut'] ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="validee" <?php echo ($_GET['statut'] ?? '') === 'validee' ? 'selected' : ''; ?>>Validée</option>
            <option value="rejetee" <?php echo ($_GET['statut'] ?? '') === 'rejetee' ? 'selected' : ''; ?>>Rejetée</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="aptitudes-container">
    <?php if (!empty($aptitudes)): ?>
        <?php foreach ($aptitudes as $aptitude): ?>
            <div class="aptitude-card">
                <div class="aptitude-header">
                    <div class="aptitude-info">
                        <h3><?php echo htmlspecialchars($aptitude['aptitude_nom']); ?></h3>
                        <p class="aptitude-etudiant">
                            <strong>Étudiant:</strong>
                            <span><?php echo htmlspecialchars($aptitude['etudiant_nom'] . ' ' . $aptitude['etudiant_prenom']); ?></span>
                        </p>
                    </div>
                    <span class="status-badge status-<?php echo htmlspecialchars($aptitude['statut'] ?? 'en_attente'); ?>">
                        <?php echo htmlspecialchars(ucfirst($aptitude['statut'] ?? 'En attente')); ?>
                    </span>
                </div>

                <div class="aptitude-body">
                    <div class="aptitude-description">
                        <p><?php echo htmlspecialchars($aptitude['description'] ?? ''); ?></p>
                    </div>

                    <div class="aptitude-meta">
                        <div class="meta-item">
                            <span class="meta-label">Niveau requis:</span>
                            <span class="meta-value"><?php echo htmlspecialchars($aptitude['niveau_requis'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Date de création:</span>
                            <span class="meta-value"><?php echo date('d/m/Y', strtotime($aptitude['date_creation'] ?? '')); ?></span>
                        </div>
                    </div>
                </div>

                <div class="aptitude-footer">
                    <a href="<?php echo BASE_URL; ?>/encadreur/aptitude/<?php echo (int)$aptitude['id']; ?>/form" class="btn btn-primary">Valider l'aptitude</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p class="empty-message">Aucune aptitude à valider</p>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <?php include BASE_PATH . '/templates/components/pagination.php'; ?>
<?php endif; ?>
