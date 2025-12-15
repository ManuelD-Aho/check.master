<div class="row">
    <!-- TYPOGRAPHY -->
    <div class="col-12 col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Typographie</h3>
            </div>
            <div class="card-body">
                <h1>H1 Heading</h1>
                <h2>H2 Heading</h2>
                <h3>H3 Heading</h3>
                <h4>H4 Heading</h4>
                <h5>H5 Heading</h5>
                <h6>H6 Heading</h6>
                <hr>
                <p>Ceci est un paragraphe standard avec du <strong>texte en gras</strong>, de l'<em>italique</em>, et un <a href="#">lien hypertexte</a>.</p>
                <p class="text-sm">Texte 'small' pour les légendes ou métadonnées.</p>
                <p class="text-xs">Texte 'extra small' pour les détails fins.</p>
                <blockquote>
                    Ceci est une citation en blockquote pour mettre en avant du contenu important.
                </blockquote>
            </div>
        </div>
    </div>

    <!-- BUTTONS -->
    <div class="col-12 col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Boutons</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h4 class="text-sm font-semibold mb-2">Variantes</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php include_component('button', ['text' => 'Primary', 'variant' => 'primary']); ?>
                        <?php include_component('button', ['text' => 'Secondary', 'variant' => 'secondary']); ?>
                        <?php include_component('button', ['text' => 'Accent', 'variant' => 'accent']); ?>
                        <?php include_component('button', ['text' => 'Success', 'variant' => 'success']); ?>
                        <?php include_component('button', ['text' => 'Danger', 'variant' => 'danger']); ?>
                        <?php include_component('button', ['text' => 'Ghost', 'variant' => 'ghost']); ?>
                        <?php include_component('button', ['text' => 'Link', 'variant' => 'link']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <h4 class="text-sm font-semibold mb-2">Tailles</h4>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php include_component('button', ['text' => 'Small', 'variant' => 'primary', 'size' => 'sm']); ?>
                        <?php include_component('button', ['text' => 'Medium', 'variant' => 'primary', 'size' => 'md']); ?>
                        <?php include_component('button', ['text' => 'Large', 'variant' => 'primary', 'size' => 'lg']); ?>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-2">États</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php include_component('button', ['text' => 'Loading', 'variant' => 'primary', 'loading' => true]); ?>
                        <?php include_component('button', ['text' => 'Disabled', 'variant' => 'primary', 'disabled' => true]); ?>
                        <?php include_component('button', ['text' => 'Icon Left', 'variant' => 'secondary', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>', 'iconPosition' => 'left']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- FORMS -->
    <div class="col-12 col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Formulaires</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label class="form-label" for="input-text">Input Texte</label>
                        <input type="text" id="input-text" class="form-input" placeholder="Entrez du texte...">
                        <div class="form-text">Texte d'aide pour le champ.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="input-select">Sélection</label>
                        <select id="input-select" class="form-select">
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Checkboxes & Radios</label>
                        <div class="d-flex gap-4">
                            <label class="form-checkbox">
                                <input type="checkbox" checked>
                                <span>Checkbox activée</span>
                            </label>
                            <label class="form-radio">
                                <input type="radio" name="radio-demo" checked>
                                <span>Radio 1</span>
                            </label>
                            <label class="form-radio">
                                <input type="radio" name="radio-demo">
                                <span>Radio 2</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Toggle Switch</label>
                        <label class="form-toggle">
                            <input type="checkbox">
                            <span class="form-toggle-slider"></span>
                            <span class="form-toggle-label">Activer l'option</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="input-error" class="text-danger">Input avec Erreur</label>
                        <input type="text" id="input-error" class="form-input is-invalid" value="Valeur invalide">
                        <div class="form-error">Ce champ est obligatoire.</div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BADGES & ALERTS -->
    <div class="col-12 col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Badges & Alertes</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h4 class="text-sm font-semibold mb-2">Badges</h4>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php include_component('badge', ['text' => 'Default', 'variant' => 'default']); ?>
                        <?php include_component('badge', ['text' => 'Primary', 'variant' => 'primary']); ?>
                        <?php include_component('badge', ['text' => 'Success', 'variant' => 'success']); ?>
                        <?php include_component('badge', ['text' => 'Warning', 'variant' => 'warning']); ?>
                        <?php include_component('badge', ['text' => 'Danger', 'variant' => 'danger']); ?>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php include_component('badge', ['text' => 'Pill', 'pill' => true, 'variant' => 'primary']); ?>
                        <?php include_component('badge', ['text' => 'Dot', 'dot' => true, 'variant' => 'success']); ?>
                        <?php include_component('badge', ['text' => 'Workflow Valide', 'state' => 'valide']); ?>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold mb-2">Alertes</h4>
                    <div class="alert alert-info mb-2">
                        <div class="alert-content">Alerte d'information simple.</div>
                    </div>
                    <div class="alert alert-success mb-2">
                        <div class="alert-content">Opération réussie avec succès !</div>
                    </div>
                    <div class="alert alert-warning mb-2">
                        <div class="alert-content">Attention, une action est requise.</div>
                    </div>
                    <button class="btn btn-sm btn-secondary mt-2" onclick="toast.info('Ceci est un toast de test', 'Notification')">
                        Tester Toast JS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WORKFLOW & MODALS -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Workflow & Interactions</h3>
            </div>
            <div class="card-body">
                <!-- Timeline -->
                <div class="workflow-horizontal mb-5">
                    <div class="workflow-step is-completed">
                        <div class="workflow-step-indicator"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12" />
                            </svg></div>
                        <div class="workflow-step-content">
                            <div class="workflow-step-title">Dépôt</div>
                            <div class="workflow-step-meta">12/12/2025</div>
                        </div>
                    </div>
                    <div class="workflow-step is-current">
                        <div class="workflow-step-indicator"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg></div>
                        <div class="workflow-step-content">
                            <div class="workflow-step-title">Validation</div>
                            <div class="workflow-step-meta">En cours</div>
                        </div>
                    </div>
                    <div class="workflow-step is-pending">
                        <div class="workflow-step-indicator"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                            </svg></div>
                        <div class="workflow-step-content">
                            <div class="workflow-step-title">Soutenance</div>
                            <div class="workflow-step-meta">À venir</div>
                        </div>
                    </div>
                </div>

                <!-- Modals -->
                <div class="d-flex gap-3 justify-content-center">
                    <button class="btn btn-primary" data-modal-open="demo-modal">Ouvrir Modale Simple</button>
                    <button class="btn btn-danger" onclick="confirmDialog({title: 'Suppression', message: 'Voulez-vous vraiment supprimer cet élément ?', type: 'danger'}).then(res => res && toast.success('Supprimé !'))">
                        Tester Confirm Dialog
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RESPONSIVE TABLES -->
<div class="row">
    <div class="col-12">
        <?php include_component('card', [
            'title' => 'Tableau de Données',
            'subtitle' => 'Exemple de tableau avec pagination et actions',
            'variant' => 'default',
            'padding' => false,
            'body' => '
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="w-10"><input type="checkbox" class="form-checkbox"></th>
                                <th>Étudiant</th>
                                <th>Matricule</th>
                                <th>Sujet</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="form-checkbox"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-primary-100 text-primary-700">AK</div>
                                        <div class="font-medium">Anon KOFFI</div>
                                    </div>
                                </td>
                                <td>CI0123456789</td>
                                <td>Système de gestion académique</td>
                                <td>15/12/2025</td>
                                <td><span class="badge badge-state-valide">Validé</span></td>
                                <td class="text-right">
                                    <button class="btn btn-sm btn-ghost btn-icon" title="Éditer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-checkbox"></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-accent-100 text-accent-700">MY</div>
                                        <div class="font-medium">Marie YAO</div>
                                    </div>
                                </td>
                                <td>CI9876543210</td>
                                <td>Analyse de données climatiques</td>
                                <td>14/12/2025</td>
                                <td><span class="badge badge-state-commission">En examen</span></td>
                                <td class="text-right">
                                    <button class="btn btn-sm btn-ghost btn-icon" title="Éditer">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-sm text-gray-500">Affichage de 1 à 2 sur 12 résultats</div>
                    <div class="pagination">
                        <button class="pagination-item is-disabled"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
                        <button class="pagination-item is-active">1</button>
                        <button class="pagination-item">2</button>
                        <button class="pagination-item">3</button>
                        <button class="pagination-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
                    </div>
                </div>
            '
        ]); ?>
    </div>
</div>

<!-- DEMO MODAL -->
<?php include_component('modal', [
    'id' => 'demo-modal',
    'title' => 'Exemple de Modale',
    'body' => '<p>Ceci est le contenu d\'une modale standard. Elle peut contenir du texte, des formulaires ou tout autre élément HTML.</p>',
    'footer' => '
        <button class="btn btn-ghost" data-modal-close>Fermer</button>
        <button class="btn btn-primary" onclick="toast.success(\'Action confirmée !\'); closeModal(\'demo-modal\')">Action</button>
    '
]); ?>