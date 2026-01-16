<?php

declare(strict_types=1);

use Src\Support\CSRF;

/**
 * Vue Import/Export - Administration
 * 
 * @var array $historique Historique des imports
 */

$pageTitle = 'Import / Export';
$pageDescription = 'Gestion des imports et exports de données';
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
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { color: var(--primary); font-size: 1.75rem; }
        .page-header p { color: var(--text-light); margin-top: 0.25rem; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
        
        .card { background: var(--white); border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); }
        .card-header h2 { font-size: 1.125rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem; }
        .card-body { padding: 1.5rem; }
        
        .import-zone { border: 2px dashed var(--border); border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
        .import-zone:hover { border-color: var(--accent); background: #f0fdfa; }
        .import-zone.dragover { border-color: var(--accent); background: #e6fffa; }
        .import-zone .icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .import-zone p { color: var(--text-light); margin-bottom: 0.5rem; }
        .import-zone input { display: none; }
        
        .export-list { list-style: none; }
        .export-list li { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border); }
        .export-list li:last-child { border-bottom: none; }
        .export-list .export-info { display: flex; align-items: center; gap: 0.75rem; }
        .export-list .export-icon { font-size: 1.5rem; }
        .export-list .export-name { font-weight: 500; }
        .export-list .export-desc { font-size: 0.875rem; color: var(--text-light); }
        
        .template-list { list-style: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); }
        .template-list li { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { background: var(--bg); font-weight: 600; color: var(--text-light); font-size: 0.875rem; }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; border: none; text-decoration: none; font-size: 0.875rem; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-success { background: var(--success); color: var(--white); }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .btn:hover { opacity: 0.9; }
        
        .progress { height: 0.5rem; background: var(--border); border-radius: 0.25rem; overflow: hidden; margin-top: 1rem; display: none; }
        .progress-bar { height: 100%; background: var(--accent); width: 0%; transition: width 0.3s; }
        
        .result-box { padding: 1rem; border-radius: 0.5rem; margin-top: 1rem; display: none; }
        .result-box.success { background: #dcfce7; color: #15803d; }
        .result-box.error { background: #fee2e2; color: #dc2626; }
        
        .empty-state { text-align: center; padding: 2rem; color: var(--text-light); }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>📦 <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <p><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div class="grid">
            <!-- Import -->
            <div class="card">
                <div class="card-header">
                    <h2>📥 Import de données</h2>
                </div>
                <div class="card-body">
                    <form id="importForm" onsubmit="handleImport(event)">
                        <?= CSRF::field() ?>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Type de données</label>
                            <select id="importType" name="type" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; margin-bottom: 1rem;">
                                <option value="etudiants">Étudiants</option>
                                <option value="enseignants">Enseignants</option>
                            </select>
                        </div>
                        
                        <div class="import-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                            <div class="icon">📄</div>
                            <p><strong>Cliquez ou glissez un fichier CSV</strong></p>
                            <p>Format: CSV séparé par point-virgule (;)</p>
                            <input type="file" id="fileInput" name="file" accept=".csv,.xlsx,.xls" onchange="handleFileSelect(this)">
                        </div>
                        
                        <div id="selectedFile" style="display: none; margin-top: 1rem; padding: 0.75rem; background: var(--bg); border-radius: 0.375rem;">
                            <span id="fileName"></span>
                            <button type="button" class="btn btn-sm btn-outline" onclick="clearFile()" style="float: right;">✕</button>
                        </div>
                        
                        <div class="progress" id="importProgress">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>
                        
                        <div class="result-box" id="importResult"></div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;" id="importBtn" disabled>
                            Importer les données
                        </button>
                    </form>
                    
                    <ul class="template-list">
                        <li>
                            <span>Template étudiants</span>
                            <a href="/admin/template/etudiants" class="btn btn-sm btn-outline">Télécharger</a>
                        </li>
                        <li>
                            <span>Template enseignants</span>
                            <a href="/admin/template/enseignants" class="btn btn-sm btn-outline">Télécharger</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Export -->
            <div class="card">
                <div class="card-header">
                    <h2>📤 Export de données</h2>
                </div>
                <div class="card-body">
                    <ul class="export-list">
                        <li>
                            <div class="export-info">
                                <span class="export-icon">👨‍🎓</span>
                                <div>
                                    <div class="export-name">Étudiants</div>
                                    <div class="export-desc">Liste complète des étudiants actifs</div>
                                </div>
                            </div>
                            <a href="/admin/export/etudiants" class="btn btn-success">Exporter</a>
                        </li>
                        <li>
                            <div class="export-info">
                                <span class="export-icon">👨‍🏫</span>
                                <div>
                                    <div class="export-name">Enseignants</div>
                                    <div class="export-desc">Liste complète des enseignants actifs</div>
                                </div>
                            </div>
                            <a href="/admin/export/enseignants" class="btn btn-success">Exporter</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Historique -->
        <div class="card">
            <div class="card-header">
                <h2>📋 Historique des imports</h2>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Fichier</th>
                            <th>Lignes</th>
                            <th>Réussis</th>
                            <th>Erreurs</th>
                            <th>Par</th>
                        </tr>
                    </thead>
                    <tbody id="historiqueBody">
                        <tr><td colspan="7" class="empty-state">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Drag & Drop
        const dropZone = document.getElementById('dropZone');
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            
            if (e.dataTransfer.files.length) {
                document.getElementById('fileInput').files = e.dataTransfer.files;
                handleFileSelect(document.getElementById('fileInput'));
            }
        });

        function handleFileSelect(input) {
            if (input.files.length) {
                const file = input.files[0];
                document.getElementById('fileName').textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                document.getElementById('selectedFile').style.display = 'block';
                document.getElementById('importBtn').disabled = false;
                hideResult();
            }
        }

        function clearFile() {
            document.getElementById('fileInput').value = '';
            document.getElementById('selectedFile').style.display = 'none';
            document.getElementById('importBtn').disabled = true;
            hideResult();
        }

        async function handleImport(event) {
            event.preventDefault();
            
            const type = document.getElementById('importType').value;
            const fileInput = document.getElementById('fileInput');
            
            if (!fileInput.files.length) {
                alert('Veuillez sélectionner un fichier');
                return;
            }
            
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            
            showProgress();
            document.getElementById('importBtn').disabled = true;
            
            try {
                const response = await fetch('/api/admin/import/' + type, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                hideProgress();
                
                if (result.success) {
                    showResult(true, result.message);
                    loadHistorique();
                    clearFile();
                } else {
                    showResult(false, result.message);
                }
            } catch (error) {
                hideProgress();
                showResult(false, 'Erreur de connexion au serveur');
            }
            
            document.getElementById('importBtn').disabled = false;
        }

        function showProgress() {
            const progress = document.getElementById('importProgress');
            const bar = document.getElementById('progressBar');
            progress.style.display = 'block';
            
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 90) {
                    clearInterval(interval);
                } else {
                    width += 10;
                    bar.style.width = width + '%';
                }
            }, 200);
        }

        function hideProgress() {
            document.getElementById('importProgress').style.display = 'none';
            document.getElementById('progressBar').style.width = '0%';
        }

        function showResult(success, message) {
            const resultBox = document.getElementById('importResult');
            resultBox.style.display = 'block';
            resultBox.className = 'result-box ' + (success ? 'success' : 'error');
            resultBox.textContent = message;
        }

        function hideResult() {
            document.getElementById('importResult').style.display = 'none';
        }

        async function loadHistorique() {
            try {
                const response = await fetch('/api/admin/imports/historique');
                const result = await response.json();
                
                if (result.success) {
                    renderHistorique(result.data.imports);
                }
            } catch (error) {
                console.error('Erreur chargement historique:', error);
            }
        }

        function renderHistorique(imports) {
            const tbody = document.getElementById('historiqueBody');
            
            if (!imports || imports.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state">Aucun import effectué</td></tr>';
                return;
            }
            
            tbody.innerHTML = imports.map(i => `
                <tr>
                    <td>${formatDate(i.created_at)}</td>
                    <td><span class="badge badge-info">${escapeHtml(i.type_import)}</span></td>
                    <td>${escapeHtml(i.fichier_nom)}</td>
                    <td>${i.nb_lignes_totales || 0}</td>
                    <td><span class="badge badge-success">${i.nb_lignes_reussies || 0}</span></td>
                    <td>${i.nb_lignes_erreurs > 0 ? `<span class="badge badge-danger">${i.nb_lignes_erreurs}</span>` : '-'}</td>
                    <td>${escapeHtml(i.nom_utilisateur || '-')}</td>
                </tr>
            `).join('');
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
            return d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        // Init
        document.addEventListener('DOMContentLoaded', loadHistorique);
    </script>
</body>
</html>
