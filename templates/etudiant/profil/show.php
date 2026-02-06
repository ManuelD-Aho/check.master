<div class="profil-container">
    <div class="profil-header">
        <div class="profile-cover"></div>
        <div class="profile-info">
            <div class="profile-avatar">
                <div class="avatar-placeholder">
                    <?php echo strtoupper(substr($student['prenom'] ?? '', 0, 1)); ?>
                </div>
            </div>
            <div class="profile-details">
                <h1><?php echo htmlspecialchars($student['prenom'] ?? ''); ?> <?php echo htmlspecialchars($student['nom'] ?? ''); ?></h1>
                <p class="matricule">Matricule: <?php echo htmlspecialchars($student['matricule'] ?? '-'); ?></p>
                <p class="status-label">Statut: <span class="status-badge"><?php echo htmlspecialchars($student['statut'] ?? 'Actif'); ?></span></p>
            </div>
            <div class="profile-actions">
                <a href="<?php echo BASE_URL; ?>/etudiant/profil/edit" class="btn-edit">Modifier le profil</a>
            </div>
        </div>
    </div>

    <div class="profil-content">
        <div class="profil-section">
            <h2>Informations personnelles</h2>
            <div class="info-grid">
                <div class="info-item"><label>Prénom</label><p><?php echo htmlspecialchars($student['prenom'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Nom</label><p><?php echo htmlspecialchars($student['nom'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Date de naissance</label><p><?php echo $student['date_naissance'] ? date('d/m/Y', strtotime($student['date_naissance'])) : '-'; ?></p></div>
                <div class="info-item"><label>Genre</label><p><?php echo htmlspecialchars($student['genre'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Email</label><p><a href="mailto:<?php echo htmlspecialchars($student['email'] ?? ''); ?>"><?php echo htmlspecialchars($student['email'] ?? '-'); ?></a></p></div>
                <div class="info-item"><label>Téléphone</label><p><?php echo htmlspecialchars($student['telephone'] ?? '-'); ?></p></div>
            </div>
        </div>

        <div class="profil-section">
            <h2>Informations académiques</h2>
            <div class="info-grid">
                <div class="info-item"><label>Filière</label><p><?php echo htmlspecialchars($student['filiere'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Année d'étude</label><p><?php echo htmlspecialchars($student['annee_etude'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Numéro d'étudiant</label><p><?php echo htmlspecialchars($student['numero_etudiant'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Année d'inscription</label><p><?php echo htmlspecialchars($student['annee_inscription'] ?? '-'); ?></p></div>
            </div>
        </div>

        <div class="profil-section">
            <h2>Adresse</h2>
            <div class="info-grid">
                <div class="info-item full-width"><label>Rue</label><p><?php echo htmlspecialchars($student['rue'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Ville</label><p><?php echo htmlspecialchars($student['ville'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Code postal</label><p><?php echo htmlspecialchars($student['code_postal'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Pays</label><p><?php echo htmlspecialchars($student['pays'] ?? '-'); ?></p></div>
            </div>
        </div>

        <div class="profil-section">
            <h2>Informations de contact supplémentaires</h2>
            <div class="info-grid">
                <div class="info-item full-width"><label>Contact d'urgence</label><p><?php echo htmlspecialchars($student['contact_urgence'] ?? '-'); ?></p></div>
                <div class="info-item"><label>Téléphone urgence</label><p><?php echo htmlspecialchars($student['telephone_urgence'] ?? '-'); ?></p></div>
            </div>
        </div>
    </div>
</div>

<style>
.profil-container { background: white; }
.profil-header { position: relative; margin-bottom: 2rem; }
.profile-cover { height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.profile-info { padding: 0 2rem 2rem 2rem; display: flex; align-items: flex-start; gap: 2rem; position: relative; margin-top: -80px; }
.profile-avatar { position: relative; z-index: 1; }
.avatar-placeholder { width: 140px; height: 140px; border-radius: 12px; background: white; border: 4px solid #667eea; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: #667eea; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2); }
.profile-details { flex: 1; padding-top: 1rem; }
.profile-details h1 { margin: 0 0 0.5rem 0; font-size: 1.8rem; color: #1a1a1a; }
.matricule { color: #666; font-size: 0.95rem; margin: 0.25rem 0; }
.status-label { color: #666; font-size: 0.95rem; margin: 0; }
.status-badge { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 600; }
.profile-actions { display: flex; gap: 1rem; }
.btn-edit { padding: 0.75rem 1.5rem; background: #667eea; color: white; border: none; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
.btn-edit:hover { background: #764ba2; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
.profil-content { padding: 0 2rem 2rem 2rem; }
.profil-section { margin-bottom: 2.5rem; }
.profil-section h2 { font-size: 1.2rem; font-weight: 700; margin: 0 0 1.5rem 0; color: #1a1a1a; padding-bottom: 0.75rem; border-bottom: 2px solid #f0f0f0; }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
.info-item { display: flex; flex-direction: column; }
.info-item.full-width { grid-column: 1 / -1; }
.info-item label { font-size: 0.85rem; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
.info-item p { font-size: 1rem; color: #333; margin: 0; line-height: 1.6; }
.info-item a { color: #667eea; text-decoration: none; transition: color 0.3s ease; }
.info-item a:hover { color: #764ba2; text-decoration: underline; }
@media (max-width: 768px) {
    .profile-info { flex-direction: column; align-items: center; text-align: center; padding: 0 1rem 2rem 1rem; margin-top: -60px; }
    .avatar-placeholder { width: 100px; height: 100px; font-size: 2.5rem; }
    .profile-details h1 { font-size: 1.4rem; }
    .profile-actions { justify-content: center; width: 100%; }
    .profil-content { padding: 0 1rem 2rem 1rem; }
    .info-grid { grid-template-columns: 1fr; gap: 1.5rem; }
}
</style>
