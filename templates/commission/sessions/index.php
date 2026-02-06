<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions - Commission</title>
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
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .sessions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .session-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s;
        }
        .session-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        .session-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .session-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .session-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
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
        .session-body {
            padding: 20px;
        }
        .session-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        .rapports-summary {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .summary-title {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .summary-counts {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .count-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        .count-badge {
            background: white;
            border: 1px solid #ddd;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: 600;
        }
        .session-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: 600;
            text-align: center;
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
            padding: 60px 20px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sessions de Commission</h1>
        </div>

        <?php if (!empty($sessions)): ?>
            <div class="sessions-grid">
                <?php foreach ($sessions as $session): ?>
                    <div class="session-card">
                        <div class="session-header">
                            <div class="session-title"><?php echo htmlspecialchars($session['titre'] ?? ''); ?></div>
                            <span class="session-status status-<?php echo htmlspecialchars($session['status'] ?? 'upcoming'); ?>">
                                <?php 
                                    $statusLabels = ['active' => 'Active', 'upcoming' => 'À venir', 'closed' => 'Fermée'];
                                    echo isset($session['status']) && isset($statusLabels[$session['status']]) ? $statusLabels[$session['status']] : 'À venir';
                                ?>
                            </span>
                        </div>

                        <div class="session-body">
                            <div class="session-info">
                                <div class="info-item">
                                    <span class="info-label">Date de Début</span>
                                    <span class="info-value"><?php echo htmlspecialchars($session['date_debut'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Date de Fin</span>
                                    <span class="info-value"><?php echo htmlspecialchars($session['date_fin'] ?? ''); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Responsable</span>
                                    <span class="info-value"><?php echo htmlspecialchars($session['responsable'] ?? ''); ?></span>
                                </div>
                            </div>

                            <div class="rapports-summary">
                                <div class="summary-title">Rapports</div>
                                <div class="summary-counts">
                                    <div class="count-item">
                                        <span>À évaluer:</span>
                                        <span class="count-badge"><?php echo isset($session['rapports_pending']) ? (int)$session['rapports_pending'] : 0; ?></span>
                                    </div>
                                    <div class="count-item">
                                        <span>Approuvés:</span>
                                        <span class="count-badge"><?php echo isset($session['rapports_approved']) ? (int)$session['rapports_approved'] : 0; ?></span>
                                    </div>
                                    <div class="count-item">
                                        <span>Total:</span>
                                        <span class="count-badge"><?php echo isset($session['rapports_total']) ? (int)$session['rapports_total'] : 0; ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="session-actions">
                                <a href="/commission/sessions/<?php echo (int)($session['id'] ?? 0); ?>" class="btn btn-primary">Détails</a>
                                <?php if (isset($session['status']) && $session['status'] === 'active'): ?>
                                    <a href="/commission/sessions/<?php echo (int)($session['id'] ?? 0); ?>/edit" class="btn btn-secondary">Éditer</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucune session de commission trouvée</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
