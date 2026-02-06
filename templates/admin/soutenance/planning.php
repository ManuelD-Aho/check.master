<?php
$title = 'Planning des soutenances';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Planning des soutenances</h1>
        <p class="subtitle">Vue d'ensemble du calendrier</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Salle</th>
                <th>Étudiant</th>
                <th>Sujet</th>
                <th>Jury</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($soutenances) && is_array($soutenances) && !empty($soutenances)): ?>
                <?php foreach ($soutenances as $soutenance): ?>
                    <tr class="data-row">
                        <td><?php echo htmlspecialchars($soutenance['date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['heure'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['salle'] ?? 'N/A'); ?></td>
                        <td><strong><?php echo htmlspecialchars($soutenance['etudiant'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($soutenance['sujet'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($soutenance['jury'] ?? 'N/A'); ?></td>
                        <td class="col-status">
                            <span class="status-badge status-<?php echo htmlspecialchars($soutenance['statut'] ?? 'programmee'); ?>">
                                <?php echo htmlspecialchars(ucfirst($soutenance['statut'] ?? 'Programmée')); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <p>Aucune soutenance programmée</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
