<?php
$title = 'Composer un jury';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Composer un jury</h1>
        <p class="subtitle">Affecter des membres au jury de soutenance</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenance/jurys" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/soutenance/compose-jury" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Informations du jury</h2>

            <div class="form-group">
                <label for="matricule">Matricule de l'enseignant <span class="required">*</span></label>
                <input type="text" id="matricule" name="matricule" required class="form-control"
                       value="<?php echo htmlspecialchars($matricule ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="soutenance_id">Soutenance <span class="required">*</span></label>
                <select id="soutenance_id" name="soutenance_id" required class="form-control">
                    <option value="">-- Sélectionner une soutenance --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="role">Rôle dans le jury <span class="required">*</span></label>
                <select id="role" name="role" required class="form-control">
                    <option value="">-- Sélectionner un rôle --</option>
                    <option value="president">Président</option>
                    <option value="rapporteur">Rapporteur</option>
                    <option value="examinateur">Examinateur</option>
                    <option value="encadreur">Encadreur</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Affecter au jury</button>
            <a href="<?php echo BASE_URL; ?>/admin/soutenance/jurys" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
