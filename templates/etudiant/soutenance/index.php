<div class="soutenance-container">
    <div class="soutenance-header">
        <h1>Soutenance</h1>
        <p>Informations et suivi</p>
    </div>

    <?php if ($soutenance): ?>
        <div class="soutenance-card">
            <div class="status-banner">
                <div class="status-icon"><?php echo $soutenance['statut'] === 'planifiée' ? '📅' : ($soutenance['statut'] === 'effectuée' ? '✅' : '⏳'); ?></div>
                <div><h2><?php echo htmlspecialchars($soutenance['statut_label'] ?? ''); ?></h2></div>
            </div>

            <div class="soutenance-grid">
                <div class="section">
                    <h3>Détails</h3>
                    <div class="info-group">
                        <label>Date</label>
                        <p><?php echo $soutenance['date_soutenance'] ? date('d/m/Y à H:i', strtotime($soutenance['date_soutenance'])) : 'À définir'; ?></p>
                    </div>
                    <div class="info-group">
                        <label>Lieu</label>
                        <p><?php echo htmlspecialchars($soutenance['lieu'] ?? '-'); ?></p>
                    </div>
                    <div class="info-group">
                        <label>Durée</label>
                        <p><?php echo htmlspecialchars($soutenance['duree'] ?? '-'); ?> minutes</p>
                    </div>
                </div>

                <div class="section">
                    <h3>Jury</h3>
                    <?php if (!empty($soutenance['jury'])): ?>
                        <div class="jury-list">
                            <?php foreach ($soutenance['jury'] as $j): ?>
                                <div class="juror">
                                    <div class="avatar"><?php echo strtoupper(substr($j['prenom'] ?? '', 0, 1)); ?></div>
                                    <div><p class="name"><?php echo htmlspecialchars($j['prenom'] ?? ''); ?> <?php echo htmlspecialchars($j['nom'] ?? ''); ?></p><p class="role"><?php echo htmlspecialchars($j['role'] ?? '-'); ?></p></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>À définir</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($soutenance['statut'] === 'effectuée'): ?>
                <div class="results-section">
                    <h3>Résultats</h3>
                    <div class="results-grid">
                        <div class="result-item"><label>Note</label><p class="value"><?php echo htmlspecialchars($soutenance['note'] ?? '-'); ?>/20</p></div>
                        <div class="result-item"><label>Mention</label><p class="value <?php echo 'mention-' . strtolower($soutenance['mention'] ?? ''); ?>"><?php echo htmlspecialchars($soutenance['mention'] ?? '-'); ?></p></div>
                        <div class="result-item"><label>Résultat</label><p class="value <?php echo 'result-' . strtolower($soutenance['resultat'] ?? 'reporte'); ?>"><?php echo htmlspecialchars($soutenance['resultat'] ?? '-'); ?></p></div>
                    </div>
                    <?php if (!empty($soutenance['commentaires'])): ?>
                        <div class="commentaires"><label>Commentaires du jury</label><p><?php echo nl2br(htmlspecialchars($soutenance['commentaires'])); ?></p></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="documents-section">
                <h3>Documents</h3>
                <div class="documents-list">
                    <a href="<?php echo BASE_URL; ?>/etudiant/soutenance/convocation" class="doc-item"><span class="doc-icon">📄</span><span>Convocation</span><span>→</span></a>
                    <a href="<?php echo BASE_URL; ?>/etudiant/soutenance/rapport" class="doc-item"><span class="doc-icon">📑</span><span>Rapport final</span><span>→</span></a>
                    <?php if ($soutenance['statut'] === 'effectuée'): ?>
                        <a href="<?php echo BASE_URL; ?>/etudiant/soutenance/pv" class="doc-item"><span class="doc-icon">✅</span><span>Procès-verbal</span><span>→</span></a>
                        <a href="<?php echo BASE_URL; ?>/etudiant/soutenance/diplome" class="doc-item"><span class="doc-icon">🎓</span><span>Diplôme</span><span>→</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state"><div class="empty-icon">📋</div><h2>Soutenance non planifiée</h2><p>Contactez l'administration</p><a href="<?php echo BASE_URL; ?>/etudiant/dashboard" class="btn-secondary">Retour</a></div>
    <?php endif; ?>
