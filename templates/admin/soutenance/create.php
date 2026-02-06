<?php
$title = 'Programmer une soutenance';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Programmer une soutenance</h1>
        <p class="subtitle">Nouveau formulaire de programmation</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/soutenance/store" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Informations de la soutenance</h2>

            <div class="form-group">
                <label for="etudiant_id">Étudiant <span class="required">*</span></label>
                <select id="etudiant_id" name="etudiant_id" required class="form-control">
                    <option value="">-- Sélectionner un étudiant --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sujet">Sujet <span class="required">*</span></label>
                <input type="text" id="sujet" name="sujet" required class="form-control">
            </div>

            <div class="form-group">
                <label for="date_soutenance">Date et heure <span class="required">*</span></label>
                <input type="datetime-local" id="date_soutenance" name="date_soutenance" required class="form-control">
            </div>

            <div class="form-group">
                <label for="salle_id">Salle <span class="required">*</span></label>
                <select id="salle_id" name="salle_id" required class="form-control">
                    <option value="">-- Sélectionner une salle --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea id="observations" name="observations" rows="4" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Programmer la soutenance</button>
            <a href="<?php echo BASE_URL; ?>/admin/soutenance" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
