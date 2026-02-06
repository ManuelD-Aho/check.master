# Définition des Fonctions par Fichier

## 1. Contrôleurs

### 1.1 AbstractController.php
```php
abstract class AbstractController
{
    // Injection de dépendances
    protected ContainerInterface $container;
    protected EntityManagerInterface $em;
    protected TemplateRenderer $view;
    protected SessionInterface $session;
    
    // Méthodes utilitaires
    protected function render(string $template, array $data = []): Response;
    protected function redirect(string $url, int $status = 302): Response;
    protected function redirectToRoute(string $routeName, array $params = []): Response;
    protected function json(array $data, int $status = 200): Response;
    protected function getUser(): ?Utilisateur;
    protected function isGranted(string $permission): bool;
    protected function denyAccessUnlessGranted(string $permission): void;
    protected function addFlash(string $type, string $message): void;
    protected function getRepository(string $entityClass): ObjectRepository;
    protected function createForm(array $data, array $rules): FormValidator;
    protected function generateCsrfToken(string $tokenId): string;
    protected function validateCsrfToken(string $tokenId, string $token): bool;
}
```

### 1.2 Auth/LoginController.php
```php
class LoginController extends AbstractController
{
    public function __construct(
        private AuthenticationService $authService,
        private RateLimiterService $rateLimiter,
        private AuditService $audit
    );
    
    // GET /login - Affiche le formulaire de connexion
    public function loginForm(Request $request): Response;
    
    // POST /login - Traite la connexion
    public function login(Request $request): Response;
    
    // Méthodes privées
    private function validateCredentials(array $data): array;
    private function handleSuccessfulLogin(Utilisateur $user, Request $request): Response;
    private function handleFailedLogin(string $login, Request $request): Response;
}
```

### 1.3 Admin/Etudiant/EtudiantController.php
```php
class EtudiantController extends AbstractController
{
    public function __construct(
        private EtudiantService $etudiantService,
        private EtudiantValidator $validator,
        private MatriculeGenerator $matriculeGenerator
    );
    
    // GET /admin/etudiants - Liste paginée
    public function index(Request $request): Response;
    
    // GET /admin/etudiants/nouveau - Formulaire création
    public function create(): Response;
    
    // POST /admin/etudiants - Enregistrement
    public function store(Request $request): Response;
    
    // GET /admin/etudiants/{matricule} - Fiche détaillée
    public function show(string $matricule): Response;
    
    // GET /admin/etudiants/{matricule}/modifier - Formulaire modification
    public function edit(string $matricule): Response;
    
    // PUT /admin/etudiants/{matricule} - Mise à jour
    public function update(string $matricule, Request $request): Response;
    
    // GET /admin/etudiants/import - Formulaire import CSV
    public function importForm(): Response;
    
    // POST /admin/etudiants/import - Traitement import
    public function import(Request $request): Response;
    
    // GET /admin/etudiants/export - Export CSV
    public function export(Request $request): Response;
}
```

### 1.4 Etudiant/CandidatureController.php (Espace étudiant)
```php
class CandidatureController extends AbstractController
{
    public function __construct(
        private CandidatureService $candidatureService,
        private EntrepriseService $entrepriseService,
        private WorkflowRegistry $workflowRegistry
    );
    
    // GET /etudiant/candidature - Vue principale candidature
    public function index(): Response;
    
    // GET /etudiant/candidature/formulaire - Formulaire de saisie
    public function formulaire(): Response;
    
    // POST /etudiant/candidature/sauvegarder - Sauvegarde brouillon (AJAX)
    public function sauvegarder(Request $request): Response;
    
    // POST /etudiant/candidature/soumettre - Soumission
    public function soumettre(Request $request): Response;
    
    // GET /etudiant/candidature/recapitulatif - Vue après soumission
    public function recapitulatif(): Response;
    
    // Méthodes privées
    private function getOrCreateCandidature(): Candidature;
    private function getCandidatureForCurrentYear(): ?Candidature;
}
```

