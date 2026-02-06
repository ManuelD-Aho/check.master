<div class="form-container">
    <h1>Nouvelle candidature</h1>
    <form method="POST" action="<?php echo BASE_URL; ?>/etudiant/candidature/submit" class="candidature-form">
        <fieldset>
            <legend>Poste</legend>
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="titre_poste" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Durée (mois) *</label>
                    <input type="number" name="duree_stage" min="1" required>
                </div>
                <div class="form-group">
                    <label>Date début *</label>
                    <input type="date" name="date_debut" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"></textarea>
            </div>
        </fieldset>

        <fieldset>
            <legend>Entreprise</legend>
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="entreprise" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Secteur *</label>
                    <input type="text" name="secteur" required>
                </div>
                <div class="form-group">
                    <label>Ville *</label>
                    <input type="text" name="ville" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone_entreprise">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_entreprise">
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Responsable stage</legend>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom_responsable">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_responsable">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone_responsable">
                </div>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Soumettre</button>
            <a href="<?php echo BASE_URL; ?>/etudiant/candidature" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<style>
.form-container{padding:2rem;max-width:800px;margin:0 auto}
.form-container h1{font-size:2rem;font-weight:700;margin:0 0 2rem 0;color:#1a1a1a}
.candidature-form{display:flex;flex-direction:column;gap:2rem}
fieldset{border:1px solid #e5e5e5;border-radius:12px;padding:1.5rem;background:white}
legend{font-size:1.1rem;font-weight:600;color:#1a1a1a;padding:0 0.5rem}
.form-group{display:flex;flex-direction:column;margin-bottom:1rem}
.form-group label{font-weight:600;color:#333;margin-bottom:0.5rem}
.form-group input,.form-group textarea{padding:0.75rem;border:1px solid #ddd;border-radius:8px;font-family:inherit;transition:all 0.3s ease}
.form-group input:focus,.form-group textarea:focus{outline:none;border-color:#667eea;box-shadow:0 0 0 3px rgba(102,126,234,0.1)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
.form-actions{display:flex;gap:1rem;margin-top:1rem}
.btn-submit,.btn-cancel{padding:0.875rem 2rem;border-radius:8px;font-weight:600;border:none;cursor:pointer;text-decoration:none;transition:all 0.3s ease;flex:1}
.btn-submit{background:#667eea;color:white}
.btn-submit:hover{background:#764ba2;transform:translateY(-2px)}
.btn-cancel{background:#f5f5f5;color:#333}
.btn-cancel:hover{background:#e5e5e5}
@media(max-width:768px){.form-container{padding:1rem}.form-row{grid-template-columns:1fr}.form-actions{flex-direction:column}}
</style>
