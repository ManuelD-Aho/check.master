<div class="page-header">
    <div class="header-left">
        <h1>Détail de l'étudiant</h1>
        <p>Fiche complète de l'étudiant</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/etudiants" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<?php if ($etudiant === null): ?>
    <div class="alert alert-danger">Étudiant introuvable.</div>
<?php else: ?>

    <div class="detail-section">
        <h2>Informations personnelles</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Matricule</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['matricule_etudiant'] ?? $etudiant['matricule'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Nom</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['nom'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Prénom</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['prenom'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['email'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Téléphone</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['telephone'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Date de naissance</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['date_naissance'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Lieu de naissance</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['lieu_naissance'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Genre</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['genre'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Nationalité</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['nationalite'] ?? ''); ?></span>
            </div>
        </div>
    </div>

    <div class="detail-section">
        <h2>Informations académiques</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Filière</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['filiere'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Promotion</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['promotion'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Adresse</span>
                <span class="detail-value"><?php echo htmlspecialchars($etudiant['adresse'] ?? ''); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Statut</span>
                <span class="detail-value">
                    <?php if (!empty($etudiant['actif'])): ?>
                        <span class="badge badge-success">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inactif</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <?php $mid = htmlspecialchars($etudiant['matricule_etudiant'] ?? $etudiant['matricule'] ?? ''); ?>
        <a href="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo $mid; ?>/modifier" class="btn btn-primary">Modifier cet étudiant</a>
    </div>

<?php endif; ?>
