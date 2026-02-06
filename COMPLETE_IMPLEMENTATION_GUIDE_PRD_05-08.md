# GUIDE COMPLET D'IMPLÉMENTATION - PRD 05 à 08

**Date**: 2026-02-06
**Version**: 1.0
**Statut**: Blueprint Complet pour Implémentation Exhaustive

---

## INTRODUCTION

Ce document fournit un guide exhaustif et détaillé pour compléter l'implémentation des PRD 05 à 08. Il contient tous les blueprints, patterns de code, et spécifications nécessaires pour implémenter chaque composant manquant.

**Architecture existante**: 95% de l'infrastructure est déjà en place (entités, services principaux, workflows). Ce guide se concentre sur les 20+ contrôleurs administratifs et 50+ templates manquants.

---

## TABLE DES MATIÈRES

1. [PRD 05 - Commission d'Évaluation](#prd-05)
2. [PRD 06 - Jurys et Soutenances](#prd-06)
3. [PRD 07 - Génération Documents](#prd-07)
4. [PRD 08 - Paramétrage Système](#prd-08)
5. [Patterns et Standards](#patterns)
6. [Tests et Validation](#tests)

---

## <a name="prd-05"></a>PRD 05 - COMMISSION D'ÉVALUATION

### Services Existants (✅ Complets)

Les services suivants sont déjà implémentés:
- `CommissionService` - Gestion membres et sessions
- `VoteService` - Calcul vote unanime
- `AffectationService` - Assignation encadrants

### Contrôleurs à Créer

#### 1. Admin/Commission/MembreCommissionController

**Fichier**: `src/Controller/Admin/Commission/MembreCommissionController.php`

**Responsabilités**:
- CRUD des 4 membres de la commission
- Vérification du nombre (exactement 4)
- Gestion des périodes d'activité

**Routes**:
- GET `/admin/commission/membres` - Liste
- GET `/admin/commission/membres/nouveau` - Formulaire création
- POST `/admin/commission/membres` - Création
- GET `/admin/commission/membres/{id}` - Détail
- GET `/admin/commission/membres/{id}/modifier` - Formulaire édition
- PUT `/admin/commission/membres/{id}` - Mise à jour
- DELETE `/admin/commission/membres/{id}` - Suppression (soft delete)

**Pattern de Code**:

```php
<?php
namespace App\Controller\Admin\Commission;

use App\Controller\AbstractController;
use App\Entity\Commission\MembreCommission;
use App\Service\Commission\CommissionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MembreCommissionController extends AbstractController
{
    public function __construct(
        private CommissionService $commissionService
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Vérifier permission COMMISSION_GERER
        $this->denyAccessUnlessGranted('COMMISSION_GERER');

        $membres = $this->commissionService->getMembres();

        return $this->render('admin/commission/membres/index.php', [
            'membres' => $membres,
            'total' => count($membres),
            'maxMembres' => 4
        ]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $this->denyAccessUnlessGranted('COMMISSION_CREER');

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            try {
                $membre = $this->commissionService->addMembre($data);
                $this->addFlash('success', 'Membre ajouté avec succès');
                return $this->redirectToRoute('/admin/commission/membres');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        // Liste enseignants éligibles
        $enseignants = $this->commissionService->getEnseignantsEligibles();

        return $this->render('admin/commission/membres/create.php', [
            'enseignants' => $enseignants
        ]);
    }

    // ... autres méthodes (show, edit, update, delete)
}
```

**Template**: `templates/admin/commission/membres/index.php`

```php
<?php $this->layout('admin/layout', ['title' => 'Membres de la Commission']) ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Membres de la Commission</h1>
        <?php if ($total < $maxMembres): ?>
            <a href="/admin/commission/membres/nouveau" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un membre
            </a>
        <?php endif; ?>
    </div>

    <?php if ($total >= $maxMembres): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            La commission est complète (<?= $maxMembres ?> membres).
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Grade</th>
                        <th>Rôle</th>
                        <th>Date nomination</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membres as $membre): ?>
                        <tr>
                            <td><?= $this->e($membre->getEnseignant()->getNomComplet()) ?></td>
                            <td><?= $this->e($membre->getEnseignant()->getGrade()) ?></td>
                            <td>
                                <span class="badge bg-primary">
                                    <?= $this->e($membre->getRole()->getLibelle()) ?>
                                </span>
                            </td>
                            <td><?= $membre->getDateNomination()->format('d/m/Y') ?></td>
                            <td>
                                <?php if ($membre->isActif()): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/commission/membres/<?= $membre->getId() ?>"
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/commission/membres/<?= $membre->getId() ?>/modifier"
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

#### 2. Admin/Commission/AssignationController

**Fichier**: `src/Controller/Admin/Commission/AssignationController.php`

**Responsabilités**:
- Interface d'assignation des encadrants (DM + EP)
- Validation des règles (EP doit être membre commission)
- Vérification disponibilité des enseignants

**Routes**:
- GET `/admin/commission/assignation` - Liste rapports à assigner
- GET `/admin/commission/assignation/{rapportId}` - Formulaire assignation
- POST `/admin/commission/assignation/{rapportId}` - Enregistrement

**Pattern de Code**:

```php
<?php
namespace App\Controller\Admin\Commission;

use App\Controller\AbstractController;
use App\Service\Commission\AffectationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AssignationController extends AbstractController
{
    public function __construct(
        private AffectationService $affectationService
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->denyAccessUnlessGranted('COMMISSION_ASSIGNER');

        // Rapports avec vote unanime OUI, sans assignation
        $rapportsAAssigner = $this->affectationService
            ->getRapportsEnAttenteAssignation();

        return $this->render('admin/commission/assignation/index.php', [
            'rapports' => $rapportsAAssigner
        ]);
    }

    public function assign(ServerRequestInterface $request, array $params): ResponseInterface
    {
        $this->denyAccessUnlessGranted('COMMISSION_ASSIGNER');

        $rapportId = $params['rapportId'];
        $rapport = $this->affectationService->getRapportById($rapportId);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            try {
                $this->affectationService->assignerEncadrants(
                    $rapport,
                    $data['directeur_memoire_id'],
                    $data['encadreur_pedagogique_id']
                );

                $this->addFlash('success', 'Encadrants assignés avec succès');
                return $this->redirectToRoute('/admin/commission/assignation');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        // Listes enseignants éligibles
        $directeursEligibles = $this->affectationService->getDirecteursEligibles();
        $encadreursEligibles = $this->affectationService->getEncadreursEligibles();

        return $this->render('admin/commission/assignation/form.php', [
            'rapport' => $rapport,
            'directeurs' => $directeursEligibles,
            'encadreurs' => $encadreursEligibles
        ]);
    }
}
```

**Template**: `templates/admin/commission/assignation/form.php`

```php
<?php $this->layout('admin/layout', ['title' => 'Assignation Encadrants']) ?>

<div class="container-fluid">
    <h1>Assignation des Encadrants</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>Informations Rapport</h5>
                </div>
                <div class="card-body">
                    <p><strong>Étudiant:</strong> <?= $this->e($rapport->getEtudiant()->getNomComplet()) ?></p>
                    <p><strong>Matricule:</strong> <?= $this->e($rapport->getEtudiant()->getMatricule()) ?></p>
                    <p><strong>Titre:</strong> <?= $this->e($rapport->getTitreRapport()) ?></p>
                    <p><strong>Entreprise:</strong> <?= $this->e($rapport->getEntreprise()->getRaisonSociale()) ?></p>
                    <p><strong>Statut:</strong>
                        <span class="badge bg-success">Vote Unanime OUI</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Formulaire d'Assignation</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?= $this->csrf() ?>

                        <div class="mb-3">
                            <label class="form-label">Directeur de Mémoire *</label>
                            <select name="directeur_memoire_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($directeurs as $directeur): ?>
                                    <option value="<?= $directeur->getId() ?>">
                                        <?= $this->e($directeur->getNomComplet()) ?>
                                        (<?= $this->e($directeur->getGrade()) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">
                                Encadre actuellement: <?= $directeur->getNombreEncadrements() ?> étudiants
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Encadreur Pédagogique *</label>
                            <select name="encadreur_pedagogique_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($encadreurs as $encadreur): ?>
                                    <option value="<?= $encadreur->getId() ?>">
                                        <?= $this->e($encadreur->getNomComplet()) ?>
                                        (<?= $this->e($encadreur->getGrade()) ?>)
                                        <?php if ($encadreur->isMembreCommission()): ?>
                                            <span class="badge bg-primary">Membre Commission</span>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted text-danger">
                                ⚠️ Doit obligatoirement être membre de la commission
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <strong>Important:</strong>
                            <ul class="mb-0">
                                <li>L'encadreur pédagogique DOIT être membre de la commission</li>
                                <li>Le directeur de mémoire sera l'encadrant principal du mémoire</li>
                                <li>Cette assignation est définitive et ne pourra être modifiée</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/commission/assignation" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Assigner les Encadrants
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
```

#### 3. Admin/Commission/PvCommissionController

**Responsabilités**:
- Créer et éditer les comptes-rendus (PV) de commission
- Lier les rapports évalués à une session
- Générer le PDF du PV

**Routes**:
- GET `/admin/commission/pv` - Liste PV
- GET `/admin/commission/pv/nouveau` - Créer PV
- POST `/admin/commission/pv` - Enregistrer
- GET `/admin/commission/pv/{id}` - Voir PV
- GET `/admin/commission/pv/{id}/pdf` - Télécharger PDF
- GET `/admin/commission/pv/{id}/modifier` - Éditer
- PUT `/admin/commission/pv/{id}` - Mettre à jour

---

## <a name="prd-06"></a>PRD 06 - JURYS ET SOUTENANCES

### Services à Créer

#### 1. AptitudeService

**Fichier**: `src/Service/Soutenance/AptitudeService.php`

**Responsabilités**:
- Validation aptitude par encadreur pédagogique
- Vérification que l'encadreur est bien assigné
- Gestion des justifications en cas de refus

**Blueprint**:

```php
<?php
namespace App\Service\Soutenance;

use App\Entity\Soutenance\AptitudeSoutenance;
use App\Entity\Student\Etudiant;
use App\Service\Email\EmailService;
use Doctrine\ORM\EntityManagerInterface;

class AptitudeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EmailService $emailService
    ) {}

    public function createAptitudeRequest(Etudiant $etudiant): AptitudeSoutenance
    {
        // Vérifier que l'étudiant a des encadrants assignés
        $affectation = $etudiant->getAffectationEncadrant();
        if (!$affectation) {
            throw new \RuntimeException('Aucun encadrant assigné');
        }

        $aptitude = new AptitudeSoutenance();
        $aptitude->setEtudiant($etudiant);
        $aptitude->setEncadreur($affectation->getEncadreurPedagogique());
        $aptitude->setAnneeAcademique($etudiant->getAnneeAcademique());

        $this->em->persist($aptitude);
        $this->em->flush();

        // Email notification encadreur
        $this->emailService->sendAptitudeNotification($aptitude);

        return $aptitude;
    }

    public function validerAptitude(
        AptitudeSoutenance $aptitude,
        bool $estApte,
        string $commentaire = null
    ): void {
        $aptitude->setEstApte($estApte);
        $aptitude->setCommentaire($commentaire);
        $aptitude->setDateValidation(new \DateTime());

        $this->em->flush();

        // Email notification étudiant
        $this->emailService->sendAptitudeResult($aptitude);

        // Si apte → débloquer composition jury
        if ($estApte) {
            $this->triggerJuryComposition($aptitude->getEtudiant());
        }
    }

    public function getAptitudesEnAttente(int $encadreurId): array
    {
        return $this->em->getRepository(AptitudeSoutenance::class)
            ->findBy([
                'id_encadreur' => $encadreurId,
                'est_apte' => null
            ]);
    }

    private function triggerJuryComposition(Etudiant $etudiant): void
    {
        // Trigger workflow transition aptitude_validee
        // Event pour notifier admin de composer le jury
    }
}
```

#### 2. PlanningService

**Fichier**: `src/Service/Soutenance/PlanningService.php`

**Responsabilités**:
- Programmation soutenances (date, heure, salle)
- Détection conflits (salle occupée, jury membre occupé)
- Vérification disponibilité

**Blueprint**:

```php
<?php
namespace App\Service\Soutenance;

use App\Entity\Soutenance\Soutenance;
use App\Entity\Academic\Salle;
use Doctrine\ORM\EntityManagerInterface;
use Carbon\Carbon;

class PlanningService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function programmer(
        Soutenance $soutenance,
        \DateTime $dateSoutenance,
        \DateTime $heureDebut,
        \DateTime $heureFin,
        Salle $salle
    ): void {
        // Vérifications
        $this->verifierDisponibiliteSalle($salle, $dateSoutenance, $heureDebut, $heureFin);
        $this->verifierDisponibiliteJury($soutenance->getJury(), $dateSoutenance, $heureDebut, $heureFin);

        $soutenance->setDateSoutenance($dateSoutenance);
        $soutenance->setHeureDebut($heureDebut);
        $soutenance->setHeureFin($heureFin);
        $soutenance->setSalle($salle);
        $soutenance->setStatut('programmee');

        $this->em->flush();

        // Notifications emails tous participants
        $this->notifierParticipants($soutenance);
    }

    private function verifierDisponibiliteSalle(
        Salle $salle,
        \DateTime $date,
        \DateTime $debut,
        \DateTime $fin
    ): void {
        $conflits = $this->em->getRepository(Soutenance::class)
            ->findConflitsSalle($salle, $date, $debut, $fin);

        if (count($conflits) > 0) {
            throw new \RuntimeException(
                sprintf('Salle %s occupée à ce créneau', $salle->getNom())
            );
        }
    }

    private function verifierDisponibiliteJury(
        Jury $jury,
        \DateTime $date,
        \DateTime $debut,
        \DateTime $fin
    ): void {
        foreach ($jury->getMembres() as $composition) {
            $conflits = $this->findConflitsEnseignant(
                $composition->getEnseignant(),
                $date,
                $debut,
                $fin
            );

            if (count($conflits) > 0) {
                throw new \RuntimeException(
                    sprintf(
                        'Conflit pour %s: déjà membre d\'un autre jury à ce créneau',
                        $composition->getEnseignant()->getNomComplet()
                    )
                );
            }
        }
    }

    public function getCreneauxDisponibles(
        \DateTime $date,
        Salle $salle = null
    ): array {
        // Retourner liste créneaux libres (8h-18h, par tranche 2h)
        // Exclure créneaux déjà occupés
    }
}
```

#### 3. NotationService

**Fichier**: `src/Service/Soutenance/NotationService.php`

**Responsabilités**:
- Saisie notes par critère d'évaluation
- Validation barèmes (note min/max par critère)
- Calcul note mémoire

**Blueprint**:

```php
<?php
namespace App\Service\Soutenance;

use App\Entity\Soutenance\Soutenance;
use App\Entity\Soutenance\NoteSoutenance;
use App\Entity\Soutenance\CritereEvaluation;
use Doctrine\ORM\EntityManagerInterface;
use Brick\Math\BigDecimal;

class NotationService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function saisirNote(
        Soutenance $soutenance,
        CritereEvaluation $critere,
        string $note
    ): NoteSoutenance {
        // Validation barème
        $bareme = $critere->getBareme();
        $noteDecimal = BigDecimal::of($note);

        if ($noteDecimal->isLessThan($bareme->getNoteMin()) ||
            $noteDecimal->isGreaterThan($bareme->getNoteMax())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Note hors barème (%s - %s)',
                    $bareme->getNoteMin(),
                    $bareme->getNoteMax()
                )
            );
        }

        // Créer ou mettre à jour note
        $noteSoutenance = $this->em->getRepository(NoteSoutenance::class)
            ->findOneBy([
                'soutenance' => $soutenance,
                'critere' => $critere
            ]);

        if (!$noteSoutenance) {
            $noteSoutenance = new NoteSoutenance();
            $noteSoutenance->setSoutenance($soutenance);
            $noteSoutenance->setCritere($critere);
            $this->em->persist($noteSoutenance);
        }

        $noteSoutenance->setNote($noteDecimal);
        $this->em->flush();

        // Vérifier si toutes notes saisies
        if ($this->toutesNotesSaisies($soutenance)) {
            $this->calculerNoteMemoireTotale($soutenance);
        }

        return $noteSoutenance;
    }

    private function toutesNotesSaisies(Soutenance $soutenance): bool
    {
        $criteres = $this->em->getRepository(CritereEvaluation::class)
            ->findBy(['actif' => true]);

        $notesSaisies = $soutenance->getNotes()->count();

        return count($criteres) === $notesSaisies;
    }

    private function calculerNoteMemoireTotale(Soutenance $soutenance): void
    {
        $notes = $soutenance->getNotes();
        $total = BigDecimal::zero();
        $totalCoefficients = BigDecimal::zero();

        foreach ($notes as $note) {
            $coeff = BigDecimal::of($note->getCritere()->getCoefficient());
            $valeur = BigDecimal::of($note->getNote());

            $total = $total->plus($valeur->multipliedBy($coeff));
            $totalCoefficients = $totalCoefficients->plus($coeff);
        }

        $moyennePonderee = $total->dividedBy(
            $totalCoefficients,
            2,
            \Brick\Math\RoundingMode::HALF_UP
        );

        $soutenance->setNoteMemoireTotale($moyennePonderee->toFloat());
        $this->em->flush();
    }

    public function getGrilleCriteres(): array
    {
        return $this->em->getRepository(CritereEvaluation::class)
            ->findBy(['actif' => true], ['ordre' => 'ASC']);
    }
}
```

#### 4. MoyenneCalculationService

**Fichier**: `src/Service/Soutenance/MoyenneCalculationService.php`

**Responsabilités**:
- Calcul moyenne finale selon formule (Annexe 2 ou 3)
- Utilisation brick/math pour précision
- Support des deux formules

**Blueprint**:

```php
<?php
namespace App\Service/Soutenance;

use App\Entity\Student\Etudiant;
use App\Entity\Soutenance\Soutenance;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class MoyenneCalculationService
{
    /**
     * Formule Annexe 2 (Standard):
     * Note Finale = ((Moyenne M1 × 2) + (Moyenne S1 M2 × 3) + (Note Mémoire × 3)) / 8
     */
    public function calculerMoyenneStandard(
        float $moyenneM1,
        float $moyenneS1M2,
        float $noteMémoire
    ): float {
        $m1 = BigDecimal::of($moyenneM1)->multipliedBy(2);
        $s1m2 = BigDecimal::of($moyenneS1M2)->multipliedBy(3);
        $memoire = BigDecimal::of($noteMémoire)->multipliedBy(3);

        $somme = $m1->plus($s1m2)->plus($memoire);
        $moyenne = $somme->dividedBy(8, 2, RoundingMode::HALF_UP);

        return $moyenne->toFloat();
    }

    /**
     * Formule Annexe 3 (Simplifiée):
     * Note Finale = ((Moyenne M1 × 1) + (Note Mémoire × 2)) / 3
     */
    public function calculerMoyenneSimplifiee(
        float $moyenneM1,
        float $noteMémoire
    ): float {
        $m1 = BigDecimal::of($moyenneM1)->multipliedBy(1);
        $memoire = BigDecimal::of($noteMémoire)->multipliedBy(2);

        $somme = $m1->plus($memoire);
        $moyenne = $somme->dividedBy(3, 2, RoundingMode::HALF_UP);

        return $moyenne->toFloat();
    }

    public function calculerMoyennePourEtudiant(
        Etudiant $etudiant,
        string $typeFormule = 'standard'
    ): float {
        $moyenneM1 = $etudiant->getMoyenneM1();
        $moyenneS1M2 = $etudiant->getMoyenneS1M2();
        $noteMémoire = $etudiant->getSoutenance()->getNoteMemoireTotale();

        if ($typeFormule === 'simplifie') {
            return $this->calculerMoyenneSimplifiee($moyenneM1, $noteMémoire);
        }

        return $this->calculerMoyenneStandard($moyenneM1, $moyenneS1M2, $noteMémoire);
    }
}
```

#### 5. DeliberationService

**Fichier**: `src/Service/Soutenance/DeliberationService.php`

**Responsabilités**:
- Calcul résultat final
- Détermination mention
- Génération décision jury

**Blueprint**:

```php
<?php
namespace App\Service\Soutenance;

use App\Entity\Soutenance\Soutenance;
use App\Entity\Soutenance\ResultatFinal;
use App\Entity\Soutenance\Mention;
use App\Entity\Soutenance\DecisionJury;
use Doctrine\ORM\EntityManagerInterface;

class DeliberationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MoyenneCalculationService $moyenneService
    ) {}

    public function deliberer(
        Soutenance $soutenance,
        string $typeFormule = 'standard'
    ): ResultatFinal {
        $etudiant = $soutenance->getEtudiant();

        // Calcul moyenne finale
        $moyenneFinale = $this->moyenneService->calculerMoyennePourEtudiant(
            $etudiant,
            $typeFormule
        );

        // Détermination décision
        $decision = $this->determinerDecision($moyenneFinale);

        // Détermination mention
        $mention = $this->determinerMention($moyenneFinale, $decision);

        // Créer résultat final
        $resultat = new ResultatFinal();
        $resultat->setEtudiant($etudiant);
        $resultat->setSoutenance($soutenance);
        $resultat->setMoyenneM1($etudiant->getMoyenneM1());
        $resultat->setMoyenneS1M2($etudiant->getMoyenneS1M2());
        $resultat->setNoteMémoire($soutenance->getNoteMemoireTotale());
        $resultat->setMoyenneFinale($moyenneFinale);
        $resultat->setDecision($decision);
        $resultat->setMention($mention);
        $resultat->setTypeFormule($typeFormule);
        $resultat->setDateDeliberation(new \DateTime());

        $this->em->persist($resultat);
        $this->em->flush();

        return $resultat;
    }

    private function determinerDecision(float $moyenne): string
    {
        if ($moyenne >= 10.0) {
            return 'admis';
        }

        if ($moyenne >= 8.0) {
            return 'ajourne'; // Peut repasser
        }

        return 'refuse';
    }

    private function determinerMention(float $moyenne, string $decision): ?Mention
    {
        if ($decision !== 'admis') {
            return null;
        }

        return $this->em->getRepository(Mention::class)
            ->findMentionPourMoyenne($moyenne);
    }
}
```

### Contrôleurs à Créer (PRD 06)

1. **Admin/Soutenance/JuryController** - Composition jury (5 membres)
2. **Admin/Soutenance/PlanningController** - Calendrier, programmation
3. **Admin/Soutenance/NotationController** - Grille notation
4. **Admin/Soutenance/DeliberationController** - Calcul final, PV

*(Patterns similaires aux contrôleurs PRD 05)*

---

## <a name="prd-07"></a>PRD 07 - GÉNÉRATION DOCUMENTS

### Services à Créer

#### 1. DocumentService (Orchestration)

**Fichier**: `src/Service/Document/DocumentService.php`

**Blueprint**:

```php
<?php
namespace App\Service\Document;

