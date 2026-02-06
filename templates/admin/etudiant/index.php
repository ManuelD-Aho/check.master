<div class="page-header">
    <div class="header-left">
        <h1>Gestion des Étudiants</h1>
        <p>Liste complète des étudiants enregistrés</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/etudiants/import" class="btn btn-secondary">Importer CSV</a>
        <a href="<?php echo BASE_URL; ?>/admin/etudiants/nouveau" class="btn btn-primary">Ajouter un étudiant</a>
    </div>
</div>

<?php if (!empty($flashes)): ?>
    <?php foreach ($flashes as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

<div class="filter-bar">
    <input type="text" id="recherche-etudiant" placeholder="Rechercher par nom, prénom ou matricule..." class="form-control">
    <select id="filtre-statut" class="form-control">
        <option value="">Tous les statuts</option>
        <option value="actif">Actif</option>
        <option value="inactif">Inactif</option>
    </select>
</div>

<div class="table-wrapper">
    <table class="data-table" id="table-etudiants">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Filière</th>
                <th>Promotion</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($etudiants)): ?>
                <?php foreach ($etudiants as $etu): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($etu['matricule_etudiant'] ?? $etu['matricule'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(($etu['nom'] ?? '') . ' ' . ($etu['prenom'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($etu['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($etu['filiere'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($etu['promotion'] ?? ''); ?></td>
                        <td>
                            <?php if (!empty($etu['actif'])): ?>
                                <span class="badge badge-success">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $mid = htmlspecialchars($etu['matricule_etudiant'] ?? $etu['matricule'] ?? ''); ?>
                            <a href="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo $mid; ?>" class="btn btn-secondary">Voir</a>
                            <a href="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo $mid; ?>/modifier" class="btn btn-primary">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Aucun étudiant trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
