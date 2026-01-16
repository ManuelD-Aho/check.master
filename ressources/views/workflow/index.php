<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue principale du Workflow - Administration
 * 
 * @var array $stats Statistiques du workflow
 * @var array $etats Liste des états
 */

$pageTitle = 'Gestion du Workflow';
$pageDescription = 'Vue d\'ensemble du workflow et gestion des transitions';
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
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--white); padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 0.875rem; color: var(--text-light); margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .stat-card.warning .value { color: var(--warning); }
        .stat-card.danger .value { color: var(--danger); }
        
        .workflow-container { background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem; }
        .workflow-container h2 { margin-bottom: 1.5rem; color: var(--primary); }
        
        .workflow-diagram { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; }
        .workflow-state { padding: 1rem 1.5rem; border-radius: 0.5rem; border: 2px solid var(--border); min-width: 160px; text-align: center; position: relative; }
        .workflow-state.initial { border-color: var(--accent); background: #e6fffa; }
        .workflow-state.final { border-color: var(--success); background: #f0fff4; }
        .workflow-state .count { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .workflow-state .name { font-size: 0.875rem; color: var(--text-light); }
        
        .transitions-list { margin-top: 1.5rem; }
        .transition-item { padding: 0.75rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .transition-item:last-child { border-bottom: none; }
        .transition-arrow { color: var(--accent); margin: 0 0.5rem; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; text-decoration: none; font-size: 0.875rem; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-warning { background: var(--warning); color: var(--white); }
        .btn:hover { opacity: 0.9; }
        
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        .alert-warning { background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; }
        .alert-danger { background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--bg); font-weight: 600; color: var(--text-light); }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        
        .tabs { display: flex; gap: 0.25rem; border-bottom: 2px solid var(--border); margin-bottom: 1.5rem; }
        .tab { padding: 0.75rem 1.5rem; border: none; background: none; cursor: pointer; font-weight: 500; color: var(--text-light); border-bottom: 2px solid transparent; margin-bottom: -2px; }
        .tab.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab:hover { color: var(--primary); }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: stretch; gap: 1rem; }
            .workflow-diagram { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>🔄 <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <p style="color: var(--text-light); margin-top: 0.25rem;"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="page-header-actions">
                <a href="/workflow/escalades" class="btn btn-warning">Escalades</a>
                <button class="btn btn-outline" onclick="refreshStats()">Actualiser</button>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <h3>Dossiers en cours</h3>
                <div class="value" id="statDossiersEnCours">-</div>
            </div>
            <div class="stat-card">
                <h3>En attente commission</h3>
                <div class="value" id="statAttenteCommission">-</div>
            </div>
            <div class="stat-card warning">
                <h3>Alertes SLA</h3>
                <div class="value" id="statAlertesSLA">-</div>
            </div>
            <div class="stat-card danger">
                <h3>Escalades actives</h3>
                <div class="value" id="statEscalades">-</div>
            </div>
        </div>

        <!-- Onglets -->
        <div class="tabs">
            <button class="tab active" data-tab="etats">États du Workflow</button>
            <button class="tab" data-tab="transitions">Transitions Récentes</button>
            <button class="tab" data-tab="retards">Dossiers en Retard</button>
        </div>

        <!-- Contenu onglet États -->
        <div class="tab-content active" id="tab-etats">
            <div class="workflow-container">
                <h2>États et nombre de dossiers</h2>
                <div class="workflow-diagram" id="workflowDiagram">
                    <p>Chargement...</p>
                </div>
            </div>
        </div>

        <!-- Contenu onglet Transitions -->
        <div class="tab-content" id="tab-transitions">
            <div class="workflow-container">
                <h2>Transitions Récentes</h2>
                <table class="table" id="transitionsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Dossier</th>
                            <th>Étudiant</th>
                            <th>Transition</th>
                            <th>Par</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Contenu onglet Retards -->
        <div class="tab-content" id="tab-retards">
            <div class="workflow-container">
                <h2>Dossiers en Retard</h2>
                <table class="table" id="retardsTable">
                    <thead>
                        <tr>
                            <th>Dossier</th>
                            <th>Étudiant</th>
                            <th>État</th>
                            <th>Jours de retard</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Gestion des onglets
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
            });
        });

        // Chargement des données
        async function loadStats() {
            try {
                const response = await fetch('/api/workflow/statistiques');
                const result = await response.json();
                if (result.success) {
                    updateStatsDisplay(result.data);
                }
            } catch (error) {
                console.error('Erreur chargement stats:', error);
            }
        }

        async function loadEtats() {
            try {
                const response = await fetch('/api/workflow/etats');
                const result = await response.json();
                if (result.success) {
                    renderWorkflowDiagram(result.data);
                }
            } catch (error) {
                document.getElementById('workflowDiagram').innerHTML = '<p>Erreur de chargement</p>';
            }
        }

        async function loadTransitions() {
            try {
                const response = await fetch('/api/workflow/transitions/recentes?limit=20');
                const result = await response.json();
                if (result.success) {
                    renderTransitions(result.data);
                }
            } catch (error) {
                console.error('Erreur chargement transitions:', error);
            }
        }

        async function loadRetards() {
            try {
                const response = await fetch('/api/workflow/dossiers/retard');
                const result = await response.json();
                if (result.success) {
                    renderRetards(result.data);
                }
            } catch (error) {
                console.error('Erreur chargement retards:', error);
            }
        }

        function updateStatsDisplay(stats) {
            // Les stats réelles seront récupérées des données
            document.getElementById('statDossiersEnCours').textContent = stats.total_en_cours || '0';
            document.getElementById('statAttenteCommission').textContent = stats.attente_commission || '0';
            document.getElementById('statAlertesSLA').textContent = stats.alertes || '0';
            document.getElementById('statEscalades').textContent = stats.escalades || '0';
        }

        function renderWorkflowDiagram(etats) {
            const container = document.getElementById('workflowDiagram');
            if (!etats || etats.length === 0) {
                container.innerHTML = '<p>Aucun état configuré</p>';
                return;
            }

            container.innerHTML = etats.map(etat => `
                <div class="workflow-state ${etat.est_initial ? 'initial' : ''} ${etat.est_terminal ? 'final' : ''}">
                    <div class="count">${etat.nb_dossiers || 0}</div>
                    <div class="name">${escapeHtml(etat.nom_etat || etat.code_etat)}</div>
                </div>
            `).join('');
        }

        function renderTransitions(transitions) {
            const tbody = document.querySelector('#transitionsTable tbody');
            if (!transitions || transitions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Aucune transition récente</td></tr>';
                return;
            }

            tbody.innerHTML = transitions.map(t => `
                <tr>
                    <td>${formatDate(t.created_at)}</td>
                    <td>#${t.dossier_id}</td>
                    <td>${escapeHtml((t.prenom_etu || '') + ' ' + (t.nom_etu || ''))}</td>
                    <td>
                        <span class="badge badge-info">${escapeHtml(t.nom_source || '?')}</span>
                        →
                        <span class="badge badge-success">${escapeHtml(t.nom_cible || '?')}</span>
                    </td>
                    <td>${escapeHtml(t.utilisateur || 'Système')}</td>
                </tr>
            `).join('');
        }

        function renderRetards(dossiers) {
            const tbody = document.querySelector('#retardsTable tbody');
            if (!dossiers || dossiers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Aucun dossier en retard 🎉</td></tr>';
                return;
            }

            tbody.innerHTML = dossiers.map(d => `
                <tr>
                    <td>#${d.id_dossier}</td>
                    <td>${escapeHtml((d.prenom_etu || '') + ' ' + (d.nom_etu || ''))}</td>
                    <td><span class="badge badge-warning">${escapeHtml(d.nom_etat || d.code_etat)}</span></td>
                    <td><span class="badge badge-danger">${d.jours_retard} jours</span></td>
                    <td>
                        <a href="/secretariat/dossiers/${d.id_dossier}" class="btn btn-outline btn-sm">Voir</a>
                    </td>
                </tr>
            `).join('');
        }

        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }

        function refreshStats() {
            loadStats();
            loadEtats();
            loadTransitions();
            loadRetards();
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            refreshStats();
        });
    </script>
</body>
</html>