use App\Entity\System\GeneratedDocument;
use Doctrine\ORM\EntityManagerInterface;

class DocumentService
{
    private array $generators = [];

    public function __construct(
        private EntityManagerInterface $em,
        private ReferenceGenerator $referenceGenerator,
        private DocumentStorage $storage
    ) {
        // Register generators
        $this->registerGenerators();
    }

    public function generate(
        string $type,
        array $data,
        int $generatedBy
    ): GeneratedDocument {
        $generator = $this->getGenerator($type);

        // Generate PDF
        $pdf = $generator->generate($data);

        // Generate reference
        $reference = $this->referenceGenerator->generate($type);

        // Store file
        $filePath = $this->storage->store($pdf, $type, $reference);

        // Create document entity
        $document = new GeneratedDocument();
        $document->setReference($reference);
        $document->setType($type);
        $document->setFilePath($filePath);
        $document->setFileSize(filesize($filePath));
        $document->setGeneratedBy($generatedBy);
        $document->setGeneratedAt(new \DateTime());
        $document->setMetadata($data);

        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    private function registerGenerators(): void
    {
        // Register all PDF generators
        $this->generators['recu'] = new RecuPaiementGenerator();
        $this->generators['bulletin'] = new BulletinGenerator();
        // ... etc
    }
}
```

#### 2. ReferenceGenerator

**Fichier**: `src/Service/Document/ReferenceGenerator.php`

**Blueprint**:

```php
<?php
namespace App\Service/Document;

use Doctrine\ORM\EntityManagerInterface;

class ReferenceGenerator
{
    private const PREFIXES = [
        'recu' => 'REC',
        'bulletin' => 'BUL',
        'rapport' => 'RAP',
        'pv_commission' => 'PVC',
        'planning' => 'PLN',
        'annexe1' => 'ANX1',
        'annexe2' => 'ANX2',
        'annexe3' => 'ANX3',
        'pv_final' => 'PVF',
    ];

    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function generate(string $type): string
    {
        $prefix = self::PREFIXES[$type] ?? 'DOC';
        $year = date('Y');

        // Get last sequence for this type and year
        $lastSequence = $this->getLastSequence($type, $year);
        $newSequence = $lastSequence + 1;

        // Format: PREFIX-YEAR-SEQUENCE (ex: REC-2025-00001)
        return sprintf('%s-%s-%05d', $prefix, $year, $newSequence);
    }

