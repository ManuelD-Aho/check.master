<?php
/** @var array|null $enseignant */
/** @var string $csrf */
$urlListe = BASE_URL . '/admin/enseignants';
$val = function (string $key) use ($enseignant): string {
    return htmlspecialchars($enseignant[$key] ?? '');
};
$champsEdition = [
    ['id' => 'nom',         'libelle' => 'Nom',          'type' => 'text',  'requis' => true],
    ['id' => 'prenom',      'libelle' => 'Prénom',        'type' => 'text',  'requis' => true],
    ['id' => 'email',       'libelle' => 'Adresse email', 'type' => 'email', 'requis' => true],
    ['id' => 'telephone',   'libelle' => 'Téléphone',     'type' => 'tel',   'requis' => false],
    ['id' => 'grade',       'libelle' => 'Grade',         'type' => 'text',  'requis' => false],
    ['id' => 'specialite',  'libelle' => 'Spécialité',    'type' => 'text',  'requis' => false],
    ['id' => 'departement', 'libelle' => 'Département',   'type' => 'text',  'requis' => false],
    ['id' => 'bureau',      'libelle' => 'Bureau',        'type' => 'text',  'requis' => false],
];
?>

<div class="page-header">
    <div class="header-left">
        <h1>Modifier l'enseignant</h1>
        <p class="subtitle">Mettre à jour les informations de l'enseignant</p>
    </div>
    <div class="header-right">
        <a href="<?php echo $urlListe; ?>" class="btn btn-secondary">Retour à la liste</a>
    </div>
</div>

<?php if ($enseignant === null): ?>
    <div class="alert alert-danger">Enseignant introuvable.</div>
<?php else: ?>

<div class="form-container">
    <form method="post"
          action="<?php echo $urlListe . '/' . $val('matricule') . '/modifier'; ?>"
          class="form-enseignant">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-grid">
            <?php foreach ($champsEdition as $champ): ?>
                <div class="form-group">
                    <label for="<?php echo $champ['id']; ?>">
                        <?php echo $champ['libelle']; ?>
                        <?php if ($champ['requis']): ?><span class="required">*</span><?php endif; ?>
                    </label>
                    <input type="<?php echo $champ['type']; ?>"
                           id="<?php echo $champ['id']; ?>"
                           name="<?php echo $champ['id']; ?>"
                           value="<?php echo $val($champ['id']); ?>"
                           <?php echo $champ['requis'] ? 'required' : ''; ?>>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="<?php echo $urlListe; ?>" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php endif; ?>