### 1.5 Etudiant/RapportController.php (Espace étudiant)
```php
class RapportController extends AbstractController
{
    public function __construct(
        private RapportService $rapportService,
        private ContentSanitizerService $sanitizer,
        private VersioningService $versioning,
        private WorkflowRegistry $workflowRegistry
    );
    
    // GET /etudiant/rapport - Vue principale (conditionnel)
    public function index(): Response;
    
    // GET /etudiant/rapport/nouveau - Choix du modèle
    public function choisirModele(): Response;
    
    // POST /etudiant/rapport/creer - Création avec modèle
    public function creer(Request $request): Response;
    
    // GET /etudiant/rapport/editeur - Éditeur de texte riche
    public function editeur(): Response;
    
    // POST /etudiant/rapport/sauvegarder - Sauvegarde auto (AJAX)
    public function sauvegarder(Request $request): Response;
    
    // GET /etudiant/rapport/informations - Métadonnées
    public function informations(): Response;
    
    // POST /etudiant/rapport/informations - Mise à jour métadonnées
    public function updateInformations(Request $request): Response;
    
    // POST /etudiant/rapport/soumettre - Soumission
    public function soumettre(Request $request): Response;
    
    // GET /etudiant/rapport/voir - Vue lecture seule
    public function voir(): Response;
    
    // GET /etudiant/rapport/telecharger - Téléchargement PDF
    public function telecharger(): Response;
    
    // Méthodes privées
    private function checkCandidatureValidee(): bool;
    private function getRapportForCurrentYear(): ?Rapport;
}
```

---

## 2. Services

### 2.1 Auth/AuthenticationService.php
```php
class AuthenticationService
{
    public function __construct(
        private UtilisateurRepository $userRepo,
        private PasswordHasherInterface $passwordHasher,
        private JwtService $jwtService,
        private TwoFactorService $twoFactorService,
        private EventDispatcherInterface $dispatcher
    );
    
    // Authentification
    public function authenticate(string $login, string $password): AuthResult;
    public function validateTwoFactor(Utilisateur $user, string $code): bool;
    public function logout(Utilisateur $user): void;
    
    // Gestion de session
    public function createSession(Utilisateur $user): string;
    public function validateSession(string $token): ?Utilisateur;
    public function refreshSession(string $token): string;
    public function invalidateSession(string $token): void;
    
    // Mot de passe
    public function hashPassword(string $plainPassword): string;
    public function verifyPassword(Utilisateur $user, string $plainPassword): bool;
    public function generateResetToken(Utilisateur $user): string;
    public function resetPassword(string $token, string $newPassword): bool;
    
    // Méthodes privées
    private function checkUserStatus(Utilisateur $user): void;
    private function incrementFailedAttempts(Utilisateur $user): void;
    private function resetFailedAttempts(Utilisateur $user): void;
}
```

### 2.2 Auth/AuthorizationService.php
```php
class AuthorizationService
{
    public function __construct(
        private PermissionRepository $permissionRepo,
        private CacheService $cache,
        private AuditService $audit
    );
    
    // Vérification des permissions
    public function isGranted(Utilisateur $user, string $permission): bool;
    public function hasPermission(int $groupeId, string $fonctionnaliteCode, string $action): bool;
    public function checkRoutePermission(Utilisateur $user, string $route, string $method): bool;
    
    // Récupération des permissions
    public function getUserPermissions(Utilisateur $user): array;
    public function getGroupPermissions(int $groupeId): array;
    
    // Cache des permissions
    public function refreshPermissionsCache(): void;
    public function clearPermissionsCache(): void;
    
    // Méthodes privées
    private function mapHttpMethodToAction(string $method): string;
    private function matchRoute(string $pattern, string $route): bool;
}
```

### 2.3 Student/EtudiantService.php
```php
class EtudiantService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EtudiantRepository $etudiantRepo,
        private MatriculeGenerator $matriculeGenerator,
        private EventDispatcherInterface $dispatcher,
        private AuditService $audit
    );
    
    // CRUD
    public function create(array $data): Etudiant;
    public function update(Etudiant $etudiant, array $data): Etudiant;
    public function deactivate(Etudiant $etudiant): void;
    
    // Recherche
    public function findByMatricule(string $matricule): ?Etudiant;
    public function findByEmail(string $email): ?Etudiant;
    public function search(array $criteria, int $page = 1, int $limit = 25): Pagerfanta;
    
    // Import/Export
    public function importFromCsv(string $filePath): ImportResult;
    public function exportToCsv(array $criteria): string;
    
    // Méthodes privées
    private function normalizeData(array $data): array;
    private function validateData(array $data): void;
    private function dispatchCreatedEvent(Etudiant $etudiant): void;
}
```

