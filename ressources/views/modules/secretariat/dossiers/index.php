<?php

declare(strict_types=1);

/**
 * CheckMaster - Vue Index Dossiers Étudiants (Secrétariat)
 * =========================================================
 * Gestion complète des dossiers étudiants par le secrétariat
 * 
 * Fonctionnalités:
 * - Liste complète des dossiers avec filtres avancés
 * - Tableau de complétude des documents
 * - Validation et demande de compléments
 * - Suivi du workflow des dossiers
 * - Statistiques en temps réel
 */

// Variables de démonstration
$title = 'Dossiers Étudiants';
$pageTitle = 'Gestion des Dossiers Étudiants';
$currentPage = 'dossiers';
$breadcrumbs = [
    ['label' => 'Secrétariat', 'url' => '/modules/secretariat'],
    ['label' => 'Dossiers', 'url' => '/modules/secretariat/dossiers']
];

// Statistiques globales
$stats = [
    'total_dossiers' => 156,
    'complets' => 98,
    'incomplets' => 42,
    'en_attente_validation' => 16,
    'taux_completude' => 63, // pourcentage
    'nouveaux_aujourd_hui' => 8
];

// Liste des documents requis par type de dossier
$documentsRequis = [
    'Pièce d\'identité',
    'Diplôme de licence',
    'Relevés de notes L3',
    'CV actualisé',
    'Lettre de motivation',
    'Photo d\'identité',
    'Certificat de naissance',
    'Certificat médical',
    'Attestation d\'assurance',
    'Reçu de paiement'
];

