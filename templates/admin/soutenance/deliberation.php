<?php
$title = 'Délibération';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Délibération</h1>
        <p class="subtitle">Vue d'ensemble des résultats de soutenance</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Sujet</th>
                <th>Date</th>
                <th>Note</th>
                <th>Mention</th>
                <th>Décision</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($soutenances) && is_array($soutenances) && !empty($soutenances)): ?>
                <?php foreach ($soutenances as $soutenance): ?>
                    <tr class="data-row">
                        <td><strong><?php echo htmlspecialchars($soutenance['etudiant'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($soutenance['sujet'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['note'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['mention'] ?? 'N/A'); ?></td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($soutenance['decision'] ?? 'en_attente'); ?>">
                                <?php echo htmlspecialchars(ucfirst($soutenance['decision'] ?? 'En attente')); ?>
                            </span>
                        </td>
                        <td class="col-actions">
                            <div class="action-buttons">
                                <a href="<?php echo BASE_URL; ?>/admin/soutenance/<?php echo (int)($soutenance['id'] ?? 0); ?>/deliberer" class="btn btn-sm btn-primary" title="Délibérer">Délibérer</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <p>Aucune soutenance à délibérer</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
