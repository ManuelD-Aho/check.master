<?php
/**
 * Template HTML pour la génération du bulletin de notes en PDF.
 * Ce fichier est utilisé comme référence pour le contenu HTML
 * qui sera injecté dans TCPDF par BulletinGeneratorService.
 *
 * Variables disponibles :
 *   $etudiant - Informations de l'étudiant
 *   $inscription - Informations d'inscription
 *   $notes - Tableau des notes
 *   $semestre - Informations du semestre
 *   $moyenne - Moyenne générale calculée
 *   $decision - Décision du conseil
 */
?>
<h2 style="text-align:center;">Bulletin de notes</h2>

<table cellpadding="4" cellspacing="0" border="1" width="100%">
    <tr>
        <td width="35%"><strong>Étudiant</strong></td>
        <td width="65%"><?php echo htmlspecialchars($etudiant['nom'] ?? ''); ?></td>
    </tr>
    <tr>
        <td width="35%"><strong>Matricule</strong></td>
        <td width="65%"><?php echo htmlspecialchars($etudiant['matricule'] ?? ''); ?></td>
    </tr>
    <tr>
        <td width="35%"><strong>Filière</strong></td>
        <td width="65%"><?php echo htmlspecialchars($inscription['filiere'] ?? ''); ?></td>
    </tr>
    <tr>
        <td width="35%"><strong>Niveau</strong></td>
        <td width="65%"><?php echo htmlspecialchars($inscription['niveau'] ?? ''); ?></td>
    </tr>
    <tr>
        <td width="35%"><strong>Année académique</strong></td>
        <td width="65%"><?php echo htmlspecialchars($inscription['annee'] ?? ''); ?></td>
    </tr>
    <tr>
        <td width="35%"><strong>Semestre</strong></td>
        <td width="65%"><?php echo htmlspecialchars($semestre['libelle'] ?? ''); ?></td>
    </tr>
</table>

<br/>
<h3>Détail des notes</h3>

<table cellpadding="4" cellspacing="0" border="1" width="100%">
    <tr>
        <th width="50%"><strong>Matière</strong></th>
        <th width="25%"><strong>Note</strong></th>
        <th width="25%"><strong>Coefficient</strong></th>
    </tr>
    <?php if (!empty($notes)): ?>
        <?php foreach ($notes as $note): ?>
            <tr>
                <td><?php echo htmlspecialchars($note['matiere'] ?? '-'); ?></td>
                <td style="text-align:center;"><?php echo htmlspecialchars((string) ($note['note'] ?? '-')); ?></td>
                <td style="text-align:center;"><?php echo htmlspecialchars((string) ($note['coefficient'] ?? '-')); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3" style="text-align:center;">Aucune note disponible</td>
        </tr>
    <?php endif; ?>
</table>

<br/>
<table cellpadding="4" cellspacing="0" border="1" width="100%">
    <tr>
        <td width="35%"><strong>Moyenne générale</strong></td>
        <td width="65%"><?php echo htmlspecialchars((string) ($moyenne ?? '0')); ?> / 20</td>
    </tr>
</table>

<?php if (!empty($decision)): ?>
<br/>
<table cellpadding="4" cellspacing="0" border="1" width="100%">
    <tr>
        <td width="35%"><strong>Décision</strong></td>
        <td width="65%"><?php echo htmlspecialchars($decision); ?></td>
    </tr>
</table>
<?php endif; ?>
