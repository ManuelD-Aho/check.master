<?php if (!isset($utilisateur) || $utilisateur === null): ?>
<div class="page-header">
    <div class="header-left">
        <h1>Utilisateur introuvable</h1>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs" class="btn btn-secondary">← Revenir à la liste</a>
    </div>
</div>
<div class="alerts">
    <div class="alert alert-error">
        <span>Impossible d'afficher les informations : cet utilisateur n'existe pas ou a été supprimé.</span>
    </div>
</div>
<?php else: ?>
<div class="page-header">
    <div class="header-left">
        <h1><?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></h1>
        <p class="subtitle">Fiche détaillée de l'utilisateur</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs/<?php echo (int)($utilisateur['id'] ?? 0); ?>/modifier" class="btn btn-primary">Éditer ce profil</a>
        <a href="<?php echo BASE_URL; ?>/admin/utilisateurs" class="btn btn-secondary">← Revenir à la liste</a>
    </div>
</div>

<div class="detail-container">
    <div class="detail-main">
        <section class="detail-section">
            <h2>Coordonnées et identité</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Nom</label>
                    <p><?php echo htmlspecialchars($utilisateur['nom'] ?? 'Non renseigné'); ?></p>
                </div>
                <div class="info-item">
                    <label>Prénom</label>
                    <p><?php echo htmlspecialchars($utilisateur['prenom'] ?? 'Non renseigné'); ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p>
                        <?php $adresseMail = $utilisateur['email'] ?? ''; ?>
                        <?php if ($adresseMail !== ''): ?>
                            <a href="mailto:<?php echo htmlspecialchars($adresseMail); ?>"><?php echo htmlspecialchars($adresseMail); ?></a>
                        <?php else: ?>
                            Non renseigné
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="detail-section">
            <h2>Rôle et accès</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Groupe</label>
                    <p><?php echo htmlspecialchars($utilisateur['groupe'] ?? 'Aucun groupe attribué'); ?></p>
                </div>
                <div class="info-item">
                    <label>Type</label>
                    <p><?php echo htmlspecialchars($utilisateur['type'] ?? 'Non défini'); ?></p>
                </div>
                <div class="info-item">
                    <label>Statut</label>
                    <span class="status-badge status-<?php echo htmlspecialchars($utilisateur['statut'] ?? 'inactif'); ?>">
                        <?php echo htmlspecialchars(ucfirst($utilisateur['statut'] ?? 'Inactif')); ?>
                    </span>
                </div>
                <div class="info-item">
                    <label>Date de création</label>
                    <p><?php echo !empty($utilisateur['date_creation']) ? htmlspecialchars(date('d/m/Y à H:i', strtotime($utilisateur['date_creation']))) : 'Inconnue'; ?></p>
                </div>
            </div>
        </section>
    </div>
</div>
<?php endif; ?>
