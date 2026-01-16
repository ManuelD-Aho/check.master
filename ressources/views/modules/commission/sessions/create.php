<?php

declare(strict_types=1);

/**
 * CheckMaster - Création d'une Session de Commission
 */

$title = 'Nouvelle Session';
$pageTitle = 'Créer une Session de Commission';
$currentPage = 'commission-sessions';
$breadcrumbs = [
    ['label' => 'Commission', 'url' => '/modules/commission'],
    ['label' => 'Sessions', 'url' => '/modules/commission/sessions'],
    ['label' => 'Nouvelle', 'url' => '']
];

ob_start();
?>

<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="page-description">Créer une nouvelle session d'évaluation</p>
    </div>
</div>

<form method="POST" action="/modules/commission/sessions/store" class="form-container">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">Informations de la Session</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="nom" class="form-label required">Nom de la session</label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           class="form-control" 
                           placeholder="Ex: Session Master 2 - Janvier 2024"
                           required>
                    <small class="form-help">Nom descriptif identifiant clairement la session</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_debut" class="form-label required">Date de début</label>
                    <input type="date" 
                           id="date_debut" 
                           name="date_debut" 
                           class="form-control"
                           min="<?= date('Y-m-d') ?>"
                           required>
                </div>
                <div class="form-group">
                    <label for="date_fin" class="form-label required">Date de fin</label>
                    <input type="date" 
                           id="date_fin" 
                           name="date_fin" 
                           class="form-control"
                           min="<?= date('Y-m-d') ?>"
                           required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="president_id" class="form-label required">Président de session</label>
                    <select id="president_id" name="president_id" class="form-control" required>
                        <option value="">Sélectionner un président</option>
                        <option value="1">Prof. Martin DUBOIS</option>
                        <option value="2">Prof. Sophie LAURENT</option>
                        <option value="3">Prof. Jean BERNARD</option>
                        <option value="4">Prof. Marie PETIT</option>
                        <option value="5">Prof. Pierre GARCIA</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type_session" class="form-label required">Type de session</label>
                    <select id="type_session" name="type_session" class="form-control" required>
                        <option value="">Sélectionner un type</option>
                        <option value="master1">Master 1</option>
                        <option value="master2">Master 2</option>
                        <option value="doctorat">Doctorat</option>
                        <option value="rattrapage">Rattrapage</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              rows="4"
                              placeholder="Description détaillée de la session, objectifs, modalités..."></textarea>
                    <small class="form-help">Informations complémentaires sur la session</small>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">Membres de la Commission</h2>
            <p class="text-sm text-gray-600 mt-1">Sélectionner les membres qui participeront à cette session</p>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group full-width">
                    <label class="form-label">Membres disponibles</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="1">
                            <span>Prof. Martin DUBOIS - Informatique</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="2">
                            <span>Prof. Sophie LAURENT - Mathématiques</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="3">
                            <span>Prof. Jean BERNARD - Physique</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="4">
                            <span>Prof. Marie PETIT - Chimie</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="5">
                            <span>Prof. Pierre GARCIA - Biologie</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="6">
                            <span>Dr. Claire MARTIN - Sciences de l'ingénieur</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="7">
                            <span>Dr. Thomas ROUX - Électronique</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="membres[]" value="8">
                            <span>Dr. Anne SIMON - Mécanique</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h2 class="card-title">Paramètres d'Évaluation</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="note_minimale" class="form-label">Note minimale de passage</label>
                    <input type="number" 
                           id="note_minimale" 
                           name="note_minimale" 
                           class="form-control" 
                           min="0" 
                           max="20" 
                           step="0.5"
                           value="10">
                </div>
                <div class="form-group">
                    <label for="quorum_vote" class="form-label">Quorum pour les votes (%)</label>
                    <input type="number" 
                           id="quorum_vote" 
                           name="quorum_vote" 
                           class="form-control" 
                           min="0" 
                           max="100"
                           value="50">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label class="checkbox-label">
                        <input type="checkbox" name="notification_auto" value="1" checked>
                        <span>Envoyer des notifications automatiques aux membres</span>
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label class="checkbox-label">
                        <input type="checkbox" name="vote_anonyme" value="1">
                        <span>Activer le vote anonyme</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="/modules/commission/sessions" class="btn btn-secondary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Annuler
        </a>
        <button type="submit" class="btn btn-primary">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Créer la session
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
        if (dateFin.value && dateFin.value < this.value) {
            dateFin.value = this.value;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layouts/app.php';