    private function getLastSequence(string $type, int $year): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('MAX(CAST(SUBSTRING(d.reference, -5) AS INT))')
           ->from(GeneratedDocument::class, 'd')
           ->where('d.type = :type')
           ->andWhere('YEAR(d.generatedAt) = :year')
           ->setParameter('type', $type)
           ->setParameter('year', $year);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
```

#### 3. DocumentStorage

**Fichier**: `src/Service/Document/DocumentStorage.php`

**Blueprint**:

```php
<?php
namespace App\Service\Document;

class DocumentStorage
{
    private string $basePath;

    public function __construct(string $storagePath)
    {
        $this->basePath = $storagePath . '/documents';
    }

    public function store(
        string $content,
        string $type,
        string $reference
    ): string {
        $year = date('Y');
        $directory = $this->getDirectory($type, $year);

        // Create directory if not exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $reference . '.pdf';
        $filePath = $directory . '/' . $filename;

        file_put_contents($filePath, $content);

        return $filePath;
    }

    private function getDirectory(string $type, int $year): string
    {
        $typeDir = match($type) {
            'recu' => 'recus',
            'bulletin' => 'bulletins',
            'rapport' => 'rapports',
            'pv_commission' => 'pv_commission',
            'planning' => 'planning',
            'annexe1', 'annexe2', 'annexe3', 'pv_final' => 'pv_finaux',
            default => 'autres'
        };

        return $this->basePath . '/' . $typeDir . '/' . $year;
    }

