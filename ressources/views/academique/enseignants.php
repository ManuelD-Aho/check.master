<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue liste des enseignants - Administration Académique
 * 
 * @var array $data Enseignants paginés
 * @var array $grades Liste des grades
 * @var array $specialites Liste des spécialités
 */

$pageTitle = 'Gestion des Enseignants';
$pageDescription = 'Liste et gestion des enseignants';
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
        
        .filters-bar { background: var(--white); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .filters-bar input, .filters-bar select { padding: 0.5rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; }
        .filters-bar input { min-width: 250px; }
        
        .stats-bar { display: flex; gap: 2rem; margin-bottom: 1rem; padding: 1rem; background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-item { display: flex; flex-direction: column; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .stat-label { font-size: 0.875rem; color: var(--text-light); }
        
        .card { background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-body { padding: 0; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--bg); font-weight: 600; color: var(--text-light); font-size: 0.875rem; }
        .table tr:hover { background: #f8fafc; }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; text-decoration: none; font-size: 0.875rem; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-secondary { background: var(--bg); color: var(--text); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .btn:hover { opacity: 0.9; }
        
        .pagination-wrapper { padding: 1rem; display: flex; justify-content: center; }
        .pagination { display: flex; gap: 0.25rem; }
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
        .modal-footer { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid var(--border); }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 0 1.5rem; margin-bottom: 1rem; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 500; margin-bottom: 0.25rem; font-size: 0.875rem; }
        .form-group input, .form-group select { padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; }
        
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
                <h1>👨‍🏫 <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <p style="color: var(--text-light); margin-top: 0.25rem;"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="page-header-actions">
                <button class="btn btn-primary" onclick="openCreateModal()">+ Nouvel Enseignant</button>
            </div>
        </div>

        <!-- Filtres -->
        <form class="filters-bar" onsubmit="loadData(); return false;">
            <input type="text" id="searchInput" placeholder="Rechercher par nom, email..." onchange="loadData()">
            <select id="filterGrade" onchange="loadData()">
                <option value="">Tous les grades</option>
            </select>
            <select id="filterSpecialite" onchange="loadData()">
                <option value="">Toutes les spécialités</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filtrer</button>
        </form>

        <!-- Statistiques -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-value" id="statTotal">-</span>
                <span class="stat-label">Total enseignants</span>
            </div>
            <div class="stat-item">
                <span class="stat-value" id="statActifs">-</span>
                <span class="stat-label">Actifs</span>
            </div>
        </div>

        <!-- Tableau -->
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Grade</th>
                            <th>Spécialité</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="enseignantsBody">
                        <tr><td colspan="7" class="empty-state">Chargement...</td></tr>
                    </tbody>
                </table>
                <div class="pagination-wrapper" id="pagination"></div>
            </div>
        </div>
    </div>

    <!-- Modal Création/Édition -->
    <div id="enseignantModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nouvel Enseignant</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="enseignantForm" onsubmit="submitEnseignant(event)">
                <?= CSRF::field() ?>
                <input type="hidden" id="enseignantId" name="id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom_ens">Nom *</label>
                        <input type="text" id="nom_ens" name="nom_ens" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom_ens">Prénom *</label>
                        <input type="text" id="prenom_ens" name="prenom_ens" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email_ens">Email *</label>
                        <input type="email" id="email_ens" name="email_ens" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone_ens">Téléphone</label>
                        <input type="tel" id="telephone_ens" name="telephone_ens">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="grade_id">Grade</label>
                        <select id="grade_id" name="grade_id">
                            <option value="">-- Sélectionner --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fonction_id">Fonction</label>
                        <select id="fonction_id" name="fonction_id">
                            <option value="">-- Sélectionner --</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="specialite_id">Spécialité</label>
                        <select id="specialite_id" name="specialite_id">
                            <option value="">-- Sélectionner --</option>
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

    <script>
        let currentPage = 1;
        let gradesData = [];
        let specialitesData = [];
        let fonctionsData = [];

        // Initialisation
        document.addEventListener('DOMContentLoaded', async () => {
            await loadReferentiels();
            loadData();
            loadStats();
        });

        async function loadReferentiels() {
            try {
                const [gradesRes, specialitesRes, fonctionsRes] = await Promise.all([
                    fetch('/api/academique/grades'),
                    fetch('/api/academique/specialites'),
                    fetch('/api/academique/fonctions')
                ]);
                
                const grades = await gradesRes.json();
                const specialites = await specialitesRes.json();
                const fonctions = await fonctionsRes.json();
                
                if (grades.success) {
                    gradesData = grades.data;
                    populateSelect('filterGrade', grades.data, 'id_grade', 'lib_grade');
                    populateSelect('grade_id', grades.data, 'id_grade', 'lib_grade');
                }
                
                if (specialites.success) {
                    specialitesData = specialites.data;
                    populateSelect('filterSpecialite', specialites.data, 'id_specialite', 'lib_specialite');
                    populateSelect('specialite_id', specialites.data, 'id_specialite', 'lib_specialite');
                }
                
                if (fonctions.success) {
                    fonctionsData = fonctions.data;
                    populateSelect('fonction_id', fonctions.data, 'id_fonction', 'lib_fonction');
                }
            } catch (error) {
                console.error('Erreur chargement référentiels:', error);
            }
        }

        function populateSelect(selectId, data, valueField, textField) {
            const select = document.getElementById(selectId);
            const firstOption = select.options[0];
            select.innerHTML = '';
            select.appendChild(firstOption);
            
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = item[textField];
                select.appendChild(option);
            });
        }

        async function loadData() {
            const search = document.getElementById('searchInput').value;
            const grade = document.getElementById('filterGrade').value;
            const specialite = document.getElementById('filterSpecialite').value;
            
            let url = `/api/academique/enseignants?page=${currentPage}`;
            if (search) url += '&q=' + encodeURIComponent(search);
            if (grade) url += '&grade=' + grade;
            if (specialite) url += '&specialite=' + specialite;
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    renderEnseignants(result.data.enseignants);
                    renderPagination(result.data.pagination);
                    document.getElementById('statTotal').textContent = result.data.pagination.totalItems;
                }
            } catch (error) {
                showError('Erreur de connexion');
            }
        }

        async function loadStats() {
            try {
                const response = await fetch('/api/academique/enseignants/statistiques');
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('statActifs').textContent = result.data.actifs || '0';
                }
            } catch (error) {
                console.error('Erreur stats:', error);
            }
        }

        function renderEnseignants(enseignants) {
            const tbody = document.getElementById('enseignantsBody');
            
            if (!enseignants || enseignants.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><div class="icon">👨‍🏫</div><p>Aucun enseignant trouvé</p></td></tr>';
                return;
            }
            
            tbody.innerHTML = enseignants.map(e => `
                <tr>
                    <td><strong>${escapeHtml((e.prenom_ens || '') + ' ' + (e.nom_ens || ''))}</strong></td>
                    <td>${escapeHtml(e.email_ens || '-')}</td>
                    <td>${escapeHtml(e.telephone_ens || '-')}</td>
                    <td>${e.grade_id ? getGradeLabel(e.grade_id) : '-'}</td>
                    <td>${e.specialite_id ? getSpecialiteLabel(e.specialite_id) : '-'}</td>
                    <td>${e.actif ? '<span class="badge badge-success">Actif</span>' : '<span class="badge badge-danger">Inactif</span>'}</td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="editEnseignant(${e.id_enseignant})">Modifier</button>
                    </td>
                </tr>
            `).join('');
        }

        function getGradeLabel(id) {
            const grade = gradesData.find(g => g.id_grade == id);
            return grade ? `<span class="badge badge-info">${escapeHtml(grade.lib_grade)}</span>` : '-';
        }

        function getSpecialiteLabel(id) {
            const spec = specialitesData.find(s => s.id_specialite == id);
            return spec ? escapeHtml(spec.lib_specialite) : '-';
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
            document.getElementById('modalTitle').textContent = 'Nouvel Enseignant';
            document.getElementById('enseignantForm').reset();
            document.getElementById('enseignantId').value = '';
            document.getElementById('enseignantModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('enseignantModal').classList.remove('active');
        }

        async function editEnseignant(id) {
            try {
                const response = await fetch('/api/academique/enseignants/' + id);
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    document.getElementById('modalTitle').textContent = 'Modifier l\'enseignant';
                    document.getElementById('enseignantId').value = data.id_enseignant;
                    document.getElementById('nom_ens').value = data.nom_ens || '';
                    document.getElementById('prenom_ens').value = data.prenom_ens || '';
                    document.getElementById('email_ens').value = data.email_ens || '';
                    document.getElementById('telephone_ens').value = data.telephone_ens || '';
                    document.getElementById('grade_id').value = data.grade_id || '';
                    document.getElementById('fonction_id').value = data.fonction_id || '';
                    document.getElementById('specialite_id').value = data.specialite_id || '';
                    document.getElementById('enseignantModal').classList.add('active');
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de connexion');
            }
        }

        async function submitEnseignant(event) {
            event.preventDefault();
            
            const form = document.getElementById('enseignantForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = data.id;
            delete data.id;
            
            const url = id ? '/api/academique/enseignants/' + id : '/api/academique/enseignants';
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
                    loadStats();
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

        function showError(message) {
            document.getElementById('enseignantsBody').innerHTML = `<tr><td colspan="7" class="empty-state"><div class="icon">❌</div><p>${escapeHtml(message)}</p></td></tr>`;
        }
    </script>
</body>
</html>
