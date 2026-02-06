<div class="page-header">
    <div class="header-left">
        <h1><?php echo !empty($user) ? 'Modifier l\'utilisateur' : 'Créer un nouvel utilisateur'; ?></h1>
        <p class="subtitle"><?php echo !empty($user) ? htmlspecialchars($user['nom'] . ' ' . $user['prenom']) : 'Ajouter un nouvel utilisateur au système'; ?></p>
    </div>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alerts">
        <div class="alert alert-danger">
            <strong>Erreurs de validation :</strong>
            <ul>
                <?php foreach ($errors as $field => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <li><?php echo htmlspecialchars($message); ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="post" class="form-user" action="<?php echo BASE_URL; ?>/admin/users/<?php echo !empty($user) ? (int)$user['id'] . '/update' : 'store'; ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="nom">Nom <span class="required">*</span></label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom <span class="required">*</span></label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Rôle <span class="required">*</span></label>
                <select id="role" name="role" required>
                    <option value="">Sélectionner un rôle</option>
                    <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="encadreur" <?php echo ($user['role'] ?? '') === 'encadreur' ? 'selected' : ''; ?>>Encadreur</option>
                    <option value="jury" <?php echo ($user['role'] ?? '') === 'jury' ? 'selected' : ''; ?>>Jury</option>
                </select>
            </div>

            <?php if (empty($user)): ?>
                <div class="form-group">
                    <label for="password">Mot de passe <span class="required">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8">
                    <small>Minimum 8 caractères</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe <span class="required">*</span></label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="password">Mot de passe (laisser vide pour ne pas modifier)</label>
                    <input type="password" id="password" name="password" minlength="8">
                    <small>Minimum 8 caractères si fourni</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" minlength="8">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="actif">Status</label>
                <div class="checkbox-group">
                    <input type="hidden" name="actif" value="0">
                    <input type="checkbox" id="actif" name="actif" value="1" <?php echo (!empty($user['actif']) ? 'checked' : ''); ?>>
                    <label for="actif" class="checkbox-label">Utilisateur actif</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo !empty($user) ? 'Mettre à jour' : 'Créer l\'utilisateur'; ?>
            </button>
            <a href="<?php echo BASE_URL; ?>/admin/users" class="btn btn-secondary">Annuler</a>
            <?php if (!empty($user)): ?>
                <button type="button" class="btn btn-danger" onclick="deleteUserForm(<?php echo (int)$user['id']; ?>)">Supprimer</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if (!empty($user)): ?>
    <div class="form-info">
        <h3>Informations supplémentaires</h3>
        <div class="info-grid">
            <div class="info-item">
                <label>ID utilisateur</label>
                <p><?php echo (int)$user['id']; ?></p>
            </div>
            <div class="info-item">
                <label>Date de création</label>
                <p><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['created_at'] ?? 'now'))); ?></p>
            </div>
            <div class="info-item">
                <label>Dernière modification</label>
                <p><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['updated_at'] ?? 'now'))); ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function deleteUserForm(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        fetch('<?php echo BASE_URL; ?>/admin/users/' + userId + '/delete', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }).then(response => {
            if (response.ok) {
                window.location.href = '<?php echo BASE_URL; ?>/admin/users';
            } else {
                alert('Erreur lors de la suppression');
            }
        });
    }
}

document.querySelector('.form-user')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    if (password && password !== passwordConfirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
    }
});
</script>
