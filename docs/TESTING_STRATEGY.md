# CheckMaster - Stratégie de Tests Exhaustifs

**Date**: 2026-01-16  
**Version**: 1.0  
**Objectif**: Atteindre 100% de fiabilité opérationnelle avec coverage >= 80%

---

## 📋 Vue d'Ensemble

Cette stratégie définit l'approche complète pour tester l'application CheckMaster composée de:
- **58 Controllers** (12 sous-dossiers)
- **69 Models** (ORM)
- **40 Services** (15 sous-dossiers)
- **14 États Workflow**
- **13 Groupes Utilisateurs**
- **13 Types Documents PDF**

---

## 🎯 Objectifs de Tests

### Objectifs Quantitatifs
- ✅ **Coverage >= 80%** sur l'ensemble du code
- ✅ **100% des controllers** testés (58 fichiers)
- ✅ **100% des models critiques** testés (30+ models prioritaires)
- ✅ **100% des services Core** testés (10 services)
- ✅ **100% des transitions workflow** testées (14 états, ~30 transitions)

### Objectifs Qualitatifs
- ✅ Tests **unitaires** isolés (mocks/stubs)
- ✅ Tests **d'intégration** avec DB réelle
- ✅ Tests **fonctionnels** end-to-end
- ✅ Tests **de sécurité** (permissions, CSRF, injections)
- ✅ Tests **de performance** (< 200ms par requête)

---

## 🏗️ Architecture de Tests

```
tests/
├── Unit/                           # Tests unitaires isolés
│   ├── Controllers/                # 58 tests controllers
│   │   ├── AuthControllerTest.php
│   │   ├── DashboardControllerTest.php
│   │   ├── Admin/                  # 11 tests
│   │   ├── Commission/             # 6 tests
│   │   ├── Communication/          # 6 tests
│   │   ├── Etudiant/               # 7 tests
│   │   ├── Finance/                # 3 tests
│   │   ├── Rapport/                # 2 tests
│   │   ├── Scolarite/              # 8 tests
│   │   ├── Secretariat/            # 2 tests
│   │   ├── Soutenance/             # 6 tests
│   │   └── Workflow/               # 1 test
│   ├── Models/                     # 69 tests models
│   │   ├── UtilisateurTest.php
│   │   ├── EtudiantTest.php
│   │   ├── CandidatureTest.php
│   │   ├── WorkflowEtatTest.php
│   │   ├── WorkflowTransitionTest.php
│   │   ├── SoutenanceTest.php
│   │   └── ... (63 autres)
│   ├── Services/                   # 40 tests services
│   │   ├── Core/                   # 6 services critiques
│   │   │   ├── ServiceWorkflowTest.php
│   │   │   ├── ServicePermissionsTest.php
│   │   │   ├── ServiceAuditTest.php
│   │   │   ├── ServiceNotificationTest.php
│   │   │   ├── ServicePdfTest.php
│   │   │   └── ServiceParametresTest.php
│   │   ├── Commission/
│   │   ├── Soutenance/
│   │   ├── Finance/
│   │   └── ... (autres sous-dossiers)
│   ├── Validators/                 # 25 tests validators
│   ├── Middleware/                 # 16 tests middlewares
│   └── Utils/                      # 14 tests utilitaires
│
├── Integration/                    # Tests d'intégration
│   ├── Database/                   # Tests avec DB
│   │   ├── MigrationTest.php       # Toutes migrations OK
│   │   ├── SeedTest.php            # Tous seeds OK
│   │   └── RelationsTest.php       # FK et relations
│   ├── Workflow/                   # Tests workflow complet
│   │   ├── WorkflowCompletTest.php # INSCRIT → DIPLOME_DELIVRE
│   │   ├── TransitionsTest.php     # Toutes transitions
│   │   └── GatesTest.php           # Gates et prérequis
│   ├── Api/                        # Tests endpoints
│   │   ├── AuthApiTest.php
│   │   ├── CandidatureApiTest.php
│   │   └── SoutenanceApiTest.php
│   └── Permissions/                # Tests permissions
│       ├── AdministrateurTest.php  # 13 fichiers (1 par groupe)
│       └── ...
│
├── Feature/                        # Tests fonctionnels E2E
│   ├── Candidature/
│   │   ├── SoumissionCandidatureTest.php
│   │   └── ValidationCandidatureTest.php
│   ├── Commission/
│   │   ├── SessionCommissionTest.php
│   │   └── EvaluationRapportTest.php
│   ├── Soutenance/
│   │   ├── ConstitutionJuryTest.php
│   │   ├── SaisieNotesTest.php
│   │   └── DecisionJuryTest.php
│   ├── Finance/
│   │   ├── PaiementTest.php
│   │   └── PenaliteTest.php
│   └── Documents/
│       ├── GenerationPdfTest.php
│       └── ArchivageTest.php
│
└── TestCase.php                    # Classe de base (helpers)
```

