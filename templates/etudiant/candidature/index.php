<div class="candidature-container">
    <div class="candidature-header">
        <h1>Mes Candidatures</h1>
        <p>Suivi de vos candidatures</p>
        <a href="<?php echo BASE_URL; ?>/etudiant/candidature/new" class="btn-new">+ Nouvelle</a>
    </div>

    <?php if (!empty($candidatures)): ?>
        <div class="candidatures-list">
            <?php foreach ($candidatures as $cand): ?>
                <div class="candidature-item">
                    <div class="item-header">
                        <h3><?php echo htmlspecialchars($cand['titre_poste'] ?? ''); ?></h3>
                        <span class="status <?php echo 'status-' . strtolower($cand['statut'] ?? ''); ?>"><?php echo htmlspecialchars($cand['statut'] ?? ''); ?></span>
                    </div>
                    <p><strong><?php echo htmlspecialchars($cand['entreprise'] ?? ''); ?></strong> - <?php echo htmlspecialchars($cand['secteur'] ?? ''); ?></p>
                    <p>Ville: <?php echo htmlspecialchars($cand['ville'] ?? '-'); ?> | Début: <?php echo $cand['date_debut'] ? date('d/m/Y', strtotime($cand['date_debut'])) : '-'; ?></p>
                    <div class="item-actions">
                        <a href="<?php echo BASE_URL; ?>/etudiant/candidature/<?php echo $cand['id']; ?>" class="btn-view">Consulter</a>
                        <a href="<?php echo BASE_URL; ?>/etudiant/candidature/<?php echo $cand['id']; ?>/edit" class="btn-edit">Éditer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><p>Aucune candidature</p></div>
    <?php endif; ?>
</div>

<style>
.candidature-container{padding:2rem;max-width:1100px;margin:0 auto}
.candidature-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.candidature-header h1{font-size:2.5rem;font-weight:700;margin:0;color:#1a1a1a}
.btn-new{padding:0.75rem 1.5rem;background:#667eea;color:white;text-decoration:none;border-radius:8px;font-weight:600;transition:all 0.3s ease}
.btn-new:hover{background:#764ba2;transform:translateY(-2px)}
.candidatures-list{display:flex;flex-direction:column;gap:1.5rem}
.candidature-item{border:1px solid #e5e5e5;border-radius:12px;padding:1.5rem;background:white;transition:all 0.3s ease}
.candidature-item:hover{border-color:#667eea;box-shadow:0 4px 16px rgba(102,126,234,0.1)}
.item-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.candidature-item h3{margin:0;font-size:1.2rem;color:#1a1a1a}
.candidature-item p{margin:0.5rem 0;color:#666}
.status{padding:0.4rem 0.8rem;border-radius:6px;font-weight:600;font-size:0.85rem}
.status-en_attente{background:#fff3cd;color:#856404}
.status-acceptee{background:#d4edda;color:#155724}
.status-rejetee{background:#f8d7da;color:#721c24}
.item-actions{display:flex;gap:0.5rem;margin-top:1rem}
.btn-view,.btn-edit{flex:1;padding:0.6rem 1rem;border:none;border-radius:6px;text-decoration:none;font-weight:600;text-align:center;cursor:pointer;transition:all 0.3s ease}
.btn-view{background:#667eea;color:white}
.btn-view:hover{background:#764ba2}
.btn-edit{background:#f0f0f0;color:#333}
.btn-edit:hover{background:#e0e0e0}
@media(max-width:768px){.candidature-container{padding:1rem}.candidature-header{flex-direction:column}.candidature-header h1{font-size:1.8rem}.btn-new{width:100%;text-align:center}}
</style>