### 2.4 Stage/CandidatureService.php
```php
class CandidatureService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CandidatureRepository $candidatureRepo,
        private WorkflowRegistry $workflowRegistry,
        private EventDispatcherInterface $dispatcher,
        private EmailService $emailService
    );
    
    // Création et mise à jour
    public function createOrUpdate(Etudiant $etudiant, array $data): Candidature;
    public function saveDraft(Candidature $candidature, array $data): void;
    
    // Transitions workflow
    public function submit(Candidature $candidature): void;
    public function validate(Candidature $candidature, Utilisateur $validateur, ?string $commentaire = null): void;
    public function reject(Candidature $candidature, Utilisateur $validateur, string $motif, string $commentaire): void;
    public function resubmit(Candidature $candidature): void;
    
    // Requêtes
    public function findByEtudiantAndAnnee(Etudiant $etudiant, AnneeAcademique $annee): ?Candidature;
    public function findPendingValidation(): array;
    public function findByStatus(string $status): array;
    
    // Méthodes privées
    private function createSnapshot(Candidature $candidature, string $action): void;
    private function notifyValidateurs(Candidature $candidature): void;
    private function notifyEtudiant(Candidature $candidature, string $action): void;
}
```

### 2.5 Report/RapportService.php
```php
class RapportService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RapportRepository $rapportRepo,
        private ContentSanitizerService $sanitizer,
        private VersioningService $versioning,
        private PdfGeneratorService $pdfGenerator,
        private WorkflowRegistry $workflowRegistry,
        private EventDispatcherInterface $dispatcher
    );
    
    // Création
    public function createFromTemplate(Etudiant $etudiant, ModeleRapport $modele): Rapport;
    public function createEmpty(Etudiant $etudiant): Rapport;
    
    // Mise à jour du contenu
    public function saveContent(Rapport $rapport, string $htmlContent): void;
    public function updateMetadata(Rapport $rapport, array $data): void;
    
    // Transitions workflow
    public function submit(Rapport $rapport): void;
    public function approve(Rapport $rapport, Utilisateur $verificateur, ?string $commentaire = null): void;
    public function returnForCorrection(Rapport $rapport, Utilisateur $verificateur, string $motif, string $commentaire): void;
    public function transferToCommission(array $rapports): void;
    
    // Génération PDF
    public function generatePdf(Rapport $rapport): string;
    
    // Méthodes privées
    private function sanitizeContent(string $html): string;
    private function createVersion(Rapport $rapport, string $type): void;
    private function countWords(string $text): int;
    private function estimatePages(int $wordCount): int;
}
```

### 2.6 Commission/VoteService.php
```php
class VoteService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EvaluationRapportRepository $evaluationRepo,
        private EventDispatcherInterface $dispatcher,
        private EmailService $emailService
    );
    
    // Vote
    public function submitVote(Rapport $rapport, Utilisateur $membre, string $decision, ?string $commentaire = null): void;
    public function getVoteStatus(Rapport $rapport): array;
    public function isVoteComplete(Rapport $rapport): bool;
    
    // Calcul résultat
    public function calculateResult(Rapport $rapport): VoteResult;
    public function processVoteResult(Rapport $rapport, VoteResult $result): void;
    
    // Relance
    public function startNewVoteCycle(Rapport $rapport): void;
    
    // Méthodes privées
    private function checkMemberCanVote(Rapport $rapport, Utilisateur $membre): void;
    private function notifyVoteProgress(Rapport $rapport): void;
    private function handleUnanimousYes(Rapport $rapport): void;
    private function handleUnanimousNo(Rapport $rapport): void;
    private function handleMixedVotes(Rapport $rapport): void;
}
```

