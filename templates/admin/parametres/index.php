<?php
$title = 'Paramètres';
$layout = 'admin';
?>

<div class="page-header">
    <div class="header-left">
        <h1>Paramètres</h1>
        <p class="subtitle">Configuration générale du système</p>
    </div>
</div>

<?php if (isset($flashes) && !empty($flashes)): ?>
    <div class="alerts">
        <?php foreach ($flashes as $flashType => $flashList): ?>
            <?php foreach ($flashList as $flashMsg): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flashType); ?>">
                    <span><?php echo htmlspecialchars($flashMsg); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="settings-grid">
    <div class="settings-card">
        <h2>Application</h2>
        <p>Paramètres généraux de l'application</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/application" class="btn btn-secondary">Configurer</a>
    </div>
    <div class="settings-card">
        <h2>Années académiques</h2>
        <p>Gérer les années académiques</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/annees" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Filières</h2>
        <p>Programmes de formation</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/filieres" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Niveaux d'étude</h2>
        <p>Licence, Master, Doctorat...</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/niveaux" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Grades</h2>
        <p>Grades académiques</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/grades" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Fonctions</h2>
        <p>Types de fonctions</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/fonctions" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Salles</h2>
        <p>Salles de soutenance</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/salles" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Rôles jury</h2>
        <p>Rôles dans les jurys</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/roles-jury" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Critères</h2>
        <p>Critères d'évaluation</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/criteres" class="btn btn-secondary">Gérer</a>
    </div>
    <div class="settings-card">
        <h2>Entreprises</h2>
        <p>Entreprises partenaires</p>
        <a href="<?php echo BASE_URL; ?>/admin/parametres/entreprises" class="btn btn-secondary">Gérer</a>
    </div>
</div>

<?php if (isset($settings) && is_array($settings)): ?>
<div class="detail-card">
    <div class="detail-section">
        <h2>Résumé de la configuration</h2>
        <div class="detail-grid">
            <?php foreach ($settings as $paramKey => $paramVal): ?>
                <div class="detail-item">
                    <span class="detail-label"><?php echo htmlspecialchars($paramKey); ?></span>
                    <span class="detail-value"><?php echo htmlspecialchars($paramVal); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
