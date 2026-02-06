<div class="edit-form-container">
    <div class="form-header">
        <h1>Modifier mon profil</h1>
        <p>Mettez à jour vos informations personnelles et académiques</p>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/etudiant/profil/edit" class="edit-form">
        <fieldset class="form-section">
            <legend>Informations personnelles</legend>
            <div class="form-row">
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($student['prenom'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($student['nom'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" value="<?php echo $student['date_naissance'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre">
                        <option value="">--Sélectionner--</option>
                        <option value="M" <?php echo ($student['genre'] ?? '') === 'M' ? 'selected' : ''; ?>>Masculin</option>
                        <option value="F" <?php echo ($student['genre'] ?? '') === 'F' ? 'selected' : ''; ?>>Féminin</option>
                        <option value="Autre" <?php echo ($student['genre'] ?? '') === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($student['telephone'] ?? ''); ?>">
                </div>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Adresse</legend>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="rue">Rue</label>
                    <input type="text" id="rue" name="rue" value="<?php echo htmlspecialchars($student['rue'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($student['ville'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="code_postal">Code postal</label>
                    <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($student['code_postal'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="pays">Pays</label>
                    <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($student['pays'] ?? ''); ?>">
                </div>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Contact d'urgence</legend>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="contact_urgence">Nom du contact</label>
                    <input type="text" id="contact_urgence" name="contact_urgence" value="<?php echo htmlspecialchars($student['contact_urgence'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="telephone_urgence">Téléphone du contact</label>
                    <input type="tel" id="telephone_urgence" name="telephone_urgence" value="<?php echo htmlspecialchars($student['telephone_urgence'] ?? ''); ?>">
                </div>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            <a href="<?php echo BASE_URL; ?>/etudiant/profil" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<style>
.edit-form-container { max-width: 800px; margin: 0 auto; padding: 2rem; }
.form-header { margin-bottom: 2rem; }
.form-header h1 { font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #1a1a1a; }
.form-header p { color: #666; margin: 0; font-size: 1rem; }
.edit-form { display: flex; flex-direction: column; gap: 2rem; }
.form-section { border: 1px solid #e5e5e5; border-radius: 12px; padding: 1.5rem; background: white; }
.form-section legend { font-size: 1.1rem; font-weight: 600; color: #1a1a1a; padding: 0 0.5rem; }
.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
.form-row:first-of-type { margin-top: 1rem; }
.form-group { display: flex; flex-direction: column; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group label { font-size: 0.9rem; font-weight: 600; color: #333; margin-bottom: 0.5rem; }
.form-group input, .form-group select { padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.3s ease; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.form-group input::placeholder { color: #ccc; }
.form-actions { display: flex; gap: 1rem; margin-top: 1rem; }
.btn-submit, .btn-cancel { padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem; border: none; cursor: pointer; text-decoration: none; transition: all 0.3s ease; display: inline-block; }
.btn-submit { background: #667eea; color: white; flex: 1; }
.btn-submit:hover { background: #764ba2; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
.btn-cancel { background: #f5f5f5; color: #333; flex: 1; }
.btn-cancel:hover { background: #e5e5e5; }
@media (max-width: 768px) {
    .edit-form-container { padding: 1rem; }
    .form-header h1 { font-size: 1.5rem; }
    .form-row { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .btn-submit, .btn-cancel { width: 100%; }
}
</style>
