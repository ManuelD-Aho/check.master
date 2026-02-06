<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($session['titre'] ?? 'Session'); ?> - Commission</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 13px;
        }
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .session-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .session-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .session-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-upcoming {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-closed {
            background: #e2e3e5;
            color: #383d41;
        }
        .session-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #007bff;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .rapports-table {
            width: 100%;
            border-collapse: collapse;
        }
        .rapports-table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }
        .rapports-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
        }
        .rapports-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .rapports-table tr:hover {
            background: #f9f9f9;
        }
        .rapport-title {
            font-weight: 600;
            color: #333;
        }
        .rapport-author {
            font-size: 13px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/commission/sessions">← Retour aux sessions</a>
        </div>

        <div class="session-header">
            <h1 class="session-title"><?php echo htmlspecialchars($session['titre'] ?? ''); ?></h1>
            <span class="session-status status-<?php echo htmlspecialchars($session['status'] ?? 'upcoming'); ?>">
                <?php 
                    $statusLabels = ['active' => 'Active', 'upcoming' => 'À venir', 'closed' => 'Fermée'];
                    echo isset($session['status']) && isset($statusLabels[$session['status']]) ? $statusLabels[$session['status']] : 'À venir';
                ?>
            </span>

            <div class="session-details">
                <div class="detail-item">
                    <span class="detail-label">Date de Début</span>
                    <span class="detail-value"><?php echo htmlspecialchars($session['date_debut'] ?? ''); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date de Fin</span>
                    <span class="detail-value"><?php echo htmlspecialchars($session['date_fin'] ?? ''); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Responsable</span>
                    <span class="detail-value"><?php echo htmlspecialchars($session['responsable'] ?? ''); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Lieu</span>
                    <span class="detail-value"><?php echo htmlspecialchars($session['lieu'] ?? ''); ?></span>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Statistiques</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number"><?php echo isset($session['rapports_total']) ? (int)$session['rapports_total'] : 0; ?></div>
                    <div class="stat-label">Total Rapports</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo isset($session['rapports_pending']) ? (int)$session['rapports_pending'] : 0; ?></div>
                    <div class="stat-label">En Attente</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo isset($session['rapports_approved']) ? (int)$session['rapports_approved'] : 0; ?></div>
                    <div class="stat-label">Approuvés</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo isset($session['rapports_rejected']) ? (int)$session['rapports_rejected'] : 0; ?></div>
                    <div class="stat-label">Rejetés</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Rapports de la Session</h2>
            
            <?php if (!empty($rapports)): ?>
                <table class="rapports-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Date de Soumission</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rapports as $rapport): ?>
                            <tr>
                                <td>
                                    <div class="rapport-title"><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></div>
                                </td>
                                <td>
                                    <div class="rapport-author"><?php echo htmlspecialchars($rapport['auteur'] ?? ''); ?></div>
                                </td>
                                <td>
                                    <span><?php echo htmlspecialchars($rapport['date'] ?? ''); ?></span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($rapport['status'] ?? 'pending'); ?>">
                                        <?php 
                                            $statusLabels = ['pending' => 'À évaluer', 'approved' => 'Approuvé', 'rejected' => 'Rejeté'];
                                            echo isset($rapport['status']) && isset($statusLabels[$rapport['status']]) ? $statusLabels[$rapport['status']] : 'À évaluer';
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/commission/rapports/<?php echo (int)($rapport['id'] ?? 0); ?>" class="btn btn-primary">Voir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucun rapport pour cette session</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
