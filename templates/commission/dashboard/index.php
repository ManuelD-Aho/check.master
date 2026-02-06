<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Commission</title>
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
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .stat-card .label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
        }
        .pending-rapports {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .pending-rapports h2 {
            padding: 20px;
            border-bottom: 1px solid #eee;
            font-size: 18px;
            font-weight: 600;
        }
        .rapport-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        .rapport-item:last-child {
            border-bottom: none;
        }
        .rapport-item:hover {
            background: #f9f9f9;
        }
        .rapport-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .rapport-info p {
            font-size: 13px;
            color: #666;
        }
        .rapport-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
        }
        .btn-primary {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #0056b3;
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
        <div class="header">
            <h1>Tableau de Bord</h1>
            <p>Gestion des rapports en attente</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Rapports Pendants</div>
                <div class="value"><?php echo isset($pendingCount) ? (int)$pendingCount : 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Sessions Actives</div>
                <div class="value"><?php echo isset($sessionsCount) ? (int)$sessionsCount : 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Rapports Approuvés</div>
                <div class="value"><?php echo isset($approvedCount) ? (int)$approvedCount : 0; ?></div>
            </div>
        </div>

        <div class="pending-rapports">
            <h2>Rapports en Attente d'Évaluation</h2>
            <?php if (!empty($pendingRapports)): ?>
                <?php foreach ($pendingRapports as $rapport): ?>
                    <div class="rapport-item">
                        <div class="rapport-info">
                            <h3><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></h3>
                            <p><?php echo htmlspecialchars($rapport['auteur'] ?? ''); ?> • <?php echo htmlspecialchars($rapport['date'] ?? ''); ?></p>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span class="rapport-status">À évaluer</span>
                            <a href="/commission/rapports/<?php echo (int)($rapport['id'] ?? 0); ?>" class="btn-primary">Voir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucun rapport en attente d'évaluation</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