---

## 🔬 Stratégie par Type de Composant

### 1. Tests Controllers (58 fichiers)

**Pattern**: Mock services, tester délégation et validation entrées

```php
namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;
use App\Services\Security\ServiceAuthentification;
use Src\Http\Request;
use Src\Http\Response;

class AuthControllerTest extends TestCase
{
    private AuthController $controller;
    private $mockAuthService;

    protected function setUp(): void
    {
        $this->mockAuthService = $this->createMock(ServiceAuthentification::class);
        $this->controller = new AuthController();
        
        // Injecter le mock (via reflection si nécessaire)
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('authService');
        $property->setAccessible(true);
        $property->setValue($this->controller, $this->mockAuthService);
    }

    public function testLoginWithValidCredentials(): void
    {
        $this->mockAuthService->expects($this->once())
            ->method('authentifier')
            ->with('user@test.com', 'password123')
            ->willReturn([
                'success' => true,
                'user' => ['id_utilisateur' => 1],
                'token' => 'abc123'
            ]);

        $_POST = ['email' => 'user@test.com', 'password' => 'password123'];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $response = $this->controller->login();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $this->mockAuthService->expects($this->once())
            ->method('authentifier')
            ->willReturn(['success' => false, 'error' => 'Identifiants invalides']);

        $_POST = ['email' => 'invalid@test.com', 'password' => 'wrong'];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $response = $this->controller->login();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testLogoutClearsSession(): void
    {
        $this->mockAuthService->expects($this->once())
            ->method('supprimerSession');

        $response = $this->controller->logout();

        $this->assertInstanceOf(Response::class, $response);
    }
}
```

**À tester pour chaque controller**:
- ✅ Validation des entrées (champs requis, formats)
- ✅ Vérification permissions (appels SecurityUtils::can())
- ✅ Délégation aux services (pas de logique métier)
- ✅ Gestion erreurs (try/catch)
- ✅ Format réponse (JSON ou View)
- ✅ Audit actions critiques

---

### 2. Tests Models (69 fichiers)

**Pattern**: Mock PDO, tester CRUD et relations

```php
namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Utilisateur;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class UtilisateurTest extends TestCase
{
    private Utilisateur $model;
    private $mockPdo;
    private $mockLogger;
    private $mockStatement;

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);

        $this->model = new Utilisateur($this->mockPdo, $this->mockLogger);
    }

    public function testFindByIdReturnsUser(): void
    {
        $expectedUser = [
            'id_utilisateur' => 1,
            'login_utilisateur' => 'admin@ufhb.ci',
            'nom_utilisateur' => 'Administrateur',
            'statut_utilisateur' => 'Actif'
        ];

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 1]);

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedUser);

        $result = $this->model->find(1);

        $this->assertEquals($expectedUser, $result);
    }

    public function testFindByEmailReturnsUser(): void
    {
        $email = 'admin@ufhb.ci';
        $expectedUser = ['id_utilisateur' => 1, 'login_utilisateur' => $email];

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':email' => $email]);

        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedUser);

        $result = $this->model->findByEmail($email);

        $this->assertEquals($expectedUser, $result);
    }

    public function testSaveInsertsNewUser(): void
    {
        $userData = [
            'login_utilisateur' => 'new@ufhb.ci',
            'mdp_utilisateur' => password_hash('password', PASSWORD_ARGON2ID),
            'nom_utilisateur' => 'Nouveau',
            'statut_utilisateur' => 'Actif'
        ];

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->mockPdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('5');

        $result = $this->model->save($userData);

        $this->assertEquals(5, $result);
    }

    public function testPasswordIsHashedWithArgon2id(): void
    {
        $password = 'TestPassword123!';
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertTrue(password_verify($password, $hash));
        $this->assertStringStartsWith('$argon2id$', $hash);
    }

    public function testMaxTentativesEchecConstant(): void
    {
        $this->assertEquals(5, Utilisateur::MAX_TENTATIVES_ECHEC);
    }

    public function testDureeVerrouillageConstant(): void
    {
        $this->assertEquals(15, Utilisateur::DUREE_VERROUILLAGE_MINUTES);
    }
}
```

