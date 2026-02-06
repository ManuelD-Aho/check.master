<?php
$title = 'Ajouter une salle';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Ajouter une salle</h1>
        <p class="subtitle">Enregistrer un nouveau local</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/parametres/salles" class="btn btn-secondary">← Retour</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?php echo BASE_URL; ?>/admin/parametres/salles" class="form-horizontal">
        <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

        <div class="form-section">
            <h2>Informations de la salle</h2>

            <div class="form-group">
                <label for="nom_salle">Nom de la salle <span class="required">*</span></label>
                <input type="text" id="nom_salle" name="nom_salle" required class="form-control" placeholder="Ex: Salle B204">
            </div>

            <div class="form-group">
                <label for="batiment">Bâtiment</label>
                <input type="text" id="batiment" name="batiment" class="form-control" placeholder="Ex: Bâtiment B">
            </div>

            <div class="form-group">
                <label for="capacite">Capacité (places)</label>
                <input type="number" id="capacite" name="capacite" min="1" class="form-control" placeholder="30">
            </div>

            <div class="form-group">
                <label for="disponible">Disponibilité</label>
                <select id="disponible" name="disponible" class="form-control">
                    <option value="1">Disponible</option>
                    <option value="0">Indisponible</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description_salle">Description</label>
                <textarea id="description_salle" name="description_salle" rows="3" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer la salle</button>
            <a href="<?php echo BASE_URL; ?>/admin/parametres/salles" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