### 2.7 Soutenance/MoyenneCalculationService.php
```php
class MoyenneCalculationService
{
    public function __construct(
        private NoteRepository $noteRepo,
        private SettingsService $settings
    );
    
    // Calcul des moyennes
    public function calculateMoyenneS1M2(Etudiant $etudiant, AnneeAcademique $annee): BigDecimal;
    public function calculateNoteSoutenance(Soutenance $soutenance): BigDecimal;
    
    // Calcul final selon type de PV
    public function calculateMoyenneFinaleStandard(
        BigDecimal $moyenneM1,
        BigDecimal $moyenneS1M2,
        BigDecimal $noteMemoire
    ): BigDecimal;
    
    public function calculateMoyenneFinaleSimplifiee(
        BigDecimal $moyenneM1,
        BigDecimal $noteMemoire
    ): BigDecimal;
    
    // Détermination mention
    public function determineMention(BigDecimal $moyenneFinale): Mention;
    public function determineDecision(BigDecimal $moyenneFinale): string;
    
    // Méthodes privées
    private function round(BigDecimal $value): BigDecimal;
    private function getMentionSeuils(): array;
}
```

### 2.8 Document/PdfGeneratorService.php
```php
class PdfGeneratorService
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
        private SettingsService $settings,
        private string $storagePath
    );
    
    // Génération générique
    public function generate(string $template, array $data, string $filename): GeneratedDocument;
    public function preview(string $template, array $data): string;
    
    // Configuration PDF
    protected function createPdf(string $orientation = 'P', string $size = 'A4'): TCPDF;
    protected function addHeader(TCPDF $pdf): void;
    protected function addFooter(TCPDF $pdf): void;
    
    // Stockage
    protected function store(string $content, string $type, string $filename): string;
    protected function generateReference(string $type): string;
    
    // Utilitaires
    protected function numberToWords(int $number): string;
    protected function formatDate(DateTime $date): string;
}
```

### 2.9 Email/EmailService.php
```php
class EmailService
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
        private SettingsService $settings,
        private EncryptionService $encryption,
        private LoggerInterface $logger
    );
    
    // Envoi
    public function send(string $to, string $subject, string $body, array $attachments = []): void;
    public function sendTemplate(string $to, string $templateCode, array $data): void;
    public function sendBulk(array $recipients, string $subject, string $body): void;
    
    // Test
    public function testConnection(): bool;
    public function sendTestEmail(string $to): void;
    
    // Templates
    public function renderTemplate(string $templateCode, array $data): string;
    public function getTemplateVariables(string $templateCode): array;
    
    // Méthodes privées
    private function createMailer(): PHPMailer;
    private function logSentEmail(string $to, string $subject): void;
}
```

---

## 3. Repositories

### 3.1 AbstractRepository.php
```php
abstract class AbstractRepository
{
    protected EntityManagerInterface $em;
    protected string $entityClass;
    
    // CRUD de base
    public function find(int $id): ?object;
    public function findAll(): array;
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
    public function findOneBy(array $criteria): ?object;
    public function save(object $entity): void;
    public function remove(object $entity): void;
    public function flush(): void;
    
    // Pagination
    public function paginate(QueryBuilder $qb, int $page = 1, int $limit = 25): Pagerfanta;
    
    // Requêtes
    protected function createQueryBuilder(string $alias): QueryBuilder;
}
```

### 3.2 Student/EtudiantRepository.php
```php
class EtudiantRepository extends AbstractRepository
{
    // Recherche spécifique
    public function findByMatricule(string $matricule): ?Etudiant;
    public function findByEmail(string $email): ?Etudiant;
    public function findByPromotion(string $promotion): array;
    public function findByFiliere(int $filiereId): array;
    
    // Recherche avancée
    public function search(array $criteria): QueryBuilder;
    public function findWithInscription(AnneeAcademique $annee): array;
    public function findWithoutUser(): array;
    
    // Statistiques
    public function countByPromotion(): array;
    public function countByFiliere(): array;
    public function countActive(): int;
    
    // Méthodes privées
    private function applySearchCriteria(QueryBuilder $qb, array $criteria): void;
}
```