**À tester pour chaque model**:
- ✅ find() retourne entité ou null
- ✅ findAll() retourne tableau
- ✅ save() INSERT ou UPDATE selon ID
- ✅ delete() supprime entité
- ✅ Relations (belongsTo, hasMany)
- ✅ Méthodes métier spécifiques
- ✅ Constantes de validation

---

### 3. Tests Services (40 fichiers)

**Pattern**: Mock dépendances, tester logique métier pure

```php
namespace Tests\Unit\Services\Core;

use PHPUnit\Framework\TestCase;
use App\Services\Workflow\ServiceWorkflow;
use App\Models\DossierEtudiant;
use App\Models\WorkflowEtat;
use App\Models\WorkflowTransition;
use App\Services\Security\ServiceAudit;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\WorkflowException;
use PDO;
use Psr\Log\LoggerInterface;

class ServiceWorkflowTest extends TestCase
{
    private ServiceWorkflow $service;
    private $mockPdo;
    private $mockLogger;
    private $mockAudit;
    private $mockNotification;

    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockAudit = $this->createMock(ServiceAudit::class);
        $this->mockNotification = $this->createMock(ServiceNotification::class);

        $this->service = new ServiceWorkflow(
            $this->mockPdo,
            $this->mockLogger,
            $this->mockAudit,
            $this->mockNotification
        );
    }

    public function testTransitionInscritToCandidatureSoumise(): void
    {
        $dossierId = 1;
        $utilisateurId = 100;

        // Mock des appels DB pour transition valide
        $this->mockPdo->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturn($this->createMock(\PDOStatement::class));

        $result = $this->service->effectuerTransition(
            $dossierId,
            'CANDIDATURE_SOUMISE',
            $utilisateurId
        );

        $this->assertTrue($result);
    }

    public function testInvalidTransitionThrowsException(): void
    {
        $this->expectException(WorkflowException::class);

        $this->service->effectuerTransition(
            1,
            'ETAT_INEXISTANT',
            100
        );
    }

    public function testTransitionFromTerminalStateThrowsException(): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage('État terminal');

        // Mock d'un dossier à l'état DIPLOME_DELIVRE (terminal)
        $this->service->effectuerTransition(
            1,
            'AUTRE_ETAT',
            100
        );
    }

    public function testVerifierGatesReturnsCriteria(): void
    {
        $gateResult = $this->service->verifierGates(1, 'EN_ATTENTE_COMMISSION');

        $this->assertIsArray($gateResult);
        $this->assertArrayHasKey('paiement_ok', $gateResult);
        $this->assertArrayHasKey('rapport_ok', $gateResult);
        $this->assertArrayHasKey('documents_ok', $gateResult);
    }

    public function testCalculSLADeadline(): void
    {
        $dateDebut = new \DateTime('2024-01-01');
        $delaiJours = 7;

        $deadline = $this->service->calculerDeadlineSLA($dateDebut, $delaiJours);

        $this->assertInstanceOf(\DateTime::class, $deadline);
        $this->assertEquals('2024-01-08', $deadline->format('Y-m-d'));
    }

    public function testGenererAlerteSLA(): void
    {
        $this->mockNotification->expects($this->once())
            ->method('envoyerNotification');

        $this->service->genererAlerteSLA(1, 'orange', 80);
    }
}
```

**À tester pour chaque service**:
- ✅ Logique métier correcte
- ✅ Validation avant traitement
- ✅ Transactions pour multi-tables
- ✅ Gestion erreurs avec exceptions
- ✅ Logging approprié
- ✅ Audit des actions critiques
- ✅ Stateless (pas de state global)

---

### 4. Tests Workflow (14 états, ~30 transitions)

