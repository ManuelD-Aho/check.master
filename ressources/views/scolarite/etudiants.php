<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue liste des étudiants avec pagination
 * 
 * @var Etudiant[] $etudiants Liste des étudiants
 * @var array $pagination Données de pagination
 * @var string $searchTerm Terme de recherche actuel
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des étudiants - CheckMaster</title>
    <?= CSRF::meta() ?>
    <style>
        :root {
            --primary: #1a365d;
            --primary-light: #2b4c7e;
            --accent: #38b2ac;
            --text: #2d3748;
            --text-light: #718096;
            --bg: #f7fafc;
            --white: #ffffff;
            --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text); }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { color: var(--primary); font-size: 1.75rem; }
        .search-box { display: flex; gap: 0.5rem; }
        .search-box input { padding: 0.75rem 1rem; border: 2px solid var(--border); border-radius: 0.5rem; width: 300px; }
        .search-box button { padding: 0.75rem 1.5rem; background: var(--accent); color: white; border: none; border-radius: 0.5rem; cursor: pointer; }
        .table-container { background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: var(--primary); color: white; font-weight: 600; }
        tr:hover { background: #f8fafc; }
        .pagination { display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem; }
        .pagination a, .pagination span { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; text-decoration: none; color: var(--text); }
        .pagination a:hover { background: var(--accent); color: white; border-color: var(--accent); }
        .pagination .active { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination .disabled { color: var(--text-light); cursor: not-allowed; }
        .stats { color: var(--text-light); font-size: 0.875rem; margin-bottom: 1rem; }
        .back-link { color: var(--accent); text-decoration: none; margin-bottom: 1rem; display: inline-block; }
        .empty { text-align: center; padding: 3rem; color: var(--text-light); }
    </style>
</head>
<body>
    <div class="container">
        <a href="/scolarite/dashboard" class="back-link">← Retour au tableau de bord</a>
        
        <div class="header">
            <h1>Liste des étudiants</h1>
            <form class="search-box" method="GET" action="/scolarite/etudiants">
                <input type="text" name="q" placeholder="Rechercher un étudiant..." value="<?= htmlspecialchars($searchTerm ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <div class="stats">
            <?php if (!empty($pagination)): ?>
                Affichage de <?= (($pagination['current'] - 1) * $pagination['perPage']) + 1 ?> à 
                <?= min($pagination['current'] * $pagination['perPage'], $pagination['totalItems']) ?> 
                sur <?= $pagination['totalItems'] ?> étudiants
            <?php endif; ?>
        </div>

        <div class="table-container">
            <?php if (!empty($etudiants)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Promotion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiants as $etudiant): ?>
                    <tr>
                        <td><?= htmlspecialchars($etudiant->num_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant->nom_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant->prenom_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant->email_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant->promotion_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="/scolarite/etudiants/<?= $etudiant->getId() ?>">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty">
                <?php if (!empty($searchTerm)): ?>
                    Aucun étudiant trouvé pour "<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>"
                <?php else: ?>
                    Aucun étudiant enregistré
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($pagination) && $pagination['total'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['hasPrev']): ?>
                <a href="?page=<?= $pagination['current'] - 1 ?><?= !empty($searchTerm) ? '&q=' . urlencode($searchTerm) : '' ?>">← Précédent</a>
            <?php else: ?>
                <span class="disabled">← Précédent</span>
            <?php endif; ?>

            <?php 
            $start = max(1, $pagination['current'] - 2);
            $end = min($pagination['total'], $pagination['current'] + 2);
            for ($i = $start; $i <= $end; $i++): 
            ?>
                <?php if ($i === $pagination['current']): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?><?= !empty($searchTerm) ? '&q=' . urlencode($searchTerm) : '' ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['hasNext']): ?>
                <a href="?page=<?= $pagination['current'] + 1 ?><?= !empty($searchTerm) ? '&q=' . urlencode($searchTerm) : '' ?>">Suivant →</a>
            <?php else: ?>
                <span class="disabled">Suivant →</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