---

## 4. Validators

### 4.1 EtudiantValidator.php
```php
class EtudiantValidator extends AbstractValidator
{
    // Validation création
    public function validateCreate(array $data): ValidationResult;
    
    // Validation mise à jour
    public function validateUpdate(Etudiant $etudiant, array $data): ValidationResult;
    
    // Règles de validation
    protected function getRules(): array;
    /*
    [
        'nom' => v::stringType()->length(2, 100),
        'prenom' => v::stringType()->length(2, 100),
        'email' => v::email()->callback([$this, 'isUniqueEmail']),
        'date_naissance' => v::date()->between('-60 years', '-18 years'),
        'genre' => v::in(['M', 'F']),
        'promotion' => v::regex('/^\d{4}-\d{4}$/'),
        'id_filiere' => v::intVal()->positive(),
    ]
    */
    
    // Callbacks personnalisés
    public function isUniqueEmail(string $email, ?int $excludeId = null): bool;
    public function isValidPromotion(string $promotion): bool;
}
```

---

## 5. Middlewares

### 5.1 AuthenticationMiddleware.php
```php
class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthenticationService $authService,
        private array $publicRoutes = []
    );
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
    
    // Méthodes privées
    private function isPublicRoute(string $path): bool;
    private function getTokenFromRequest(ServerRequestInterface $request): ?string;
    private function redirectToLogin(): ResponseInterface;
}
```

### 5.2 PermissionMiddleware.php
```php
class PermissionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthorizationService $authService,
        private RouteActionRepository $routeActionRepo
    );
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
    
    // Méthodes privées
    private function matchRoute(string $path, string $method): ?RouteAction;
    private function denyAccess(): ResponseInterface;
    private function logUnauthorizedAccess(Utilisateur $user, string $route): void;
}
```

---

## 6. Event Listeners

### 6.1 Stage/NotifyCandidatureListener.php
```php
class NotifyCandidatureListener implements EventSubscriberInterface
{
    public function __construct(
        private EmailService $emailService,
        private SettingsService $settings
    );
    
    public static function getSubscribedEvents(): array;
    /*
    [
        CandidatureSubmittedEvent::class => 'onCandidatureSubmitted',
        CandidatureValidatedEvent::class => 'onCandidatureValidated',
        CandidatureRejectedEvent::class => 'onCandidatureRejected',
    ]
    */
    
    public function onCandidatureSubmitted(CandidatureSubmittedEvent $event): void;
    public function onCandidatureValidated(CandidatureValidatedEvent $event): void;
    public function onCandidatureRejected(CandidatureRejectedEvent $event): void;
}
```

---

## 7. Helpers

### 7.1 DateHelper.php
```php
class DateHelper
{
    // Formatage
    public static function format(DateTime $date, string $format = 'd/m/Y'): string;
    public static function formatFull(DateTime $date): string;
    public static function formatTime(DateTime $date): string;
    
    // Calculs
    public static function diffInDays(DateTime $start, DateTime $end): int;
    public static function diffInMonths(DateTime $start, DateTime $end): int;
    public static function addDays(DateTime $date, int $days): DateTime;
    public static function addMonths(DateTime $date, int $months): DateTime;
    
    // Vérifications
    public static function isWeekend(DateTime $date): bool;
    public static function isBusinessDay(DateTime $date): bool;
    public static function isInRange(DateTime $date, DateTime $start, DateTime $end): bool;
    
    // Création
    public static function now(): DateTime;
    public static function today(): DateTime;
    public static function parse(string $date, string $format = 'Y-m-d'): DateTime;
}
```

### 7.2 NumberHelper.php
```php
class NumberHelper
{
    // Formatage
    public static function format(float $number, int $decimals = 2): string;
    public static function formatMoney(float $amount, string $currency = 'FCFA'): string;
    public static function toWords(int $number, string $lang = 'fr'): string;
    
    // Calculs
    public static function round(float $number, int $precision = 2): float;
    public static function percentage(float $value, float $total): float;
    public static function average(array $numbers): float;
    
    // Validation
    public static function isInRange(float $value, float $min, float $max): bool;
}
```
