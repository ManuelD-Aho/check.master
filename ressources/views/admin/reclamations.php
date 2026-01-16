<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue des réclamations - Administration
 * 
 * @var array $data Réclamations paginées
 * @var array $stats Statistiques
 */

$pageTitle = 'Gestion des Réclamations';
$pageDescription = 'Suivi et traitement des réclamations étudiantes';
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
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--white); padding: 1.25rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center; }
        .stat-card h3 { font-size: 0.75rem; color: var(--text-light); margin-bottom: 0.5rem; text-transform: uppercase; }
        .stat-card .value { font-size: 1.75rem; font-weight: 700; color: var(--primary); }
        .stat-card.warning .value { color: var(--warning); }
        .stat-card.success .value { color: var(--success); }
        .stat-card.danger .value { color: var(--danger); }
        
        .filters-bar { background: var(--white); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .filters-bar select { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; min-width: 150px; }
        
        .card { background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-body { padding: 0; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--bg); font-weight: 600; color: var(--text-light); font-size: 0.875rem; }
        .table tr:hover { background: #f8fafc; }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-primary { background: #dbeafe; color: #1d4ed8; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; text-decoration: none; font-size: 0.875rem; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-danger { background: var(--danger); color: var(--white); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .btn:hover { opacity: 0.9; }
        
        .action-buttons { display: flex; gap: 0.25rem; }
        
        .pagination { display: flex; justify-content: center; padding: 1rem; gap: 0.25rem; }
        .pagination-link { padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 0.25rem; text-decoration: none; color: var(--text); }
        .pagination-link:hover, .pagination-link.active { background: var(--primary); color: var(--white); border-color: var(--primary); }
        
        .empty-state { text-align: center; padding: 3rem; color: var(--text-light); }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        
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
        .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; min-height: 100px; resize: vertical; }
        
        .detail-row { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border); }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: var(--text-light); font-size: 0.875rem; }
        .detail-value { font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>📝 <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <p style="color: var(--text-light); margin-top: 0.25rem;"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <button class="btn btn-outline" onclick="loadData()">🔄 Actualiser</button>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card danger">
                <h3>En attente</h3>
                <div class="value" id="statEnAttente">-</div>
            </div>
            <div class="stat-card warning">
                <h3>En cours</h3>
                <div class="value" id="statEnCours">-</div>
            </div>
            <div class="stat-card success">
                <h3>Résolues</h3>
                <div class="value" id="statResolues">-</div>
            </div>
            <div class="stat-card">
                <h3>Rejetées</h3>
                <div class="value" id="statRejetees">-</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-bar">
            <select id="filterStatut" onchange="loadData()">
                <option value="">Tous les statuts</option>
                <option value="En_attente">En attente</option>
                <option value="En_cours">En cours</option>
                <option value="Resolue">Résolues</option>
                <option value="Rejetee">Rejetées</option>
            </select>
            <select id="filterType" onchange="loadData()">
                <option value="">Tous les types</option>
                <option value="Note">Note</option>
                <option value="Paiement">Paiement</option>
                <option value="Inscription">Inscription</option>
                <option value="Soutenance">Soutenance</option>
                <option value="Candidature">Candidature</option>
                <option value="Autre">Autre</option>
            </select>
        </div>

        <!-- Tableau -->
        <div class="card">
            <div class="card-body">
                <table class="table" id="reclamationsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Étudiant</th>
                            <th>Type</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reclamationsBody">
                        <tr><td colspan="7" class="empty-state">Chargement...</td></tr>
                    </tbody>
                </table>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>

    <!-- Modal Détail -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Réclamation #<span id="detailId"></span></h2>
                <button class="modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer" id="detailActions">
                <!-- Actions dynamiques -->
            </div>
        </div>
    </div>

    <!-- Modal Résolution -->
    <div id="resolveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Résoudre la réclamation</h2>
                <button class="modal-close" onclick="closeResolveModal()">&times;</button>
            </div>
            <form id="resolveForm" onsubmit="resolveReclamation(event)">
                <input type="hidden" id="resolveId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="resolution">Résolution apportée *</label>
                        <textarea id="resolution" name="resolution" required placeholder="Décrivez la résolution apportée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeResolveModal()">Annuler</button>
                    <button type="submit" class="btn btn-success">Résoudre</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Rejet -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Rejeter la réclamation</h2>
                <button class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <form id="rejectForm" onsubmit="rejectReclamation(event)">
                <input type="hidden" id="rejectId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motif">Motif du rejet *</label>
                        <textarea id="motif" name="motif" required placeholder="Expliquez le motif du rejet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeRejectModal()">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;

        // Chargement des données
        async function loadData() {
            await Promise.all([loadStats(), loadReclamations()]);
        }

        async function loadStats() {
            try {
                const response = await fetch('/api/admin/reclamations/statistiques');
                const result = await response.json();
                
                if (result.success) {
                    const stats = result.data.general || {};
                    document.getElementById('statEnAttente').textContent = stats.en_attente || '0';
                    document.getElementById('statEnCours').textContent = stats.en_cours || '0';
                    document.getElementById('statResolues').textContent = stats.resolues || '0';
                    document.getElementById('statRejetees').textContent = stats.rejetees || '0';
                }
            } catch (error) {
                console.error('Erreur chargement stats:', error);
            }
        }

        async function loadReclamations() {
            const statut = document.getElementById('filterStatut').value;
            const type = document.getElementById('filterType').value;
            
            let url = `/api/admin/reclamations?page=${currentPage}`;
            if (statut) url += '&statut=' + statut;
            if (type) url += '&type=' + type;
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    renderReclamations(result.data.reclamations);
                    renderPagination(result.data.pagination);
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Erreur de connexion');
            }
        }

        function renderReclamations(reclamations) {
            const tbody = document.getElementById('reclamationsBody');
            
            if (!reclamations || reclamations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><div class="icon">📭</div><p>Aucune réclamation</p></td></tr>';
                return;
            }
            
            tbody.innerHTML = reclamations.map(r => `
                <tr>
                    <td>#${r.id_reclamation}</td>
                    <td>
                        <strong>${escapeHtml((r.prenom_etu || '') + ' ' + (r.nom_etu || ''))}</strong><br>
                        <small style="color: var(--text-light)">${escapeHtml(r.num_etu || '')}</small>
                    </td>
                    <td><span class="badge badge-info">${escapeHtml(r.type_reclamation)}</span></td>
                    <td>${escapeHtml(truncate(r.sujet, 40))}</td>
                    <td><span class="badge ${getStatusBadgeClass(r.statut)}">${escapeHtml(r.statut)}</span></td>
                    <td>${formatDate(r.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline" onclick="viewDetail(${r.id_reclamation})">Voir</button>
                            ${r.statut === 'En_attente' ? `<button class="btn btn-sm btn-primary" onclick="prendreEnCharge(${r.id_reclamation})">Prendre</button>` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function renderPagination(pagination) {
            const container = document.getElementById('pagination');
            if (!pagination || pagination.total <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '';
            for (let i = 1; i <= pagination.total; i++) {
                html += `<a href="#" class="pagination-link ${i === pagination.current ? 'active' : ''}" onclick="goToPage(${i})">${i}</a>`;
            }
            container.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            loadReclamations();
        }

        function getStatusBadgeClass(statut) {
            switch(statut) {
                case 'En_attente': return 'badge-danger';
                case 'En_cours': return 'badge-warning';
                case 'Resolue': return 'badge-success';
                case 'Rejetee': return 'badge-primary';
                default: return 'badge-info';
            }
        }

        // Actions
        async function viewDetail(id) {
            try {
                const response = await fetch('/api/admin/reclamations/' + id);
                const result = await response.json();
                
                if (result.success) {
                    showDetailModal(result.data);
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        function showDetailModal(data) {
            document.getElementById('detailId').textContent = data.id_reclamation;
            
            const etudiant = data.etudiant || {};
            
            document.getElementById('detailContent').innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Étudiant</span>
                    <span class="detail-value">${escapeHtml((etudiant.prenom_etu || '') + ' ' + (etudiant.nom_etu || ''))}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Type</span>
                    <span class="detail-value">${escapeHtml(data.type_reclamation)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Statut</span>
                    <span class="badge ${getStatusBadgeClass(data.statut)}">${escapeHtml(data.statut)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sujet</span>
                    <span class="detail-value">${escapeHtml(data.sujet)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Description</span>
                    <span class="detail-value">${escapeHtml(data.description)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date création</span>
                    <span class="detail-value">${formatDate(data.created_at)}</span>
                </div>
                ${data.resolution ? `<div class="detail-row"><span class="detail-label">Résolution</span><span class="detail-value">${escapeHtml(data.resolution)}</span></div>` : ''}
                ${data.motif_rejet ? `<div class="detail-row"><span class="detail-label">Motif rejet</span><span class="detail-value">${escapeHtml(data.motif_rejet)}</span></div>` : ''}
            `;
            
            let actions = '<button class="btn btn-outline" onclick="closeDetailModal()">Fermer</button>';
            
            if (data.statut === 'En_attente') {
                actions += `<button class="btn btn-primary" onclick="prendreEnCharge(${data.id_reclamation}); closeDetailModal();">Prendre en charge</button>`;
            } else if (data.statut === 'En_cours') {
                actions += `<button class="btn btn-danger" onclick="openRejectModal(${data.id_reclamation}); closeDetailModal();">Rejeter</button>`;
                actions += `<button class="btn btn-success" onclick="openResolveModal(${data.id_reclamation}); closeDetailModal();">Résoudre</button>`;
            }
            
            document.getElementById('detailActions').innerHTML = actions;
            document.getElementById('detailModal').classList.add('active');
        }

        async function prendreEnCharge(id) {
            try {
                const response = await fetch('/api/admin/reclamations/' + id + '/prendre-en-charge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Réclamation prise en charge');
                    loadData();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function resolveReclamation(event) {
            event.preventDefault();
            
            const id = document.getElementById('resolveId').value;
            const resolution = document.getElementById('resolution').value;
            
            try {
                const response = await fetch('/api/admin/reclamations/' + id + '/resoudre', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ resolution })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Réclamation résolue');
                    closeResolveModal();
                    loadData();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function rejectReclamation(event) {
            event.preventDefault();
            
            const id = document.getElementById('rejectId').value;
            const motif = document.getElementById('motif').value;
            
            try {
                const response = await fetch('/api/admin/reclamations/' + id + '/rejeter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ motif })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Réclamation rejetée');
                    closeRejectModal();
                    loadData();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        // Modals
        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }
        
        function openResolveModal(id) {
            document.getElementById('resolveId').value = id;
            document.getElementById('resolution').value = '';
            document.getElementById('resolveModal').classList.add('active');
        }
        
        function closeResolveModal() {
            document.getElementById('resolveModal').classList.remove('active');
        }
        
        function openRejectModal(id) {
            document.getElementById('rejectId').value = id;
            document.getElementById('motif').value = '';
            document.getElementById('rejectModal').classList.add('active');
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }

        // Helpers
        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('fr-FR');
        }

        function truncate(str, length) {
            if (!str) return '';
            return str.length > length ? str.substring(0, length) + '...' : str;
        }

        function showError(message) {
            document.getElementById('reclamationsBody').innerHTML = `<tr><td colspan="7" class="empty-state"><div class="icon">❌</div><p>${escapeHtml(message)}</p></td></tr>`;
        }

        // Init
        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>