// Liste des dossiers (données de démonstration)
$dossiers = [
    [
        'id' => 'DOS-2024-001',
        'id_hash' => 'abc123',
        'etudiant' => [
            'nom' => 'DIALLO',
            'prenom' => 'Mariama',
            'matricule' => 'M2-2024-001',
            'email' => 'mariama.diallo@ucad.edu.sn',
            'telephone' => '+221 77 123 45 67',
            'photo_url' => null
        ],
        'etat_workflow' => 'valide',
        'etat_label' => 'Dossier validé',
        'documents_deposes' => 10,
        'documents_requis' => 10,
        'completude' => 100,
        'documents_manquants' => [],
        'date_creation' => '2024-01-10',
        'date_derniere_maj' => '2024-01-28',
        'validateur' => 'Mme SOW - Secrétaire',
        'commentaire' => 'Dossier complet et conforme',
        'statut_paiement' => 'paye',
        'can_validate' => false
    ],
    [
        'id' => 'DOS-2024-002',
        'id_hash' => 'def456',
        'etudiant' => [
            'nom' => 'NDIAYE',
            'prenom' => 'Abdoulaye',
            'matricule' => 'M2-2024-015',
            'email' => 'abdoulaye.ndiaye@ucad.edu.sn',
            'telephone' => '+221 76 234 56 78',
            'photo_url' => null
        ],
        'etat_workflow' => 'documents_incomplets',
        'etat_label' => 'Documents incomplets',
        'documents_deposes' => 7,
        'documents_requis' => 10,
        'completude' => 70,
        'documents_manquants' => ['Certificat médical', 'Attestation d\'assurance', 'Reçu de paiement'],
        'date_creation' => '2024-01-18',
        'date_derniere_maj' => '2024-01-25',
        'validateur' => null,
        'commentaire' => 'En attente documents manquants',
        'statut_paiement' => 'attente',
        'can_validate' => false
    ],
    [
        'id' => 'DOS-2024-003',
        'id_hash' => 'ghi789',
        'etudiant' => [
            'nom' => 'SARR',
            'prenom' => 'Fatou',
            'matricule' => 'M2-2024-032',
            'email' => 'fatou.sarr@ucad.edu.sn',
            'telephone' => '+221 77 345 67 89',
            'photo_url' => null
        ],
        'etat_workflow' => 'en_attente_validation',
        'etat_label' => 'En attente validation',
        'documents_deposes' => 10,
        'documents_requis' => 10,
        'completude' => 100,
        'documents_manquants' => [],
        'date_creation' => '2024-01-22',
        'date_derniere_maj' => '2024-01-29',
        'validateur' => null,
        'commentaire' => null,
        'statut_paiement' => 'paye',
        'can_validate' => true
    ],
    [
        'id' => 'DOS-2024-004',
        'id_hash' => 'jkl012',
        'etudiant' => [
            'nom' => 'BA',
            'prenom' => 'Moussa',
            'matricule' => 'M2-2024-048',
            'email' => 'moussa.ba@ucad.edu.sn',
            'telephone' => '+221 76 456 78 90',
            'photo_url' => null
        ],
        'etat_workflow' => 'documents_incomplets',
        'etat_label' => 'Documents incomplets',
        'documents_deposes' => 5,
        'documents_requis' => 10,
        'completude' => 50,
        'documents_manquants' => ['CV actualisé', 'Lettre de motivation', 'Certificat de naissance', 'Certificat médical', 'Reçu de paiement'],
        'date_creation' => '2024-01-15',
        'date_derniere_maj' => '2024-01-20',
        'validateur' => null,
        'commentaire' => null,
        'statut_paiement' => 'attente',
        'can_validate' => false
    ],
    [
        'id' => 'DOS-2024-005',
        'id_hash' => 'mno345',
        'etudiant' => [
            'nom' => 'FAYE',
            'prenom' => 'Aissatou',
            'matricule' => 'M2-2024-067',
            'email' => 'aissatou.faye@ucad.edu.sn',
            'telephone' => '+221 77 567 89 01',
            'photo_url' => null
        ],
        'etat_workflow' => 'en_attente_validation',
        'etat_label' => 'En attente validation',
        'documents_deposes' => 10,
        'documents_requis' => 10,
        'completude' => 100,
        'documents_manquants' => [],
        'date_creation' => '2024-01-25',
        'date_derniere_maj' => '2024-01-30',
        'validateur' => null,
        'commentaire' => null,
        'statut_paiement' => 'paye',
        'can_validate' => true
    ],
    [
        'id' => 'DOS-2024-006',
        'id_hash' => 'pqr678',
        'etudiant' => [
            'nom' => 'MBAYE',
            'prenom' => 'Ousmane',
            'matricule' => 'M2-2024-089',
            'email' => 'ousmane.mbaye@ucad.edu.sn',
            'telephone' => '+221 76 678 90 12',
            'photo_url' => null
        ],
        'etat_workflow' => 'valide',
        'etat_label' => 'Dossier validé',
        'documents_deposes' => 10,
        'documents_requis' => 10,
        'completude' => 100,
        'documents_manquants' => [],
        'date_creation' => '2024-01-12',
        'date_derniere_maj' => '2024-01-26',
        'validateur' => 'M. DIOP - Chef Secrétariat',
        'commentaire' => 'Dossier excellent, tous documents conformes',
        'statut_paiement' => 'paye',
        'can_validate' => false
    ],
    [
        'id' => 'DOS-2024-007',
        'id_hash' => 'stu901',
        'etudiant' => [
            'nom' => 'CISSE',
            'prenom' => 'Aminata',
            'matricule' => 'M2-2024-103',
            'email' => 'aminata.cisse@ucad.edu.sn',
            'telephone' => '+221 77 789 01 23',
            'photo_url' => null
        ],
        'etat_workflow' => 'documents_incomplets',
        'etat_label' => 'Documents incomplets',
        'documents_deposes' => 8,
        'documents_requis' => 10,
        'completude' => 80,
        'documents_manquants' => ['Certificat médical', 'Attestation d\'assurance'],
        'date_creation' => '2024-01-28',
        'date_derniere_maj' => '2024-01-30',
        'validateur' => null,
        'commentaire' => null,
        'statut_paiement' => 'paye',
        'can_validate' => false
    ]
];

