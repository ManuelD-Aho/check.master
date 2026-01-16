<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue liste des étudiants avec pagination - Administration Académique
 * 
 * @var Etudiant[] $etudiants Liste des étudiants
 * @var array $pagination Données de pagination
 * @var string $search Terme de recherche actuel
 * @var array $promotions Liste des promotions
 */

$layout = dirname(__DIR__) . '/layouts/admin.php';
$pageTitle = 'Gestion des Étudiants';
$pageDescription = 'Liste et gestion des étudiants inscrits';
?>

<?php ob_start(); ?>
<!-- Header de la page -->
<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-muted"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="header-actions">
            <button type="button" class="btn btn-outline" onclick="openImportModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
                Importer
            </button>
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                </svg>
                Nouvel Étudiant
            </button>
        </div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="filters-bar">
    <form method="GET" action="/academique/etudiants" class="filters-form">
        <div class="search-input">
            <input type="text" name="q" placeholder="Rechercher par nom, numéro, email..." 
                   value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="filter-select">
            <select name="promotion">
                <option value="">Toutes les promotions</option>
                <?php if (!empty($promotions)): ?>
                    <?php foreach ($promotions as $promo): ?>
                        <option value="<?= htmlspecialchars($promo, ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($promotion ?? '') === $promo ? 'selected' : '' ?>>
                            <?= htmlspecialchars($promo, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filtrer</button>
        <?php if (!empty($search) || !empty($promotion)): ?>
            <a href="/academique/etudiants" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
    </form>
</div>

<!-- Statistiques rapides -->
<div class="stats-bar">
    <div class="stat-item">
        <span class="stat-value"><?= $data['pagination']['totalItems'] ?? 0 ?></span>
        <span class="stat-label">Total étudiants</span>
    </div>
</div>

<!-- Table des étudiants -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($data['etudiants'])): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Étudiant</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Promotion</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['etudiants'] as $etudiant): ?>
                    <tr>
                        <td class="font-mono"><?= htmlspecialchars($etudiant->num_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <strong><?= htmlspecialchars(($etudiant->prenom_etu ?? '') . ' ' . ($etudiant->nom_etu ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>
                        <td><?= htmlspecialchars($etudiant->email_etu ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($etudiant->telephone_etu ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge badge-info"><?= htmlspecialchars($etudiant->promotion_etu ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td>
                            <?php if ($etudiant->actif): ?>
                                <span class="badge badge-success">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-outline" 
                                        onclick="viewEtudiant(<?= $etudiant->getId() ?>)">
                                    Voir
                                </button>
                                <button type="button" class="btn btn-sm btn-outline" 
                                        onclick="editEtudiant(<?= $etudiant->getId() ?>)">
                                    Modifier
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (!empty($data['pagination']) && $data['pagination']['total'] > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination">
                <?php if ($data['pagination']['current'] > 1): ?>
                    <a href="?page=<?= $data['pagination']['current'] - 1 ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="pagination-link">
                        &laquo; Précédent
                    </a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $data['pagination']['current'] - 2);
                $end = min($data['pagination']['total'], $data['pagination']['current'] + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="?page=<?= $i ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" 
                       class="pagination-link <?= $i === $data['pagination']['current'] ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($data['pagination']['current'] < $data['pagination']['total']): ?>
                    <a href="?page=<?= $data['pagination']['current'] + 1 ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="pagination-link">
                        Suivant &raquo;
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">👤</div>
            <h3>Aucun étudiant trouvé</h3>
            <p>
                <?php if (!empty($search)): ?>
                    Aucun résultat pour "<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                <?php else: ?>
                    Commencez par ajouter des étudiants ou importez-les depuis un fichier.
                <?php endif; ?>
            </p>
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                Ajouter un étudiant
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Création/Édition -->
<div id="etudiantModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Nouvel Étudiant</h2>
            <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="etudiantForm" onsubmit="submitEtudiant(event)">
            <?= CSRF::field() ?>
            <input type="hidden" id="etudiantId" name="id">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="num_etu">Numéro étudiant *</label>
                    <input type="text" id="num_etu" name="num_etu" required pattern="[A-Z]{2}[0-9]{8}" 
                           placeholder="Ex: AB12345678">
                    <small>Format: 2 lettres + 8 chiffres</small>
                </div>
                <div class="form-group">
                    <label for="promotion_etu">Promotion</label>
                    <input type="text" id="promotion_etu" name="promotion_etu" placeholder="Ex: 2024-2025">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom_etu">Nom *</label>
                    <input type="text" id="nom_etu" name="nom_etu" required>
                </div>
                <div class="form-group">
                    <label for="prenom_etu">Prénom *</label>
                    <input type="text" id="prenom_etu" name="prenom_etu" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email_etu">Email *</label>
                    <input type="email" id="email_etu" name="email_etu" required>
                </div>
                <div class="form-group">
                    <label for="telephone_etu">Téléphone</label>
                    <input type="tel" id="telephone_etu" name="telephone_etu" placeholder="+22501020304">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date_naiss_etu">Date de naissance</label>
                    <input type="date" id="date_naiss_etu" name="date_naiss_etu">
                </div>
                <div class="form-group">
                    <label for="lieu_naiss_etu">Lieu de naissance</label>
                    <input type="text" id="lieu_naiss_etu" name="lieu_naiss_etu">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="genre_etu">Genre</label>
                    <select id="genre_etu" name="genre_etu">
                        <option value="">-- Sélectionner --</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-header { margin-bottom: 1.5rem; }
    .page-header-content { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
    .page-header h1 { font-size: 1.75rem; color: var(--text-color, #1a365d); margin: 0; }
    .page-header .text-muted { color: #64748b; margin: 0.25rem 0 0; }
    .header-actions { display: flex; gap: 0.5rem; }
    
    .filters-bar { background: #fff; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .filters-form { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
    .search-input input { padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; width: 300px; }
    .filter-select select { padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; }
    
    .stats-bar { display: flex; gap: 2rem; margin-bottom: 1rem; padding: 1rem; background: #fff; border-radius: 0.5rem; }
    .stat-item { display: flex; flex-direction: column; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--primary, #1a365d); }
    .stat-label { font-size: 0.875rem; color: #64748b; }
    
    .card { background: #fff; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .card-body { padding: 1rem; }
    
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .table th { background: #f8fafc; font-weight: 600; color: #475569; }
    .table tr:hover { background: #f8fafc; }
    .font-mono { font-family: monospace; }
    
    .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
    .badge-info { background: #e0f2fe; color: #0369a1; }
    .badge-success { background: #dcfce7; color: #15803d; }
    .badge-danger { background: #fee2e2; color: #dc2626; }
    
    .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; }
    .btn-primary { background: var(--primary, #1a365d); color: #fff; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .btn-outline { background: transparent; border: 1px solid #e2e8f0; color: #475569; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    .btn:hover { opacity: 0.9; }
    
    .action-buttons { display: flex; gap: 0.25rem; }
    
    .pagination-wrapper { margin-top: 1rem; display: flex; justify-content: center; }
    .pagination { display: flex; gap: 0.25rem; }
    .pagination-link { padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.25rem; text-decoration: none; color: #475569; }
    .pagination-link:hover, .pagination-link.active { background: var(--primary, #1a365d); color: #fff; border-color: var(--primary, #1a365d); }
    
    .empty-state { text-align: center; padding: 3rem; }
    .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
    .empty-state h3 { margin: 0 0 0.5rem; }
    .empty-state p { color: #64748b; margin-bottom: 1rem; }
    
    .modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
    .modal-content { background: #fff; border-radius: 0.5rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; }
    .modal-header h2 { margin: 0; font-size: 1.25rem; }
    .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 0 1.5rem; margin-bottom: 1rem; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-weight: 500; margin-bottom: 0.25rem; font-size: 0.875rem; }
    .form-group input, .form-group select { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem; }
    .form-group small { color: #64748b; font-size: 0.75rem; margin-top: 0.25rem; }
    
    @media (max-width: 768px) {
        .page-header-content { flex-direction: column; align-items: stretch; }
        .header-actions { justify-content: flex-start; }
        .filters-form { flex-direction: column; }
        .search-input input { width: 100%; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Nouvel Étudiant';
        document.getElementById('etudiantForm').reset();
        document.getElementById('etudiantId').value = '';
        document.getElementById('etudiantModal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('etudiantModal').style.display = 'none';
    }
    
    async function viewEtudiant(id) {
        window.location.href = '/academique/etudiants/' + id;
    }
    
    async function editEtudiant(id) {
        try {
            const response = await fetch('/api/academique/etudiants/' + id);
            const result = await response.json();
            
            if (result.success) {
                const etudiant = result.data;
                document.getElementById('modalTitle').textContent = 'Modifier l\'étudiant';
                document.getElementById('etudiantId').value = etudiant.id_etudiant;
                document.getElementById('num_etu').value = etudiant.num_etu || '';
                document.getElementById('nom_etu').value = etudiant.nom_etu || '';
                document.getElementById('prenom_etu').value = etudiant.prenom_etu || '';
                document.getElementById('email_etu').value = etudiant.email_etu || '';
                document.getElementById('telephone_etu').value = etudiant.telephone_etu || '';
                document.getElementById('date_naiss_etu').value = etudiant.date_naiss_etu || '';
                document.getElementById('lieu_naiss_etu').value = etudiant.lieu_naiss_etu || '';
                document.getElementById('genre_etu').value = etudiant.genre_etu || '';
                document.getElementById('promotion_etu').value = etudiant.promotion_etu || '';
                document.getElementById('etudiantModal').style.display = 'flex';
            } else {
                alert('Erreur: ' + result.message);
            }
        } catch (error) {
            alert('Erreur de connexion');
        }
    }
    
    async function submitEtudiant(event) {
        event.preventDefault();
        
        const form = document.getElementById('etudiantForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const id = data.id;
        delete data.id;
        
        const url = id ? '/api/academique/etudiants/' + id : '/api/academique/etudiants';
        const method = id ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message || 'Opération réussie');
                closeModal();
                window.location.reload();
            } else {
                alert('Erreur: ' + result.message);
            }
        } catch (error) {
            alert('Erreur de connexion');
        }
    }
    
    function openImportModal() {
        window.location.href = '/admin/import?type=etudiants';
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include $layout; ?>