```php
namespace Tests\Integration\Workflow;

use Tests\TestCase;
use App\Services\Workflow\ServiceWorkflow;
use App\Models\DossierEtudiant;
use App\Models\WorkflowEtat;

class WorkflowCompletTest extends TestCase
{
    private ServiceWorkflow $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        // Utiliser DB réelle ou in-memory SQLite
        $this->workflow = new ServiceWorkflow(/* vraies dépendances */);
    }

    public function testWorkflowComplet_InscritToDiplome(): void
    {
        // Créer un dossier test
        $dossier = DossierEtudiant::create([
            'etudiant_id' => 1,
            'annee_acad_id' => 1,
            'etat_actuel' => 'INSCRIT'
        ]);

        $etatsAttendus = [
            'INSCRIT',
            'CANDIDATURE_SOUMISE',
            'VERIFICATION_SCOLARITE',
            'FILTRE_COMMUNICATION',
            'EN_ATTENTE_COMMISSION',
            'EN_EVALUATION_COMMISSION',
            'RAPPORT_VALIDE',
            'ATTENTE_AVIS_ENCADREUR',
            'PRET_POUR_JURY',
            'JURY_EN_CONSTITUTION',
            'SOUTENANCE_PLANIFIEE',
            'SOUTENANCE_EN_COURS',
            'SOUTENANCE_TERMINEE',
            'DIPLOME_DELIVRE'
        ];

        foreach ($etatsAttendus as $index => $etat) {
            if ($index === 0) {
                // État initial
                $this->assertEquals($etat, $dossier->etat_actuel);
            } else {
                // Effectuer la transition
                $etatPrecedent = $etatsAttendus[$index - 1];
                
                $result = $this->workflow->effectuerTransition(
                    $dossier->id,
                    $etat,
                    1 // utilisateur_id admin
                );

                $this->assertTrue($result);
                
                // Recharger le dossier
                $dossier = DossierEtudiant::find($dossier->id);
                $this->assertEquals($etat, $dossier->etat_actuel);
            }
        }

        // Vérifier que toutes les transitions ont été enregistrées
        $historique = $dossier->getHistoriqueWorkflow();
        $this->assertCount(13, $historique); // 13 transitions (14 états - 1)
    }

    public function testTransition_InscritToCandidatureSoumise(): void
    {
        $dossier = $this->creerDossierTest('INSCRIT');

        $result = $this->workflow->effectuerTransition(
            $dossier->id,
            'CANDIDATURE_SOUMISE',
            1
        );

        $this->assertTrue($result);
        
        $dossier = DossierEtudiant::find($dossier->id);
        $this->assertEquals('CANDIDATURE_SOUMISE', $dossier->etat_actuel);
    }

    // ... tester les 13 autres transitions individuellement
}
```

---

### 5. Tests Permissions (13 groupes)

```php
namespace Tests\Integration\Permissions;

use Tests\TestCase;
use App\Services\Security\ServicePermissions;

class AdministrateurPermissionsTest extends TestCase
{
    private ServicePermissions $permissions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permissions = new ServicePermissions();
    }

    public function testAdministrateurAccesTousModules(): void
    {
        $utilisateurAdmin = $this->createTestUser(['id_GU' => 1]); // Groupe Administrateur

        $modules = [
            'admin.utilisateurs',
            'admin.permissions',
            'admin.parametres',
            'scolarite.etudiants',
            'commission.evaluations',
            'soutenance.jury',
            'finance.paiements'
        ];

        foreach ($modules as $module) {
            $hasAccess = $this->permissions->verifier(
                $utilisateurAdmin['id_utilisateur'],
                $module,
                'lire'
            );

            $this->assertTrue(
                $hasAccess,
                "Administrateur devrait avoir accès à {$module}"
            );
        }
    }

    public function testAdministrateurCanCreateUsers(): void
    {
        $utilisateurAdmin = $this->createTestUser(['id_GU' => 1]);

        $canCreate = $this->permissions->verifier(
            $utilisateurAdmin['id_utilisateur'],
            'admin.utilisateurs',
            'creer'
        );

        $this->assertTrue($canCreate);
    }
}

class EtudiantPermissionsTest extends TestCase
{
    public function testEtudiantNoAccessToAdminPanel(): void
    {
        $etudiant = $this->createTestUser(['id_GU' => 10]); // Groupe Étudiant

        $hasAccess = $this->permissions->verifier(
            $etudiant['id_utilisateur'],
            'admin.utilisateurs',
            'lire'
        );

        $this->assertFalse($hasAccess, "Étudiant ne devrait PAS avoir accès à admin");
    }

    public function testEtudiantCanAccessOwnProfile(): void
    {
        $etudiant = $this->createTestUser(['id_GU' => 10]);

        $hasAccess = $this->permissions->verifier(
            $etudiant['id_utilisateur'],
            'etudiant.profil',
            'lire'
        );

        $this->assertTrue($hasAccess);
    }

    public function testEtudiantCanSubmitCandidature(): void
    {
        $etudiant = $this->createTestUser(['id_GU' => 10]);

        $canSubmit = $this->permissions->verifier(
            $etudiant['id_utilisateur'],
            'etudiant.candidature',
            'creer'
        );

        $this->assertTrue($canSubmit);
    }
}
```

