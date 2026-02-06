<?php
$title = 'Journal d\'audit';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Journal d'audit</h1>
        <p class="subtitle">Visualisation des logs d'activité du système</p>
    </div>
</div>

<div class="table-container">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>Statut</th>
                <th>Table</th>
                <th>Détails</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log->getDateCreation()->format('d/m/Y H:i:s')); ?></td>
                        <td>
                            <?php
                            $utilisateur = $log->getUtilisateur();
                            echo $utilisateur !== null ? htmlspecialchars($utilisateur->getNom() . ' ' . $utilisateur->getPrenom()) : '-';
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($log->getAction()); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $log->getStatutAction()->value === 'succes' ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($log->getStatutAction()->value); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($log->getTableConcernee() ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($log->getDetails() ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-state">Aucun log d'audit disponible</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