// Filtres disponibles
$filtres = [
    'etat' => [
        ['value' => '', 'label' => 'Tous les états'],
        ['value' => 'en_attente_validation', 'label' => 'En attente validation'],
        ['value' => 'documents_incomplets', 'label' => 'Documents incomplets'],
        ['value' => 'valide', 'label' => 'Validés'],
        ['value' => 'rejete', 'label' => 'Rejetés']
    ],
    'completude' => [
        ['value' => '', 'label' => 'Toute complétude'],
        ['value' => '100', 'label' => '100% complets'],
        ['value' => '75-99', 'label' => '75-99%'],
        ['value' => '50-74', 'label' => '50-74%'],
        ['value' => '0-49', 'label' => 'Moins de 50%']
    ],
    'periode' => [
        ['value' => '', 'label' => 'Toute période'],
        ['value' => 'aujourd_hui', 'label' => "Aujourd'hui"],
        ['value' => '7_jours', 'label' => '7 derniers jours'],
        ['value' => '30_jours', 'label' => '30 derniers jours'],
        ['value' => 'annee', 'label' => 'Cette année']
    ]
];

// Helper pour badge statut
function getEtatBadge(string $etat): string
{
    $badges = [
        'en_attente_validation' => '<span class="badge badge-warning">En attente validation</span>',
        'documents_incomplets' => '<span class="badge badge-danger">Documents incomplets</span>',
        'valide' => '<span class="badge badge-success">Validé</span>',
        'rejete' => '<span class="badge badge-danger">Rejeté</span>'
    ];
    return $badges[$etat] ?? '<span class="badge badge-secondary">Inconnu</span>';
}

// Helper pour badge paiement
function getPaiementBadge(string $statut): string
{
    $badges = [
        'paye' => '<span class="badge badge-success badge-sm">Payé</span>',
        'attente' => '<span class="badge badge-warning badge-sm">En attente</span>',
        'exonere' => '<span class="badge badge-info badge-sm">Exonéré</span>'
    ];
    return $badges[$statut] ?? '<span class="badge badge-secondary badge-sm">-</span>';
}

// Helper pour barre de progression
function getProgressBar(int $completude): string
{
    $color = $completude === 100 ? 'success' : ($completude >= 70 ? 'warning' : 'danger');
    return '<div class="progress"><div class="progress-bar progress-bar-' . $color . '" style="width: ' . $completude . '%">' . $completude . '%</div></div>';
}

// Début capture contenu
ob_start();
?>

