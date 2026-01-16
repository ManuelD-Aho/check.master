<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue des escalades - Workflow
 * 
 * @var array $escalades Escalades ouvertes
 * @var array $stats Statistiques
 */

$pageTitle = 'Gestion des Escalades';
$pageDescription = 'Suivi et résolution des escalades workflow';
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
        
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-header h1 { color: var(--primary); font-size: 1.75rem; }
        .page-header-actions { display: flex; gap: 0.5rem; }
        
        .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--accent); text-decoration: none; margin-bottom: 1rem; }
        
        .filters-bar { background: var(--white); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .filters-bar select { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--white); padding: 1.25rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 0.75rem; color: var(--text-light); margin-bottom: 0.5rem; text-transform: uppercase; }
        .stat-card .value { font-size: 1.75rem; font-weight: 700; color: var(--primary); }
        .stat-card.warning .value { color: var(--warning); }
        .stat-card.danger .value { color: var(--danger); }
        
        .card { background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .card-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .card-header h2 { font-size: 1.125rem; color: var(--primary); }
        .card-body { padding: 1.5rem; }
        
        .escalade-item { border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; transition: box-shadow 0.2s; }
        .escalade-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .escalade-item.niveau-1 { border-left: 4px solid var(--accent); }
        .escalade-item.niveau-2 { border-left: 4px solid var(--warning); }
        .escalade-item.niveau-3 { border-left: 4px solid var(--danger); }
        
        .escalade-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; }
        .escalade-title { font-weight: 600; color: var(--primary); }
        .escalade-id { font-size: 0.875rem; color: var(--text-light); }
        .escalade-meta { display: flex; gap: 1rem; font-size: 0.875rem; color: var(--text-light); margin-bottom: 0.75rem; }
        .escalade-description { color: var(--text); margin-bottom: 1rem; }
        .escalade-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-primary { background: #dbeafe; color: #1d4ed8; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; text-decoration: none; font-size: 0.875rem; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-warning { background: var(--warning); color: var(--white); }
        .btn-danger { background: var(--danger); color: var(--white); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .btn:hover { opacity: 0.9; }
        
        .empty-state { text-align: center; padding: 3rem; color: var(--text-light); }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        
        .modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: var(--white); border-radius: 0.5rem; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); }
        .modal-header h2 { font-size: 1.25rem; margin: 0; }
        .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light); }
        .modal-body { padding: 1.5rem; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid var(--border); }
        
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.25rem; font-size: 0.875rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; }
        .form-group textarea { min-height: 100px; resize: vertical; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/workflow" class="back-link">← Retour au Workflow</a>
        
        <div class="page-header">
            <div>
                <h1>⚠️ <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <p style="color: var(--text-light); margin-top: 0.25rem;"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="page-header-actions">
                <button class="btn btn-primary" onclick="openCreateModal()">Nouvelle Escalade</button>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card danger">
                <h3>Escalades ouvertes</h3>
                <div class="value" id="statOuvertes">-</div>
            </div>
            <div class="stat-card warning">
                <h3>En cours</h3>
                <div class="value" id="statEnCours">-</div>
            </div>
            <div class="stat-card">
                <h3>Résolues (30j)</h3>
                <div class="value" id="statResolues">-</div>
            </div>
            <div class="stat-card">
                <h3>Mes escalades</h3>
                <div class="value" id="statMes">-</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-bar">
            <select id="filterStatut" onchange="loadEscalades()">
                <option value="">Tous les statuts</option>
                <option value="ouvertes" selected>Ouvertes</option>
                <option value="en_cours">En cours</option>
                <option value="resolues">Résolues</option>
            </select>
            <select id="filterType" onchange="loadEscalades()">
                <option value="">Tous les types</option>
                <option value="Blocage_workflow">Blocage workflow</option>
                <option value="Depassement_delai">Dépassement délai</option>
                <option value="Reclamation">Réclamation</option>
                <option value="Autre">Autre</option>
            </select>
        </div>

        <!-- Liste des escalades -->
        <div class="card">
            <div class="card-header">
                <h2>Escalades</h2>
                <span id="escaladeCount" class="badge badge-info">0</span>
            </div>
            <div class="card-body" id="escaladesList">
                <div class="empty-state">
                    <div class="icon">⏳</div>
                    <p>Chargement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Création -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nouvelle Escalade</h2>
                <button class="modal-close" onclick="closeCreateModal()">&times;</button>
            </div>
            <form id="createForm" onsubmit="createEscalade(event)">
                <?= CSRF::field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dossier_id">N° Dossier *</label>
                        <input type="number" id="dossier_id" name="dossier_id" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="type">Type *</label>
                        <select id="type" name="type" required>
                            <option value="Blocage_workflow">Blocage workflow</option>
                            <option value="Depassement_delai">Dépassement délai</option>
                            <option value="Reclamation">Réclamation</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required placeholder="Décrivez le problème en détail..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeCreateModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Résolution -->
    <div id="resolveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Résoudre l'escalade</h2>
                <button class="modal-close" onclick="closeResolveModal()">&times;</button>
            </div>
            <form id="resolveForm" onsubmit="resolveEscalade(event)">
                <input type="hidden" id="resolveId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="resolution">Résolution *</label>
                        <textarea id="resolution" name="resolution" required placeholder="Décrivez la résolution appliquée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeResolveModal()">Annuler</button>
                    <button type="submit" class="btn btn-success">Résoudre</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Chargement des données
        async function loadEscalades() {
            const statut = document.getElementById('filterStatut').value;
            const type = document.getElementById('filterType').value;
            
            let url = '/api/workflow/escalades?';
            if (statut) url += 'statut=' + statut + '&';
            if (type) url += 'type=' + type;
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    renderEscalades(result.data);
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Erreur de connexion');
            }
        }

        async function loadStats() {
            try {
                const [statsRes, mesRes] = await Promise.all([
                    fetch('/api/workflow/escalades/statistiques'),
                    fetch('/api/workflow/escalades/mes')
                ]);
                
                const stats = await statsRes.json();
                const mes = await mesRes.json();
                
                if (stats.success) {
                    document.getElementById('statOuvertes').textContent = stats.data.actives?.ouvertes || '0';
                    document.getElementById('statEnCours').textContent = stats.data.actives?.en_cours || '0';
                    document.getElementById('statResolues').textContent = stats.data.resolues_30j || '0';
                }
                
                if (mes.success) {
                    document.getElementById('statMes').textContent = mes.data.length || '0';
                }
            } catch (error) {
                console.error('Erreur stats:', error);
            }
        }

        function renderEscalades(escalades) {
            const container = document.getElementById('escaladesList');
            document.getElementById('escaladeCount').textContent = escalades.length;
            
            if (!escalades || escalades.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="icon">✅</div>
                        <p>Aucune escalade trouvée</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = escalades.map(e => `
                <div class="escalade-item niveau-${e.niveau_escalade || 1}">
                    <div class="escalade-header">
                        <div>
                            <span class="escalade-title">${escapeHtml(e.type_escalade)}</span>
                            <span class="escalade-id">#${e.id_escalade}</span>
                        </div>
                        <span class="badge ${getStatusBadgeClass(e.statut)}">${escapeHtml(e.statut)}</span>
                    </div>
                    <div class="escalade-meta">
                        <span>📁 Dossier #${e.dossier_id}</span>
                        <span>📊 Niveau ${e.niveau_escalade || 1}</span>
                        <span>📅 ${formatDate(e.created_at)}</span>
                        ${e.assigne_a ? '<span>👤 ' + escapeHtml(e.assigne_a) + '</span>' : ''}
                    </div>
                    <div class="escalade-description">${escapeHtml(e.description || 'Pas de description')}</div>
                    <div class="escalade-actions">
                        ${e.statut === 'Ouverte' ? `
                            <button class="btn btn-sm btn-primary" onclick="prendreEnCharge(${e.id_escalade})">Prendre en charge</button>
                        ` : ''}
                        ${e.statut === 'En_cours' ? `
                            <button class="btn btn-sm btn-success" onclick="openResolveModal(${e.id_escalade})">Résoudre</button>
                            <button class="btn btn-sm btn-warning" onclick="escalader(${e.id_escalade})">Escalader</button>
                        ` : ''}
                        <button class="btn btn-sm btn-outline" onclick="viewDetails(${e.id_escalade})">Détails</button>
                    </div>
                </div>
            `).join('');
        }

        function getStatusBadgeClass(statut) {
            switch(statut) {
                case 'Ouverte': return 'badge-danger';
                case 'En_cours': return 'badge-warning';
                case 'Resolue': return 'badge-success';
                case 'Fermee': return 'badge-info';
                default: return 'badge-primary';
            }
        }

        // Actions
        async function prendreEnCharge(id) {
            if (!confirm('Voulez-vous prendre en charge cette escalade ?')) return;
            
            try {
                const response = await fetch('/api/workflow/escalades/' + id + '/prendre-en-charge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Escalade prise en charge');
                    loadEscalades();
                    loadStats();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function escalader(id) {
            const motif = prompt('Motif de l\'escalade au niveau supérieur:');
            if (!motif) return;
            
            try {
                const response = await fetch('/api/workflow/escalades/' + id + '/escalader', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ motif })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Escalade effectuée');
                    loadEscalades();
                    loadStats();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function createEscalade(event) {
            event.preventDefault();
            
            const form = document.getElementById('createForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/workflow/escalades', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Escalade créée');
                    closeCreateModal();
                    loadEscalades();
                    loadStats();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function resolveEscalade(event) {
            event.preventDefault();
            
            const id = document.getElementById('resolveId').value;
            const resolution = document.getElementById('resolution').value;
            
            try {
                const response = await fetch('/api/workflow/escalades/' + id + '/resoudre', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ resolution })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Escalade résolue');
                    closeResolveModal();
                    loadEscalades();
                    loadStats();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        function viewDetails(id) {
            window.location.href = '/workflow/escalades/' + id;
        }

        // Modals
        function openCreateModal() {
            document.getElementById('createForm').reset();
            document.getElementById('createModal').classList.add('active');
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('active');
        }
        
        function openResolveModal(id) {
            document.getElementById('resolveId').value = id;
            document.getElementById('resolution').value = '';
            document.getElementById('resolveModal').classList.add('active');
        }
        
        function closeResolveModal() {
            document.getElementById('resolveModal').classList.remove('active');
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

        function showError(message) {
            document.getElementById('escaladesList').innerHTML = `
                <div class="empty-state">
                    <div class="icon">❌</div>
                    <p>${escapeHtml(message)}</p>
                </div>
            `;
        }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            loadEscalades();
            loadStats();
        });
    </script>
</body>
</html>
