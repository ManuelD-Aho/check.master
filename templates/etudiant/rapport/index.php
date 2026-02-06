<div class="rapports-container">
    <div class="rapports-header">
        <h1>Mes Rapports</h1>
        <p>Gestion de vos rapports</p>
        <a href="<?php echo BASE_URL; ?>/etudiant/rapports/new" class="btn-new">+ Nouveau</a>
    </div>

    <div class="stats">
        <div class="stat"><div class="stat-value"><?php echo count($rapports ?? []); ?></div><div class="stat-label">Rapports</div></div>
        <div class="stat"><div class="stat-value"><?php echo count($rapportsEnCours ?? []); ?></div><div class="stat-label">En cours</div></div>
        <div class="stat"><div class="stat-value"><?php echo count($rapportsValidés ?? []); ?></div><div class="stat-label">Validés</div></div>
    </div>

    <?php if (!empty($rapports)): ?>
        <div class="rapports-list">
            <?php foreach ($rapports as $rap): ?>
                <div class="rapport-card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($rap['titre'] ?? 'Rapport'); ?></h3>
                        <span class="status <?php echo 'status-' . strtolower($rap['statut'] ?? 'brouillon'); ?>"><?php echo htmlspecialchars($rap['statut'] ?? 'Brouillon'); ?></span>
                    </div>
                    <p class="meta"><?php echo htmlspecialchars($rap['entreprise'] ?? ''); ?> | <?php echo htmlspecialchars($rap['annee_academique'] ?? ''); ?></p>
                    <p class="description"><?php echo htmlspecialchars(substr($rap['resume'] ?? '', 0, 100)); ?>...</p>
                    <div class="progress">
                        <div class="progress-bar"><div class="progress-fill" style="width:<?php echo ($rap['pourcentage_completion'] ?? 0); ?>%"></div></div>
                        <span class="progress-text"><?php echo ($rap['pourcentage_completion'] ?? 0); ?>%</span>
                    </div>
                    <div class="card-actions">
                        <a href="<?php echo BASE_URL; ?>/etudiant/rapports/<?php echo $rap['id']; ?>" class="btn-view">Consulter</a>
                        <a href="<?php echo BASE_URL; ?>/etudiant/rapports/<?php echo $rap['id']; ?>/edit" class="btn-edit">Éditer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><p>Aucun rapport</p><a href="<?php echo BASE_URL; ?>/etudiant/rapports/new" class="btn-primary">Créer</a></div>
    <?php endif; ?>
</div>

<style>
.rapports-container{padding:2rem;max-width:1100px;margin:0 auto}
.rapports-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
.rapports-header h1{font-size:2.5rem;font-weight:700;margin:0;color:#1a1a1a}
.rapports-header p{color:#666;margin:0.5rem 0 0 0;width:100%}
.btn-new{padding:0.75rem 1.5rem;background:#667eea;color:white;text-decoration:none;border-radius:8px;font-weight:600;transition:all 0.3s ease}
.btn-new:hover{background:#764ba2;transform:translateY(-2px)}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem}
.stat{background:white;border:1px solid #e5e5e5;border-radius:12px;padding:1.5rem;text-align:center}
.stat-value{font-size:2.5rem;font-weight:700;color:#667eea;margin-bottom:0.5rem}
.stat-label{font-size:0.85rem;color:#999;text-transform:uppercase}
.rapports-list{display:flex;flex-direction:column;gap:1.5rem}
.rapport-card{border:1px solid #e5e5e5;border-radius:12px;padding:1.5rem;background:white;transition:all 0.3s ease}
.rapport-card:hover{border-color:#667eea;box-shadow:0 4px 16px rgba(102,126,234,0.1)}
.card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem}
.rapport-card h3{margin:0;font-size:1.2rem;color:#1a1a1a}
.status{padding:0.4rem 0.8rem;border-radius:6px;font-weight:600;font-size:0.85rem}
.status-brouillon{background:#e2e3e5;color:#383d41}
.status-en_revision{background:#fff3cd;color:#856404}
.status-approuvé{background:#d4edda;color:#155724}
.meta{margin:0.5rem 0;color:#666;font-size:0.9rem}
.description{margin:0.5rem 0;color:#666}
.progress{display:flex;align-items:center;gap:0.75rem;margin:1rem 0}
.progress-bar{flex:1;height:8px;background:#e5e5e5;border-radius:4px;overflow:hidden}
.progress-fill{height:100%;background:linear-gradient(90deg,#667eea 0%,#764ba2 100%)}
.progress-text{font-size:0.85rem;font-weight:600;min-width:35px}
.card-actions{display:flex;gap:0.5rem;margin-top:1rem}
.btn-view,.btn-edit{flex:1;padding:0.6rem 1rem;border:none;border-radius:6px;text-decoration:none;font-weight:600;text-align:center;cursor:pointer;transition:all 0.3s ease}
.btn-view{background:#667eea;color:white}
.btn-view:hover{background:#764ba2}
.btn-edit{background:#f0f0f0;color:#333}
.btn-edit:hover{background:#e0e0e0}
.empty-state{text-align:center;padding:3rem;background:white;border:2px dashed #e5e5e5;border-radius:12px}
.btn-primary{padding:0.75rem 1.5rem;background:#667eea;color:white;text-decoration:none;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.3s ease;display:inline-block;margin-top:1rem}
.btn-primary:hover{background:#764ba2;transform:translateY(-2px)}
@media(max-width:768px){.rapports-container{padding:1rem}.rapports-header{flex-direction:column}.btn-new{width:100%;text-align:center}.stats{grid-template-columns:1fr}}
</style>
