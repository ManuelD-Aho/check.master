<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($rapport['titre'] ?? 'Rapport'); ?> - Commission</title>
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
            max-width: 900px;
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
        .rapport-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .rapport-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .rapport-meta {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #666;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .meta-value {
            font-weight: 600;
            color: #333;
        }
        .rapport-body {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .rapport-body h3 {
            margin: 20px 0 10px;
            font-size: 18px;
        }
        .rapport-body p {
            margin-bottom: 15px;
        }
        .rapport-body img {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
            border-radius: 4px;
        }
        .vote-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 14px;
        }
        .vote-options {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .radio-option label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }
        .textarea {
            width: 100%;
            min-height: 150px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 600;
        }
        .btn-primary {
            background: #28a745;
            color: white;
        }
        .btn-primary:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .status-bar {
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/commission/rapports">← Retour aux rapports</a>
        </div>

        <?php if (isset($rapport['status'])): ?>
            <div class="status-bar status-<?php echo htmlspecialchars($rapport['status']); ?>">
                <span>
                    <?php 
                        $statusLabels = ['pending' => 'En attente d\'évaluation', 'approved' => 'Approuvé', 'rejected' => 'Rejeté'];
                        echo isset($statusLabels[$rapport['status']]) ? $statusLabels[$rapport['status']] : 'En attente';
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="rapport-header">
            <h1 class="rapport-title"><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></h1>
            <div class="rapport-meta">
                <div class="meta-item">
                    <span class="meta-label">Auteur</span>
                    <span class="meta-value"><?php echo htmlspecialchars($rapport['auteur'] ?? ''); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Date de Soumission</span>
                    <span class="meta-value"><?php echo htmlspecialchars($rapport['date'] ?? ''); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Session</span>
                    <span class="meta-value"><?php echo htmlspecialchars($rapport['session'] ?? ''); ?></span>
                </div>
            </div>
        </div>

        <div class="rapport-body">
            <h3>Résumé</h3>
            <p><?php echo htmlspecialchars($rapport['resume'] ?? ''); ?></p>

            <h3>Contenu</h3>
            <p><?php echo nl2br(htmlspecialchars($rapport['contenu'] ?? '')); ?></p>

            <?php if (isset($rapport['observations']) && !empty($rapport['observations'])): ?>
                <h3>Observations</h3>
                <p><?php echo nl2br(htmlspecialchars($rapport['observations'])); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!isset($rapport['status']) || $rapport['status'] === 'pending'): ?>
            <form method="POST" class="vote-form">
                <h2>Évaluation</h2>

                <div class="form-group">
                    <label>Vote</label>
                    <div class="vote-options">
                        <div class="radio-option">
                            <input type="radio" id="approve" name="vote" value="approved" required>
                            <label for="approve">Approuver</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="reject" name="vote" value="rejected" required>
                            <label for="reject">Rejeter</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment">Commentaire (Optionnel)</label>
                    <textarea id="comment" name="comment" class="textarea"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Soumettre l'Évaluation</button>
                    <a href="/commission/rapports" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        <?php else: ?>
            <div class="vote-form">
                <h2>Évaluation Finalisée</h2>
                <?php if (isset($rapport['comment']) && !empty($rapport['comment'])): ?>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <p><?php echo nl2br(htmlspecialchars($rapport['comment'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