---

### 6. Tests Documents PDF (13 types)

```php
namespace Tests\Integration\Documents;

use Tests\TestCase;
use App\Services\Documents\ServicePdf;

class GenerationPdfTest extends TestCase
{
    private ServicePdf $pdfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfService = new ServicePdf();
    }

    public function testGenererRecuPaiement(): void
    {
        $paiement = $this->createTestPaiement([
            'montant' => 50000,
            'etudiant_id' => 1,
            'type_paiement' => 'inscription'
        ]);

        $pdf = $this->pdfService->genererRecuPaiement($paiement['id']);

        $this->assertNotNull($pdf);
        $this->assertFileExists($pdf['chemin']);
        $this->assertEquals('application/pdf', mime_content_type($pdf['chemin']));
        $this->assertGreaterThan(0, filesize($pdf['chemin']));
    }

    public function testGenererBulletinNotes(): void
    {
        $soutenance = $this->createTestSoutenance([
            'dossier_id' => 1,
            'date_soutenance' => date('Y-m-d'),
            'note_finale' => 15.5
        ]);

        $pdf = $this->pdfService->genererBulletinNotes($soutenance['id']);

        $this->assertNotNull($pdf);
        $this->assertFileExists($pdf['chemin']);
    }

    public function testGenererPvCommission(): void
    {
        $session = $this->createTestSessionCommission([
            'date_session' => date('Y-m-d'),
            'type_session' => 'evaluation'
        ]);

        $pdf = $this->pdfService->genererPvCommission($session['id']);

        $this->assertNotNull($pdf);
        $this->assertFileExists($pdf['chemin']);
    }

    // Répéter pour les 10 autres types de documents
}
```

---

## 🔄 Workflow de Tests

### 1. Développement (Local)
```bash
# Tests unitaires rapides
composer test -- --testsuite=Unit

# Tests d'intégration (avec DB)
composer test -- --testsuite=Integration

# Tests complets
composer test

# Coverage HTML
composer test -- --coverage-html tests/coverage/html
```

### 2. Pre-Commit (Git Hook)
```bash
# Formatage
composer fix

# Analyse statique
composer stan

# Tests unitaires
composer test -- --testsuite=Unit
```

### 3. CI/CD (GitHub Actions)
```yaml
- name: Run PHPUnit Tests
  run: composer test

- name: Generate Coverage
  run: composer test -- --coverage-clover coverage.xml

- name: Check Coverage Threshold
  run: |
    if [ $(php -r "echo intval(explode('%', shell_exec('grep -oP \"(?<=lines=\")[0-9.]+(?=%)\" coverage.xml'))[0]);") -lt 80 ]; then
      echo "Coverage < 80%"
      exit 1
    fi
```

---

## 📊 Checklist de Progression

