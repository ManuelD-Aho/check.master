<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports - Commission</title>
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
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 10px 16px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        .filter-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .filter-btn:hover {
            border-color: #007bff;
        }
        .rapports-list {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .rapport-row {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        .rapport-row:last-child {
            border-bottom: none;
        }
        .rapport-row:hover {
            background: #f9f9f9;
        }
        .rapport-content {
            flex: 1;
        }
        .rapport-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .rapport-meta {
            font-size: 13px;
            color: #666;
            display: flex;
            gap: 20px;
        }
        .rapport-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
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
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
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
        .pagination {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        .page-link {
            padding: 10px 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .page-link.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .page-link:hover {
            border-color: #007bff;
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
            <h1>Rapports à Évaluer</h1>
        </div>

        <div class="filters">
            <button class="filter-btn active" data-filter="all">Tous</button>
            <button class="filter-btn" data-filter="pending">En attente</button>
            <button class="filter-btn" data-filter="approved">Approuvés</button>
            <button class="filter-btn" data-filter="rejected">Rejetés</button>
        </div>

        <div class="rapports-list">
            <?php if (!empty($rapports)): ?>
                <?php foreach ($rapports as $rapport): ?>
                    <div class="rapport-row" data-status="<?php echo htmlspecialchars($rapport['status'] ?? 'pending'); ?>">
                        <div class="rapport-content">
                            <div class="rapport-title"><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></div>
                            <div class="rapport-meta">
                                <span><?php echo htmlspecialchars($rapport['auteur'] ?? ''); ?></span>
                                <span><?php echo htmlspecialchars($rapport['date'] ?? ''); ?></span>
                                <span><?php echo htmlspecialchars($rapport['session'] ?? ''); ?></span>
                            </div>
                        </div>
                        <div class="rapport-actions">
                            <span class="status-badge status-<?php echo htmlspecialchars($rapport['status'] ?? 'pending'); ?>">
                                <?php 
                                    $statusLabels = ['pending' => 'À évaluer', 'approved' => 'Approuvé', 'rejected' => 'Rejeté'];
                                    echo isset($rapport['status']) && isset($statusLabels[$rapport['status']]) ? $statusLabels[$rapport['status']] : 'À évaluer';
                                ?>
                            </span>
                            <a href="/commission/rapports/<?php echo (int)($rapport['id'] ?? 0); ?>" class="btn btn-primary">Voir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Aucun rapport trouvé</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/commission/rapports?page=<?php echo $i; ?>" class="page-link <?php echo ($i === ($currentPage ?? 1)) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
