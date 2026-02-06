<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'étudiant</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-layout">

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h1>Check Master</h1>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/admin/dashboard">Tableau de bord</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/utilisateurs">Utilisateurs</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/etudiants" class="active">Étudiants</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/enseignants">Enseignants</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/candidatures">Candidatures</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/rapports">Rapports</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/soutenances">Soutenances</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <div class="header-left">
                <h1>Modifier l'étudiant</h1>
                <p class="subtitle">
                    <?php if (isset($etudiant)): ?>
                        <?php echo htmlspecialchars($etudiant->getMatriculeEtudiant()); ?> -
                        <?php echo htmlspecialchars($etudiant->getNomEtudiant()); ?>
                        <?php echo htmlspecialchars($etudiant->getPrenomEtudiant()); ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="header-right">
                <a href="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo htmlspecialchars($etudiant->getMatriculeEtudiant() ?? ''); ?>" class="btn btn-secondary">← Retour</a>
            </div>
        </div>

        <?php if (isset($flashes) && !empty($flashes)): ?>
            <div class="alerts">
                <?php foreach ($flashes as $type => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                            <span><?php echo htmlspecialchars($message); ?></span>
                            <button class="alert-close">&times;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($etudiant) || $etudiant === null): ?>
            <div class="alert alert-error">
                <span>Étudiant non trouvé</span>
            </div>
        <?php else: ?>

        <div class="form-container">
            <form method="POST" action="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo htmlspecialchars($etudiant->getMatriculeEtudiant()); ?>" class="form-horizontal">
                <input type="hidden" name="_csrf_token" value="<?php echo htmlspecialchars($csrf ?? ''); ?>">

                <div class="form-section">
                    <h2>Informations personnelles</h2>

                    <div class="form-group">
                        <label for="nom">Nom <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" required maxlength="100"
                               value="<?php echo htmlspecialchars($etudiant->getNomEtudiant()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom <span class="required">*</span></label>
                        <input type="text" id="prenom" name="prenom" required maxlength="100"
                               value="<?php echo htmlspecialchars($etudiant->getPrenomEtudiant()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required maxlength="255"
                               value="<?php echo htmlspecialchars($etudiant->getEmailEtudiant()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" maxlength="20"
                               value="<?php echo htmlspecialchars($etudiant->getTelephoneEtudiant() ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_naissance">Date de naissance <span class="required">*</span></label>
                        <input type="date" id="date_naissance" name="date_naissance" required
                               value="<?php echo $etudiant->getDateNaissance()->format('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="lieu_naissance">Lieu de naissance <span class="required">*</span></label>
                        <input type="text" id="lieu_naissance" name="lieu_naissance" required maxlength="100"
                               value="<?php echo htmlspecialchars($etudiant->getLieuNaissance()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="nationalite">Nationalité</label>
                        <input type="text" id="nationalite" name="nationalite" maxlength="50"
                               value="<?php echo htmlspecialchars($etudiant->getNationalite() ?? 'Ivoirienne'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($etudiant->getAdresse() ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Informations académiques</h2>

                    <div class="form-group">
                        <label>Matricule</label>
                        <input type="text" value="<?php echo htmlspecialchars($etudiant->getMatriculeEtudiant()); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="promotion">Promotion <span class="required">*</span></label>
                        <input type="text" id="promotion" name="promotion" required maxlength="20"
                               value="<?php echo htmlspecialchars($etudiant->getPromotion()); ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="<?php echo BASE_URL; ?>/admin/etudiants/<?php echo htmlspecialchars($etudiant->getMatriculeEtudiant()); ?>" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>

        <?php endif; ?>
    </main>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