### Phase 1: Tests Unitaires Controllers (58 fichiers)
- [ ] AuthController
- [ ] DashboardController
- [ ] AccueilController
- [ ] ApiController
- [ ] Admin/UtilisateursController
- [ ] Admin/PermissionsController
- [ ] Admin/ParametresController
- [ ] Admin/AuditController
- [ ] Admin/BackupController
- [ ] Admin/ArchivesController
- [ ] Admin/SessionsController
- [ ] Admin/ReclamationsController
- [ ] Admin/ReferentielsController
- [ ] Admin/ImportExportController
- [ ] Admin/DashboardController
- [ ] Commission/DashboardController
- [ ] Commission/SessionsController
- [ ] Commission/EvaluationsController
- [ ] Commission/VotesController
- [ ] Commission/PvController
- [ ] Commission/ArchivesController
- [ ] Communication/MessagerieController
- [ ] Communication/NotificationsController
- [ ] Communication/ConversationsController
- [ ] Communication/RapportsController
- [ ] Communication/CalendrierController
- [ ] Communication/ChecklistController
- [ ] Etudiant/DashboardController
- [ ] Etudiant/CandidatureController
- [ ] Etudiant/RapportController
- [ ] Etudiant/ProfilController
- [ ] Etudiant/NotesController
- [ ] Etudiant/FinancesController
- [ ] Etudiant/ReclamationsController
- [ ] Finance/PaiementsController
- [ ] Finance/PenalitesController
- [ ] Finance/ExonerationsController
- [ ] Rapport/RapportsController
- [ ] Rapport/AnnotationsController
- [ ] Scolarite/DashboardController
- [ ] Scolarite/EtudiantsController
- [ ] Scolarite/InscriptionsController
- [ ] Scolarite/CandidaturesController
- [ ] Scolarite/PaiementsController
- [ ] Scolarite/PenalitesController
- [ ] Scolarite/NotesController
- [ ] Scolarite/ReclamationsController
- [ ] Secretariat/DashboardController
- [ ] Secretariat/DossiersController
- [ ] Soutenance/SoutenancesController
- [ ] Soutenance/PlanningController
- [ ] Soutenance/JuryController
- [ ] Soutenance/NotesController
- [ ] Soutenance/ConvocationsController
- [ ] Soutenance/CandidaturesController
- [ ] Workflow/WorkflowController
- [ ] Documents/DocumentsController
- [ ] Documents/BrouillonsController
- [ ] Documents/ArchivesController
- [ ] Documents/HistoriqueController
- [ ] Academique/EntitesAcademiquesController

### Phase 2: Tests Unitaires Models (69 fichiers)
- [ ] Utilisateur
- [ ] Etudiant
- [ ] Enseignant
- [ ] PersonnelAdmin
- [ ] GroupeUtilisateur
- [ ] TypeUtilisateur
- [ ] UtilisateurGroupe
- [ ] Permission
- [ ] Rattacher
- [ ] Action
- [ ] Traitement
- [ ] DossierEtudiant
- [ ] Candidature
- [ ] RapportEtudiant
- [ ] AnnotationRapport
- [ ] WorkflowEtat
- [ ] WorkflowTransition
- [ ] WorkflowHistorique
- [ ] WorkflowAlerte
- [ ] CommissionSession
- [ ] CommissionMembre
- [ ] CommissionVote
- [ ] Soutenance
- [ ] JuryMembre
- [ ] NoteSoutenance
- [ ] DecisionJury
- [ ] RoleJury
- [ ] StatutJury
- [ ] Paiement
- [ ] Penalite
- [ ] Exoneration
- [ ] DocumentGenere
- [ ] Archive
- [ ] NotificationTemplate
- [ ] NotificationQueue
- [ ] NotificationHistorique
- [ ] EmailBounce
- [ ] MessageInterne
- [ ] Reclamation
- [ ] Escalade
- [ ] EscaladeNiveau
- [ ] EscaladeAction
- [ ] AnneeAcademique
- [ ] Semestre
- [ ] Specialite
- [ ] NiveauEtude
- [ ] Ue
- [ ] Ecue
- [ ] Entreprise
- [ ] Grade
- [ ] Fonction
- [ ] Salle
- [ ] CritereEvaluation
- [ ] NiveauApprobation
- [ ] NiveauAccesDonnees
- [ ] ConfigurationSysteme
- [ ] SessionActive
- [ ] RoleTemporaire
- [ ] CodeTemporaire
- [ ] Pister
- [ ] HistoriqueEntite
- [ ] ImportHistorique
- [ ] Migration
- [ ] MaintenanceMode
- [ ] PermissionCache
- [ ] StatCache
- [ ] Mention
- [ ] Ressource
- [ ] Groupe