    public function retrieve(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('Fichier introuvable');
        }

        return file_get_contents($filePath);
    }

    public function delete(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }
}
```

---

## <a name="prd-08"></a>PRD 08 - PARAMÉTRAGE SYSTÈME

### Contrôleurs à Créer (Patterns CRUD Standard)

Tous les contrôleurs de paramétrage suivent le même pattern CRUD:

1. **Admin/Parametrage/AnneeAcademiqueController** - CRUD années académiques
2. **Admin/Parametrage/NiveauEtudeController** - CRUD niveaux d'étude
3. **Admin/Parametrage/UeController** - CRUD UE/ECUE
4. **Admin/Parametrage/CritereEvaluationController** - CRUD critères
5. **Admin/Parametrage/MenuController** - Gestion menus
6. **Admin/Parametrage/MessageController** - Gestion messages
7. **Admin/Maintenance/AuditController** - Visualisation audit
8. **Admin/Maintenance/CacheController** - Gestion cache

**Pattern Standard (exemple Année Académique)**:

```php
<?php
namespace App\Controller\Admin\Parametrage;

use App\Controller\AbstractController;
use App\Entity\Academic\AnneeAcademique;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AnneeAcademiqueController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->denyAccessUnlessGranted('PARAM_ANNEE');

        $annees = $this->em->getRepository(AnneeAcademique::class)
            ->findBy([], ['date_debut' => 'DESC']);

