<?php
/** @var array $enseignants */
/** @var array $flashes */
$urlBase = BASE_URL . '/admin/enseignants';
$colonnes = ['Matricule', 'Nom complet', 'Email', 'Grade', 'Spécialité', 'Actions'];
?>

<div class="page-header">
    <div class="header-left">
        <h1>Gestion des Enseignants</h1>
        <p class="subtitle">Consulter et gérer la liste du corps enseignant</p>
    </div>
    <div class="header-right">
        <a href="<?php echo $urlBase; ?>/nouveau" class="btn btn-primary">+ Ajouter un enseignant</a>
    </div>
</div>

<?php if (!empty($flashes)): ?>
    <div class="alerts">
        <?php foreach ($flashes as $type => $messages): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                    <span><?php echo htmlspecialchars($msg); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <?php foreach ($colonnes as $idx => $col): ?>
                    <th<?php echo $idx === count($colonnes) - 1 ? ' class="col-actions"' : ''; ?>><?php echo $col; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($enseignants)): ?>
            <?php foreach ($enseignants as $row): ?>
                <?php
                    $mat = htmlspecialchars($row['matricule'] ?? '');
                    $nomComplet = htmlspecialchars(trim(($row['nom'] ?? '') . ' ' . ($row['prenom'] ?? '')));
                ?>
                <tr class="data-row">
                    <td class="col-number"><code><?php echo $mat; ?></code></td>
                    <td class="col-name"><strong><?php echo $nomComplet; ?></strong></td>
                    <td class="col-email"><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['grade'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['specialite'] ?? ''); ?></td>
                    <td class="col-actions">
                        <div class="action-buttons">
                            <a href="<?php echo $urlBase . '/' . $mat; ?>"
                               class="btn-icon btn-view" title="Consulter la fiche">&#128065;</a>
                            <a href="<?php echo $urlBase . '/' . $mat . '/modifier'; ?>"
                               class="btn-icon btn-edit" title="Éditer">&#9998;</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?php echo count($colonnes); ?>" class="empty-state">
                    <p>Aucun enseignant enregistré pour le moment.</p>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