### Phase 3: Tests Unitaires Services (40 fichiers)
- [ ] ServiceWorkflow
- [ ] ServicePermissions
- [ ] ServiceAudit
- [ ] ServiceNotification
- [ ] ServicePdf
- [ ] ServiceParametres
- [ ] ServiceCache
- [ ] ServiceSession
- [ ] ServiceDatabase
- [ ] ServiceFichier
- [ ] ServiceHashids
- [ ] ServiceAuthentification
- [ ] ServiceCommission (Workflow)
- [ ] ServiceEscalade
- [ ] ServiceCommission (Commission)
- [ ] ServiceAdministration
- [ ] ServiceArchivage
- [ ] ServiceBrouillon
- [ ] ServiceHistorique
- [ ] ServiceIntegrite
- [ ] ServiceBounces
- [ ] ServiceConversation
- [ ] ServiceCourrier
- [ ] ServiceMessagerie
- [ ] ServiceRappels
- [ ] ServiceExcel
- [ ] ServiceExoneration
- [ ] ServicePaiement
- [ ] ServicePenalite
- [ ] ServiceReclamation
- [ ] ServiceAnnotations
- [ ] ServiceRapport
- [ ] ServiceScolarite
- [ ] ServiceSignature
- [ ] ServiceCalendrier
- [ ] ServiceCandidature
- [ ] ServiceJury
- [ ] ServiceNotes
- [ ] ServiceSoutenance
- [ ] ServiceEntitesAcademiques

### Phase 4: Tests d'Intégration
- [ ] Migration complète BD
- [ ] Seeds complets
- [ ] Relations FK
- [ ] Workflow INSCRIT → DIPLOME_DELIVRE
- [ ] Toutes transitions workflow (13 transitions)
- [ ] Gates workflow (prérequis)
- [ ] API Authentication
- [ ] API Candidature
- [ ] API Soutenance
- [ ] Permissions Administrateur
- [ ] Permissions Scolarité
- [ ] Permissions Commission
- [ ] Permissions Étudiant
- [ ] Permissions autres groupes (9 groupes)

### Phase 5: Tests Fonctionnels E2E
- [ ] Soumission candidature étudiant
- [ ] Validation candidature scolarité
- [ ] Évaluation rapport commission
- [ ] Constitution jury
- [ ] Saisie notes soutenance
- [ ] Décision jury
- [ ] Paiement frais
- [ ] Calcul pénalités
- [ ] Génération reçu paiement
- [ ] Génération bulletin notes
- [ ] Génération PV commission
- [ ] Génération PV soutenance
- [ ] Génération convocation
- [ ] Génération attestation
- [ ] Archivage documents

### Phase 6: Validation Finale
- [ ] PHPUnit tous tests (100% pass)
- [ ] Coverage >= 80%
- [ ] PHPStan niveau 6 (0 erreurs)
- [ ] PHP-CS-Fixer (PSR-12)
- [ ] Performance (< 200ms/requête)
- [ ] Sécurité (OWASP Top 10)

---

## 🚀 Exécution

### Commandes Quotidiennes
```bash
# Tests rapides (unitaires uniquement)
composer test -- --testsuite=Unit --stop-on-failure

# Tests complets
composer test

# Coverage
composer test -- --coverage-html tests/coverage/html
open tests/coverage/html/index.html
```

### Commandes Analyse
```bash
# Analyse statique
composer stan

# Formatage
composer fix

# Tout vérifier
composer check  # = stan + test
```

---

## 📈 Métriques de Succès

| Métrique | Objectif | Actuel | Statut |
|----------|----------|--------|--------|
| **Tests Controllers** | 58/58 (100%) | 0/58 (0%) | 🔴 À faire |
| **Tests Models** | 30/69 (43%)* | 3/69 (4%) | 🟡 En cours |
| **Tests Services** | 40/40 (100%) | 10/40 (25%) | 🟡 En cours |
| **Tests Integration** | 20 tests | 7 tests | 🟡 En cours |
| **Coverage** | >= 80% | ~30% | 🔴 Insuffisant |
| **PHPStan** | Niveau 6 | Niveau 3 | 🟡 À améliorer |

*30 models critiques prioritaires sur 69 total

---

## 📝 Notes Importantes

### Dépendances Requises
```bash
# Installation
composer install

# Si problème GitHub auth
composer config --global github-oauth.github.com YOUR_TOKEN
```

### Environnement Test
```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Base de Données Test
- Utiliser SQLite in-memory pour tests unitaires
- Utiliser MySQL/MariaDB dédié pour tests d'intégration
- Migrations + Seeds automatiques avant chaque test suite

---

**Dernière mise à jour**: 2026-01-16  
**Auteur**: Équipe CheckMaster  
**Version**: 1.0