        return $this->render('admin/parametrage/annees/index.php', [
            'annees' => $annees
        ]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $this->denyAccessUnlessGranted('PARAM_ANNEE_CREER');

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            try {
                $annee = new AnneeAcademique();
                $annee->setLibelleAnnee($data['libelle']);
                $annee->setDateDebut(new \DateTime($data['date_debut']));
                $annee->setDateFin(new \DateTime($data['date_fin']));
                $annee->setEstActive($data['est_active'] ?? false);
                $annee->setEstOuverteInscription($data['est_ouverte'] ?? true);

                $this->em->persist($annee);
                $this->em->flush();

                $this->addFlash('success', 'Année académique créée');
                return $this->redirectToRoute('/admin/parametrage/annees');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('admin/parametrage/annees/create.php');
    }

    // ... edit, update, delete methods
}
```

**Template Standard**:

```php
<?php $this->layout('admin/layout', ['title' => 'Années Académiques']) ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Années Académiques</h1>
        <a href="/admin/parametrage/annees/nouvelle" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Année
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Active</th>
                        <th>Inscriptions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($annees as $annee): ?>
                        <tr>
                            <td><?= $this->e($annee->getLibelleAnnee()) ?></td>
                            <td><?= $annee->getDateDebut()->format('d/m/Y') ?></td>
                            <td><?= $annee->getDateFin()->format('d/m/Y') ?></td>
                            <td>
                                <?php if ($annee->isEstActive()): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($annee->isEstOuverteInscription()): ?>
                                    <span class="badge bg-info">Ouvertes</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Fermées</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/parametrage/annees/<?= $annee->getId() ?>/modifier"
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

## <a name="patterns"></a>PATTERNS ET STANDARDS

### Pattern Controller Standard

Tous les contrôleurs doivent suivre ce pattern:

```php
<?php
namespace App\Controller\[Namespace];

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class [Name]Controller extends AbstractController
{
    public function __construct(
        // Dependencies
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Permission check
        $this->denyAccessUnlessGranted('PERMISSION');

        // Logic

        // Render
        return $this->render('path/to/template.php', []);
    }

    // CRUD methods: create, show, edit, update, delete
}
```

### Pattern Template Standard

Tous les templates doivent utiliser le layout:

```php
<?php $this->layout('admin/layout', ['title' => 'Page Title']) ?>

<div class="container-fluid">
    <h1>Page Title</h1>

    <!-- Content -->

</div>
```

### Pattern Service Standard

```php
<?php
namespace App\Service\[Namespace];

use Doctrine\ORM\EntityManagerInterface;

class [Name]Service
{
    public function __construct(
        private EntityManagerInterface $em,
        // Other dependencies
    ) {}

    public function methodName(): ReturnType
    {
        // Logic

        $this->em->persist($entity);
        $this->em->flush();

        return $result;
    }
}
```

---

## <a name="tests"></a>TESTS ET VALIDATION

### Tests Unitaires (Services)

```php
<?php
namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\Soutenance\AptitudeService;

class AptitudeServiceTest extends TestCase
{
    private AptitudeService $service;

    protected function setUp(): void
    {
        // Setup mocks
        $this->service = new AptitudeService(/* ... */);
    }

    public function testValiderAptitude(): void
    {
        // Arrange
        $aptitude = $this->createMockAptitude();

        // Act
        $this->service->validerAptitude($aptitude, true, 'Apte');

        // Assert
        $this->assertTrue($aptitude->isEstApte());
        $this->assertNotNull($aptitude->getDateValidation());
    }
}
```

### Tests d'Intégration (Workflows)

```php
<?php
namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;

class CommissionWorkflowTest extends TestCase
{
    public function testVoteUnanimeOui(): void
    {
        // Test workflow complet vote unanime

        // 1. Transfert rapport → en_attente_evaluation
        // 2. 4 votes OUI → vote_unanime_oui
        // 3. Assignation → pret_pour_pv
    }
}
```

### Tests Fonctionnels (End-to-end)

```php
<?php
namespace App\Tests\Functional;

use PHPUnit\Framework\TestCase;

class SoutenanceCompleteTest extends TestCase
{
    public function testParcours CompletSoutenance(): void
    {
        // Test parcours complet étudiant

        // 1. Encadrants assignés
        // 2. Aptitude validée
        // 3. Jury composé
        // 4. Soutenance programmée
        // 5. Notes saisies
        // 6. Délibération
        // 7. PV générés
    }
}
```

---

## CONCLUSION

Ce guide fournit tous les blueprints nécessaires pour compléter exhaustivement les PRD 05-08.

**Pour implémenter**:
1. Suivre les patterns fournis
2. Créer les fichiers dans l'ordre de priorité
3. Tester chaque composant individuellement
4. Intégrer progressivement
5. Documenter au fur et à mesure

**Estimation totale**: 12-17 jours de développement pour une implémentation complète.

---

*Document généré le 2026-02-06 - Plateforme MIAGE-GI*
