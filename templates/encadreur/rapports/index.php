<div class="page-header">
    <div class="header-left">
        <h1>Rapports à examiner</h1>
        <p class="subtitle">Rapports de stage de vos étudiants en attente de validation</p>
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
        <input type="text" name="search" placeholder="Rechercher par titre ou étudiant..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <select name="statut">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?php echo ($_GET['statut'] ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="en_cours_review" <?php echo ($_GET['statut'] ?? '') === 'en_cours_review' ? 'selected' : ''; ?>>En cours de révision</option>
            <option value="approuve" <?php echo ($_GET['statut'] ?? '') === 'approuve' ? 'selected' : ''; ?>>Approuvé</option>
            <option value="rejete" <?php echo ($_GET['statut'] ?? '') === 'rejete' ? 'selected' : ''; ?>>Rejeté</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
    </form>
</div>

<div class="rapports-container">
    <?php if (!empty($rapports)): ?>
        <?php foreach ($rapports as $rapport): ?>
            <div class="rapport-card">
                <div class="rapport-header">
                    <div class="rapport-title-block">
                        <h3><?php echo htmlspecialchars($rapport['titre'] ?? 'Sans titre'); ?></h3>
                        <p class="rapport-etudiant">
                            <strong>Étudiant:</strong>
                            <span><?php echo htmlspecialchars($rapport['etudiant_nom'] . ' ' . $rapport['etudiant_prenom']); ?></span>
                        </p>
                    </div>
                    <span class="status-badge status-<?php echo htmlspecialchars($rapport['statut'] ?? 'en_attente'); ?>">
                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $rapport['statut'] ?? 'En attente'))); ?>
                    </span>
                </div>

                <div class="rapport-body">
                    <div class="rapport-meta">
                        <div class="meta-item">
                            <span class="meta-label">Date de soumission:</span>
                            <span class="meta-value"><?php echo date('d/m/Y', strtotime($rapport['date_soumission'] ?? '')); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Entreprise:</span>
                            <span class="meta-value"><?php echo htmlspecialchars($rapport['entreprise'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Période:</span>
                            <span class="meta-value">
                                <?php echo date('d/m/Y', strtotime($rapport['date_debut'] ?? '')); ?> au 
                                <?php echo date('d/m/Y', strtotime($rapport['date_fin'] ?? '')); ?>
                            </span>
                        </div>
                    </div>

                    <div class="rapport-description">
                        <p><?php echo htmlspecialchars(substr($rapport['description'] ?? '', 0, 200)); ?>...</p>
                    </div>
                </div>

                <div class="rapport-footer">
                    <a href="<?php echo BASE_URL; ?>/encadreur/rapports/<?php echo (int)$rapport['id']; ?>" class="btn btn-primary">Examiner et commenter</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p class="empty-message">Aucun rapport à examiner</p>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($pagination) && !empty($pagination)): ?>
    <?php include BASE_PATH . '/templates/components/pagination.php'; ?>
<?php endif; ?>