</div>

<style>
.soutenance-container{padding:2rem;max-width:1100px;margin:0 auto}
.soutenance-header{margin-bottom:2rem}
.soutenance-header h1{font-size:2.5rem;font-weight:700;margin:0;color:#1a1a1a}
.soutenance-header p{color:#666;margin:0;font-size:1.1rem}
.soutenance-card{background:white;border:1px solid #e5e5e5;border-radius:12px;overflow:hidden}
.status-banner{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:2rem;display:flex;align-items:center;gap:1.5rem}
.status-icon{font-size:3rem}
.status-banner h2{margin:0 0 0.25rem 0;font-size:1.5rem}
.soutenance-grid{display:grid;grid-template-columns:1fr 1fr;gap:2rem;padding:2rem;border-bottom:1px solid #f0f0f0}
.section h3{font-size:1.1rem;font-weight:700;margin:0 0 1.5rem 0;color:#1a1a1a}
.info-group{margin-bottom:1.5rem}
.info-group label{display:block;font-size:0.85rem;font-weight:600;color:#999;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.5rem}
.info-group p{margin:0;font-size:1rem;color:#333;font-weight:500}
.jury-list{display:flex;flex-direction:column;gap:1rem}
.juror{display:flex;gap:1rem;padding:1rem;background:#f9f9f9;border-radius:8px}
.avatar{width:48px;height:48px;border-radius:50%;background:#667eea;color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem;flex-shrink:0}
.juror .name{margin:0 0 0.25rem 0;font-weight:600;color:#333}
.juror .role{margin:0;font-size:0.85rem;color:#666}
.results-section{padding:2rem;border-bottom:1px solid #f0f0f0}
.results-section h3{font-size:1.1rem;font-weight:700;margin:0 0 1.5rem 0;color:#1a1a1a}
.results-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:2rem}
.result-item{background:#f9f9f9;padding:1.5rem;border-radius:8px;text-align:center}
.result-item label{display:block;font-size:0.85rem;font-weight:600;color:#999;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem}
.result-item .value{margin:0;font-size:1.8rem;font-weight:700;color:#333}
.mention-excellent{color:#28a745!important}
.mention-très\ bien{color:#17a2b8!important}
.mention-bien{color:#667eea!important}
.result-reussi{color:#28a745!important}
.result-reporte{color:#ffc107!important}
.commentaires{background:#f0f8ff;padding:1.5rem;border-radius:8px;border-left:4px solid #667eea}
.commentaires label{display:block;font-size:0.85rem;font-weight:600;color:#999;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.75rem}
.commentaires p{margin:0;color:#333;line-height:1.6}
.documents-section{padding:2rem}
.documents-section h3{font-size:1.1rem;font-weight:700;margin:0 0 1.5rem 0;color:#1a1a1a}
.documents-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem}
.doc-item{display:flex;align-items:center;gap:1rem;padding:1rem;background:#f9f9f9;border:1px solid #e5e5e5;border-radius:8px;text-decoration:none;color:#333;transition:all 0.3s ease}
.doc-item:hover{border-color:#667eea;background:white;transform:translateX(4px)}
.doc-icon{font-size:1.5rem}
.empty-state{text-align:center;padding:3rem;background:white;border:2px dashed #e5e5e5;border-radius:12px}
.empty-icon{font-size:4rem;margin-bottom:1rem}
.empty-state h2{font-size:1.5rem;color:#1a1a1a;margin:0 0 0.5rem 0}
.empty-state p{color:#666;margin:0 0 1.5rem 0}
.btn-secondary{padding:0.75rem 1.5rem;background:#667eea;color:white;text-decorat
