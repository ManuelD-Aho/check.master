<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue liste des entreprises - Administration Académique
 * 
 * @var array $data Entreprises paginées
 * @var array $secteurs Liste des secteurs
 */

$pageTitle = 'Gestion des Entreprises';
$pageDescription = 'Liste et gestion des entreprises partenaires';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> - CheckMaster</title>
    <?= CSRF::meta() ?>
    <style>
        :root {
            --primary: #1a365d;
            --primary-light: #2b4c7e;
            --accent: #38b2ac;
            --success: #48bb78;
            --warning: #ed8936;
            --danger: #f56565;
            --text: #2d3748;
            --text-light: #718096;
            --bg: #f7fafc;
            --white: #ffffff;
            --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text); }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .page-header h1 { color: var(--primary); font-size: 1.75rem; }
        .page-header-actions { display: flex; gap: 0.5rem; }
        
        .filters-bar { background: var(--white); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .filters-bar input, .filters-bar select { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; }
        .filters-bar input { min-width: 250px; }
        
        .stats-bar { display: flex; gap: 2rem; margin-bottom: 1rem; padding: 1rem; background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-item { display: flex; flex-direction: column; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .stat-label { font-size: 0.875rem; color: var(--text-light); }
        
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        
        .enterprise-card { background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; transition: box-shadow 0.2s; }
        .enterprise-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .enterprise-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .enterprise-name { font-size: 1.125rem; font-weight: 600; color: var(--primary); }
        .enterprise-sigle { font-size: 0.875rem; color: var(--text-light); }
        .enterprise-info { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; }
        .enterprise-info-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-light); }
        .enterprise-actions { display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid var(--border); }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; text-decoration: none; font-size: 0.875rem; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-secondary { background: var(--bg); color: var(--text); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .btn:hover { opacity: 0.9; }
        
        .empty-state { text-align: center; padding: 3rem; color: var(--text-light); grid-column: 1 / -1; }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        
        .pagination-wrapper { padding: 1rem 0; display: flex; justify-content: center; grid-column: 1 / -1; }
        .pagination { display: flex; gap: 0.25rem; }
        .pagination-link { padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 0.25rem; text-decoration: none; color: var(--text); }
        .pagination-link:hover, .pagination-link.active { background: var(--primary); color: var(--white); border-color: var(--primary); }
        
        .modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: var(--white); border-radius: 0.5rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); }
        .modal-header h2 { font-size: 1.25rem; margin: 0; }
        .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light); }
        .modal-body { padding: 1.5rem; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid var(--border); }
        
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.25rem; font-size: 0.875rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        
        @media (max-width: 768px) { 
            .form-row { grid-template-columns: 1fr; }
            .filters-bar { flex-direction: column; align-items: stretch; }
            .filters-bar input { min-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>🏢 <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <p style="color: var(--text-light); margin-top: 0.25rem;"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="page-header-actions">
                <button class="btn btn-primary" onclick="openCreateModal()">+ Nouvelle Entreprise</button>
            </div>
        </div>

        <!-- Filtres -->
        <form class="filters-bar" onsubmit="loadData(); return false;">
            <input type="text" id="searchInput" placeholder="Rechercher par nom, sigle..." onchange="loadData()">
            <select id="filterSecteur" onchange="loadData()">
                <option value="">Tous les secteurs</option>
                <option value="Technologie">Technologie</option>
                <option value="Finance">Finance</option>
                <option value="Industrie">Industrie</option>
                <option value="Services">Services</option>
                <option value="Commerce">Commerce</option>
                <option value="Autre">Autre</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrer</button>
        </form>

        <!-- Statistiques -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-value" id="statTotal">-</span>
                <span class="stat-label">Total entreprises</span>
            </div>
            <div class="stat-item">
                <span class="stat-value" id="statActives">-</span>
                <span class="stat-label">Actives</span>
            </div>
        </div>

        <!-- Liste des entreprises -->
        <div class="cards-grid" id="entreprisesGrid">
            <div class="empty-state">
                <div class="icon">⏳</div>
                <p>Chargement...</p>
            </div>
        </div>

        <div class="pagination-wrapper" id="pagination"></div>
    </div>

    <!-- Modal Création/Édition -->
    <div id="entrepriseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nouvelle Entreprise</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="entrepriseForm" onsubmit="submitEntreprise(event)">
                <?= CSRF::field() ?>
                <input type="hidden" id="entrepriseId" name="id">
                
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom_entreprise">Nom *</label>
                            <input type="text" id="nom_entreprise" name="nom_entreprise" required>
                        </div>
                        <div class="form-group">
                            <label for="sigle">Sigle</label>
                            <input type="text" id="sigle" name="sigle">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="secteur_activite">Secteur d'activité</label>
                            <select id="secteur_activite" name="secteur_activite">
                                <option value="">-- Sélectionner --</option>
                                <option value="Technologie">Technologie</option>
                                <option value="Finance">Finance</option>
                                <option value="Industrie">Industrie</option>
                                <option value="Services">Services</option>
                                <option value="Commerce">Commerce</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea id="adresse" name="adresse" placeholder="Adresse complète"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom_contact">Nom du contact</label>
                            <input type="text" id="nom_contact" name="nom_contact">
                        </div>
                        <div class="form-group">
                            <label for="email_contact">Email du contact</label>
                            <input type="email" id="email_contact" name="email_contact">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', loadData);

        async function loadData() {
            const search = document.getElementById('searchInput').value;
            const secteur = document.getElementById('filterSecteur').value;
            
            let url = `/api/academique/entreprises?page=${currentPage}`;
            if (search) url += '&q=' + encodeURIComponent(search);
            if (secteur) url += '&secteur=' + encodeURIComponent(secteur);
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    renderEntreprises(result.data.entreprises);
                    renderPagination(result.data.pagination);
                    document.getElementById('statTotal').textContent = result.data.pagination.totalItems;
                    document.getElementById('statActives').textContent = result.data.pagination.totalItems;
                }
            } catch (error) {
                showError('Erreur de connexion');
            }
        }

        function renderEntreprises(entreprises) {
            const container = document.getElementById('entreprisesGrid');
            
            if (!entreprises || entreprises.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="icon">🏢</div>
                        <p>Aucune entreprise trouvée</p>
                        <button class="btn btn-primary" onclick="openCreateModal()">Ajouter une entreprise</button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = entreprises.map(e => `
                <div class="enterprise-card">
                    <div class="enterprise-header">
                        <div>
                            <div class="enterprise-name">${escapeHtml(e.nom_entreprise)}</div>
                            ${e.sigle ? `<div class="enterprise-sigle">(${escapeHtml(e.sigle)})</div>` : ''}
                        </div>
                        ${e.secteur_activite ? `<span class="badge badge-info">${escapeHtml(e.secteur_activite)}</span>` : ''}
                    </div>
                    <div class="enterprise-info">
                        ${e.email ? `<div class="enterprise-info-item">📧 ${escapeHtml(e.email)}</div>` : ''}
                        ${e.telephone ? `<div class="enterprise-info-item">📞 ${escapeHtml(e.telephone)}</div>` : ''}
                        ${e.adresse ? `<div class="enterprise-info-item">📍 ${escapeHtml(truncate(e.adresse, 50))}</div>` : ''}
                        ${e.nom_contact ? `<div class="enterprise-info-item">👤 ${escapeHtml(e.nom_contact)}</div>` : ''}
                    </div>
                    <div class="enterprise-actions">
                        <button class="btn btn-sm btn-outline" onclick="viewEntreprise(${e.id_entreprise})">Voir</button>
                        <button class="btn btn-sm btn-outline" onclick="editEntreprise(${e.id_entreprise})">Modifier</button>
                    </div>
                </div>
            `).join('');
        }

        function renderPagination(pagination) {
            const container = document.getElementById('pagination');
            if (!pagination || pagination.total <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<div class="pagination">';
            for (let i = 1; i <= Math.min(pagination.total, 10); i++) {
                html += `<a href="#" class="pagination-link ${i === pagination.current ? 'active' : ''}" onclick="goToPage(${i})">${i}</a>`;
            }
            html += '</div>';
            container.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            loadData();
        }

        // Modal
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nouvelle Entreprise';
            document.getElementById('entrepriseForm').reset();
            document.getElementById('entrepriseId').value = '';
            document.getElementById('entrepriseModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('entrepriseModal').classList.remove('active');
        }

        async function viewEntreprise(id) {
            window.location.href = '/academique/entreprises/' + id;
        }

        async function editEntreprise(id) {
            try {
                const response = await fetch('/api/academique/entreprises/' + id);
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    document.getElementById('modalTitle').textContent = 'Modifier l\'entreprise';
                    document.getElementById('entrepriseId').value = data.id_entreprise;
                    document.getElementById('nom_entreprise').value = data.nom_entreprise || '';
                    document.getElementById('sigle').value = data.sigle || '';
                    document.getElementById('secteur_activite').value = data.secteur_activite || '';
                    document.getElementById('telephone').value = data.telephone || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('adresse').value = data.adresse || '';
                    document.getElementById('nom_contact').value = data.nom_contact || '';
                    document.getElementById('email_contact').value = data.email_contact || '';
                    document.getElementById('entrepriseModal').classList.add('active');
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function submitEntreprise(event) {
            event.preventDefault();
            
            const form = document.getElementById('entrepriseForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id;
            delete data.id;
            
            const url = id ? '/api/academique/entreprises/' + id : '/api/academique/entreprises';
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
                    loadData();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        // Helpers
        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function truncate(str, length) {
            if (!str) return '';
            return str.length > length ? str.substring(0, length) + '...' : str;
        }

        function showError(message) {
            document.getElementById('entreprisesGrid').innerHTML = `
                <div class="empty-state">
                    <div class="icon">❌</div>
                    <p>${escapeHtml(message)}</p>
                </div>
            `;
        }
    </script>
</body>
</html>