<!-- Page Container -->
<div class="page-container">
    
    <!-- Page Header avec Stats -->
    <div class="page-header-extended">
        <div class="stats-grid">
            <div class="stat-card stat-card-primary">
                <div class="stat-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?= $stats['total_dossiers'] ?></div>
                    <div class="stat-card-label">Total dossiers</div>
                    <div class="stat-card-extra">+<?= $stats['nouveaux_aujourd_hui'] ?> aujourd'hui</div>
                </div>
            </div>

            <div class="stat-card stat-card-success">
                <div class="stat-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?= $stats['complets'] ?></div>
                    <div class="stat-card-label">Dossiers complets</div>
                    <div class="stat-card-extra"><?= $stats['taux_completude'] ?>% de complétude</div>
                </div>
            </div>

            <div class="stat-card stat-card-danger">
                <div class="stat-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?= $stats['incomplets'] ?></div>
                    <div class="stat-card-label">Dossiers incomplets</div>
                </div>
            </div>

            <div class="stat-card stat-card-warning">
                <div class="stat-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value"><?= $stats['en_attente_validation'] ?></div>
                    <div class="stat-card-label">En attente validation</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Actions -->
    <div class="page-toolbar">
        <div class="page-toolbar-left">
            <div class="filter-group">
                <!-- Filtre état -->
                <select class="form-select" id="filter-etat" aria-label="Filtrer par état">
                    <?php foreach ($filtres['etat'] as $option): ?>
                        <option value="<?= htmlspecialchars($option['value'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Filtre complétude -->
                <select class="form-select" id="filter-completude" aria-label="Filtrer par complétude">
                    <?php foreach ($filtres['completude'] as $option): ?>
                        <option value="<?= htmlspecialchars($option['value'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Filtre période -->
                <select class="form-select" id="filter-periode" aria-label="Filtrer par période">
                    <?php foreach ($filtres['periode'] as $option): ?>
                        <option value="<?= htmlspecialchars($option['value'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="button" class="btn btn-secondary btn-icon" title="Réinitialiser filtres">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1,4 1,10 7,10"/>
                        <polyline points="23,20 23,14 17,14"/>
                        <path d="M20.49 9A9 9 0 005.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 013.51 15"/>
                    </svg>
                </button>
            </div>

            <div class="search-box">
                <svg class="search-box-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="search" 
                       class="search-box-input" 
                       placeholder="Rechercher par nom, matricule, email..."
                       aria-label="Rechercher">
            </div>
        </div>

        <div class="page-toolbar-right">
            <button type="button" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7,10 12,15 17,10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Exporter
            </button>

            <button type="button" class="btn btn-primary" id="btn-validation-masse">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                Validation en masse
            </button>
        </div>
    </div>

    <!-- Tableau des dossiers -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all" aria-label="Sélectionner tout">
                        </th>
                        <th>Référence</th>
                        <th>Étudiant</th>
                        <th>État</th>
                        <th>Complétude</th>
                        <th>Paiement</th>
                        <th>Date création</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dossiers as $dossier): ?>
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       class="select-dossier" 
                                       value="<?= htmlspecialchars($dossier['id_hash'], ENT_QUOTES, 'UTF-8') ?>"
                                       aria-label="Sélectionner dossier">
                            </td>
                            <td>
                                <div class="text-semibold"><?= htmlspecialchars($dossier['id'], ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-cell-avatar">
                                        <?= htmlspecialchars(strtoupper(substr($dossier['etudiant']['prenom'], 0, 1) . substr($dossier['etudiant']['nom'], 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="user-cell-info">
                                        <div class="user-cell-name">
                                            <?= htmlspecialchars($dossier['etudiant']['prenom'] . ' ' . $dossier['etudiant']['nom'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="user-cell-meta">
                                            <?= htmlspecialchars($dossier['etudiant']['matricule'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="user-cell-meta text-sm">
                                            <?= htmlspecialchars($dossier['etudiant']['email'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?= getEtatBadge($dossier['etat_workflow']) ?></td>
                            <td>
                                <div class="mb-1">
                                    <?= getProgressBar($dossier['completude']) ?>
                                </div>
                                <div class="text-sm text-muted">
                                    <?= $dossier['documents_deposes'] ?>/<?= $dossier['documents_requis'] ?> documents
                                </div>
                                <?php if (count($dossier['documents_manquants']) > 0): ?>
                                    <div class="text-sm text-danger mt-1">
                                        <strong>Manquants:</strong>
                                        <?= htmlspecialchars(implode(', ', array_slice($dossier['documents_manquants'], 0, 2)), ENT_QUOTES, 'UTF-8') ?>
                                        <?php if (count($dossier['documents_manquants']) > 2): ?>
                                            <span title="<?= htmlspecialchars(implode(', ', $dossier['documents_manquants']), ENT_QUOTES, 'UTF-8') ?>">
                                                (+<?= count($dossier['documents_manquants']) - 2 ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= getPaiementBadge($dossier['statut_paiement']) ?></td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($dossier['date_creation'])) ?></div>
                                <div class="text-muted text-sm">
                                    MAJ: <?= date('d/m/Y', strtotime($dossier['date_derniere_maj'])) ?>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-icon btn-ghost" 
                                            title="Voir détails"
                                            data-action="voir"
                                            data-id="<?= htmlspecialchars($dossier['id_hash'], ENT_QUOTES, 'UTF-8') ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>

                                    <?php if ($dossier['can_validate']): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-icon btn-success" 
                                                title="Valider le dossier"
                                                data-action="valider"
                                                data-id="<?= htmlspecialchars($dossier['id_hash'], ENT_QUOTES, 'UTF-8') ?>">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="20,6 9,17 4,12"/>
                                            </svg>
                                        </button>
                                    <?php endif; ?>

                                    <?php if (count($dossier['documents_manquants']) > 0): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-icon btn-warning" 
                                                title="Demander compléments"
                                                data-action="complements"
                                                data-id="<?= htmlspecialchars($dossier['id_hash'], ENT_QUOTES, 'UTF-8') ?>">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                            </svg>
                                        </button>
                                    <?php endif; ?>

                                    <button type="button" 
                                            class="btn btn-sm btn-icon btn-ghost" 
                                            title="Historique"
                                            data-action="historique"
                                            data-id="<?= htmlspecialchars($dossier['id_hash'], ENT_QUOTES, 'UTF-8') ?>">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12,6 12,12 16,14"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            <div class="pagination-info">
                Affichage de <strong>1-7</strong> sur <strong><?= $stats['total_dossiers'] ?></strong> dossiers
            </div>
            <nav class="pagination" aria-label="Navigation pagination">
                <button type="button" class="pagination-btn" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15,18 9,12 15,6"/>
                    </svg>
                </button>
                <button type="button" class="pagination-btn is-active">1</button>
                <button type="button" class="pagination-btn">2</button>
                <button type="button" class="pagination-btn">3</button>
                <button type="button" class="pagination-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9,18 15,12 9,6"/>
                    </svg>
                </button>
            </nav>
        </div>
    </div>

    <!-- Aide contextuelle -->
    <div class="help-box">
        <div class="help-box-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="help-box-content">
            <div class="help-box-title">Gestion des dossiers étudiants</div>
            <div class="help-box-text">
                Vérifiez la complétude et la conformité de chaque document avant validation.
                Pour les dossiers incomplets, utilisez le bouton "Demander compléments" pour notifier l'étudiant.
                La validation d'un dossier permet de passer à l'étape suivante du workflow.
            </div>
        </div>
    </div>

</div>

<!-- Scripts spécifiques page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélection multiple
    const selectAll = document.getElementById('select-all');
    const selectDossiers = document.querySelectorAll('.select-dossier');
    
    selectAll?.addEventListener('change', function() {
        selectDossiers.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Filtres
    const filterEtat = document.getElementById('filter-etat');
    const filterCompletude = document.getElementById('filter-completude');
    const filterPeriode = document.getElementById('filter-periode');
    
    [filterEtat, filterCompletude, filterPeriode].forEach(filter => {
        filter?.addEventListener('change', function() {
            console.log('Filtre changé:', this.value);
            // TODO: Implémenter filtrage AJAX
        });
    });

    // Validation en masse
    const btnValidationMasse = document.getElementById('btn-validation-masse');
    btnValidationMasse?.addEventListener('click', function() {
        const selected = Array.from(selectDossiers)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un dossier');
            return;
        }
        
        if (confirm(`Valider ${selected.length} dossier(s) ?`)) {
            console.log('Validation en masse:', selected);
            // TODO: Appel AJAX pour validation masse
        }
    });

    // Actions individuelles
    document.querySelectorAll('[data-action="voir"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            console.log('Voir détails dossier:', id);
            window.location.href = '/modules/secretariat/dossiers/' + id;
        });
    });

    document.querySelectorAll('[data-action="valider"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Confirmer la validation de ce dossier ?')) {
                console.log('Valider dossier:', id);
                // TODO: Appel AJAX pour valider
            }
        });
    });

    document.querySelectorAll('[data-action="complements"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            console.log('Demander compléments:', id);
            // TODO: Ouvrir modal demande compléments
        });
    });

    document.querySelectorAll('[data-action="historique"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            console.log('Afficher historique:', id);
            // TODO: Ouvrir modal historique
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../../layouts/app.php';
