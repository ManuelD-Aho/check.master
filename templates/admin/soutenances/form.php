<div class="page-header">
    <div class="header-left">
        <h1><?php echo !empty($soutenance['id']) ? 'Modifier la soutenance' : 'Programmer une soutenance'; ?></h1>
        <p class="subtitle">Formulaire de programmation</p>
    </div>
    <div class="header-right">
        <a href="<?php echo BASE_URL; ?>/admin/soutenances" class="btn btn-secondary">Annuler</a>
    </div>
</div>

<?php if (isset($flashMessages) && !empty($flashMessages)): ?>
    <div class="alerts">
        <?php foreach ($flashMessages as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
                    <span><?php echo htmlspecialchars($message); ?></span>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="post" action="<?php echo BASE_URL; ?>/admin/soutenances/<?php echo !empty($soutenance['id']) ? (int)$soutenance['id'] . '/update' : 'store'; ?>" class="form-main">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

        <fieldset class="form-section">
            <legend>Informations de l'étudiant</legend>

            <div class="form-group">
                <label for="etudiant_id">Étudiant *</label>
                <select id="etudiant_id" name="etudiant_id" required class="form-control">
                    <option value="">Sélectionner un étudiant</option>
                    <?php if (!empty($etudiants)): ?>
                        <?php foreach ($etudiants as $etudiant): ?>
                            <option value="<?php echo (int)$etudiant['id']; ?>" <?php echo ($soutenance['etudiant_id'] ?? '') == $etudiant['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="sujet_pfe">Sujet PFE</label>
                    <input type="text" id="sujet_pfe" name="sujet_pfe" value="<?php echo htmlspecialchars($soutenance['sujet_pfe'] ?? ''); ?>" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="annee_academique">Année académique</label>
                    <input type="text" id="annee_academique" name="annee_academique" value="<?php echo htmlspecialchars($soutenance['annee_academique'] ?? ''); ?>" class="form-control" readonly>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Programmation de la soutenance</legend>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_soutenance">Date et heure *</label>
                    <input type="datetime-local" id="date_soutenance" name="date_soutenance" value="<?php echo !empty($soutenance['date_soutenance']) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($soutenance['date_soutenance']))) : ''; ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="duree">Durée (minutes) *</label>
                    <input type="number" id="duree" name="duree" value="<?php echo htmlspecialchars($soutenance['duree'] ?? '60'); ?>" min="30" max="300" step="15" required class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="lieu">Lieu *</label>
                    <input type="text" id="lieu" name="lieu" value="<?php echo htmlspecialchars($soutenance['lieu'] ?? ''); ?>" placeholder="Ex: Salle 101, Amphi A..." required class="form-control">
                </div>
                <div class="form-group">
                    <label for="type_soutenance">Type de soutenance *</label>
                    <select id="type_soutenance" name="type_soutenance" required class="form-control">
                        <option value="">Sélectionner un type</option>
                        <option value="pfe" <?php echo ($soutenance['type_soutenance'] ?? '') === 'pfe' ? 'selected' : ''; ?>>PFE</option>
                        <option value="stage" <?php echo ($soutenance['type_soutenance'] ?? '') === 'stage' ? 'selected' : ''; ?>>Stage</option>
                        <option value="projet" <?php echo ($soutenance['type_soutenance'] ?? '') === 'projet' ? 'selected' : ''; ?>>Projet</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="observations">Observations</label>
                <textarea id="observations" name="observations" class="form-control" rows="4"><?php echo htmlspecialchars($soutenance['observations'] ?? ''); ?></textarea>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Commission</legend>

            <div class="form-group">
                <label>Membres de la commission *</label>
                <div class="checkbox-group">
                    <?php if (!empty($membres)): ?>
                        <?php foreach ($membres as $membre): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" id="membre_<?php echo (int)$membre['id']; ?>" name="membres[]" value="<?php echo (int)$membre['id']; ?>" class="form-check" <?php echo in_array($membre['id'], $soutenance['membres'] ?? []) ? 'checked' : ''; ?>>
                                <label for="membre_<?php echo (int)$membre['id']; ?>">
                                    <?php echo htmlspecialchars($membre['nom'] . ' ' . $membre['prenom']); ?>
                                    <span class="role-label">(<?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $membre['role'] ?? 'Examinateur'))); ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="form-hint">Aucun membre de commission disponible</p>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Informations additionnelles</legend>

            <div class="form-row">
                <div class="form-group">
                    <label for="statut">Statut *</label>
                    <select id="statut" name="statut" required class="form-control">
                        <option value="programmee" <?php echo ($soutenance['statut'] ?? 'programmee') === 'programmee' ? 'selected' : ''; ?>>Programmée</option>
                        <option value="en_attente" <?php echo ($soutenance['statut'] ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="completee" <?php echo ($soutenance['statut'] ?? '') === 'completee' ? 'selected' : ''; ?>>Complétée</option>
                        <option value="reportee" <?php echo ($soutenance['statut'] ?? '') === 'reportee' ? 'selected' : ''; ?>>Reportée</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="note_finale">Note finale</label>
                    <input type="number" id="note_finale" name="note_finale" value="<?php echo htmlspecialchars($soutenance['note_finale'] ?? ''); ?>" min="0" max="20" step="0.5" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="remarques">Remarques du jury</label>
                <textarea id="remarques" name="remarques" class="form-control" rows="4"><?php echo htmlspecialchars($soutenance['remarques'] ?? ''); ?></textarea>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo !empty($soutenance['id']) ? 'Mettre à jour' : 'Programmer la soutenance'; ?>
            </button>
            <a href="<?php echo BASE_URL; ?>/admin/soutenances" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
document.getElementById('etudiant_id').addEventListener('change', function() {
    const etudiantId = this.value;
    if (etudiantId) {
        fetch('<?php echo BASE_URL; ?>/api/etudiants/' + etudiantId + '/details')
            .then(response => response.json())
            .then(data => {
                document.getElementById('sujet_pfe').value = data.sujet_pfe || '';
                document.getElementById('annee_academique').value = data.annee_academique || '';
            });
    }
});
</script>
