<?php
/** @var array|null $enseignant */
$urlListe = BASE_URL . '/admin/enseignants';
$e = function (string $key) use ($enseignant): string {
    return htmlspecialchars($enseignant[$key] ?? '');
};
?>

<div class="page-header">
    <div class="header-left">
        <h1>Fiche Enseignant</h1>
        <p class="subtitle">Informations détaillées</p>
    </div>
    <div class="header-right">
        <a href="<?php echo $urlListe; ?>" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<?php if ($enseignant === null): ?>
    <div class="alert alert-danger">Enseignant introuvable.</div>
<?php else: ?>

    <?php
    $blocIdentite = [
        'Matricule'  => 'matricule',
        'Nom'        => 'nom',
        'Prénom'     => 'prenom',
        'Email'      => 'email',
        'Téléphone'  => 'telephone',
    ];
    $blocAcademique = [
        'Grade'       => 'grade',
        'Spécialité'  => 'specialite',
        'Département' => 'departement',
        'Bureau'      => 'bureau',
    ];
    ?>

    <div class="detail-section">
        <h2>Identité</h2>
        <div class="detail-grid">
            <?php foreach ($blocIdentite as $libelle => $champ): ?>
                <div class="detail-item">
                    <span class="detail-label"><?php echo $libelle; ?></span>
                    <span class="detail-value"><?php echo $e($champ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="detail-section">
        <h2>Profil académique</h2>
        <div class="detail-grid">
            <?php foreach ($blocAcademique as $libelle => $champ): ?>
                <div class="detail-item">
                    <span class="detail-label"><?php echo $libelle; ?></span>
                    <span class="detail-value"><?php echo $e($champ); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="detail-item">
                <span class="detail-label">Statut</span>
                <span class="detail-value">
                    <?php if (!empty($enseignant['actif'])): ?>
                        <span class="badge badge-success">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inactif</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?php echo $urlListe . '/' . $e('matricule') . '/modifier'; ?>" class="btn btn-primary">Modifier cet enseignant</a>
    </div>

<?php endif; ?>
