<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use App\App;
use App\Service\System\SettingsService;
use App\Service\System\CacheService;
use App\Service\System\EncryptionService;
use App\Service\System\AuditService;
use App\Service\System\MenuService;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Auth\PasswordService;
use App\Service\Auth\JwtService;
use App\Service\Auth\TwoFactorService;
use App\Service\Auth\RateLimiterService;
use App\Service\Email\EmailService;
use App\Service\Email\TemplateRenderer;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\User\UtilisateurRepository;
use App\Repository\User\GroupeUtilisateurRepository;
use App\Repository\User\PermissionRepository;
use App\Repository\User\TypeUtilisateurRepository;
use App\Repository\Student\EtudiantRepository;
use App\Repository\Student\InscriptionRepository;
use App\Repository\Student\VersementRepository;
use App\Repository\Student\EcheanceRepository;
use App\Repository\Academic\AnneeAcademiqueRepository;
use App\Repository\Academic\NiveauEtudeRepository;
use App\Repository\Academic\FiliereRepository;
use App\Repository\Academic\SemestreRepository;
use App\Repository\Academic\UniteEnseignementRepository;
use App\Repository\Academic\ElementConstitutifRepository;
use App\Repository\Academic\NoteRepository;
use App\Repository\Staff\EnseignantRepository;
use App\Repository\Staff\GradeRepository;
use App\Repository\Staff\FonctionRepository;
use App\Repository\Staff\SpecialiteRepository;
use App\Repository\Staff\PersonnelAdministratifRepository;
use App\Repository\Stage\CandidatureRepository;
use App\Repository\Stage\EntrepriseRepository;
use App\Repository\Stage\HistoriqueCandidatureRepository;
use App\Repository\Stage\InformationStageRepository;
use App\Repository\Stage\MotifRejetCandidatureRepository;
use App\Repository\Report\RapportRepository;
use App\Repository\Report\VersionRapportRepository;
use App\Repository\Report\CommentaireRapportRepository;
use App\Repository\Report\ValidationRapportRepository;
use App\Repository\Report\ModeleRapportRepository;
use App\Repository\Commission\SessionCommissionRepository;
use App\Repository\Commission\MembreCommissionRepository;
use App\Repository\Commission\CompteRenduCommissionRepository;
use App\Repository\Commission\CompteRenduRapportRepository;
use App\Repository\Commission\EvaluationRapportRepository;
use App\Repository\Commission\AffectationEncadrantRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use App\Repository\Soutenance\JuryRepository;
use App\Repository\Soutenance\CompositionJuryRepository;
use App\Repository\Soutenance\RoleJuryRepository;
use App\Repository\Soutenance\NoteSoutenanceRepository;
use App\Repository\Soutenance\ResultatFinalRepository;
use App\Repository\Soutenance\AptitudeSoutenanceRepository;
use App\Repository\Soutenance\CritereEvaluationRepository;
use App\Repository\Soutenance\BaremeCritereRepository;
use App\Repository\Soutenance\SalleRepository;
use App\Repository\Soutenance\MentionRepository;
use App\Repository\System\AppSettingRepository;
use App\Repository\System\FonctionnaliteRepository;
use App\Repository\System\AuditLogRepository;
use App\Service\Student\EtudiantService;
use App\Service\Student\InscriptionService;
use App\Service\Stage\CandidatureService;
use App\Service\Stage\EntrepriseService;
use App\Service\Report\RapportService;
use App\Service\Commission\CommissionService;
use App\Service\Commission\VoteService;
use App\Service\Commission\AffectationService;
use App\Service\Soutenance\JuryService;
use App\Service\Soutenance\SoutenanceService;
use App\Service\Document\DocumentGeneratorService;
use App\Service\Document\RecuPaiementGenerator;
use App\Service\Document\AttestationInscriptionGenerator;
use App\Service\Document\AttestationStageGenerator;
use App\Service\Document\Annexe1Generator;
use App\Service\Document\Annexe2Generator;
use App\Service\Document\Annexe3Generator;
use App\Service\Document\CompteRenduCommissionGenerator;
use App\Service\Document\FicheNotationGenerator;
use App\Service\Document\PvSoutenanceGenerator;
use App\Service\Document\BulletinGeneratorService;
use App\Service\Document\PvFinalGeneratorService;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    'settings' => function () {
        return [
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
                'secret' => $_ENV['APP_SECRET'] ?? '',
                'url' => $_ENV['APP_URL'] ?? 'http://localhost',
            ],
            'database' => [
                'driver' => 'pdo_mysql',
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => (int)($_ENV['DB_PORT'] ?? 3306),
                'dbname' => $_ENV['DB_NAME'] ?? 'miage_platform',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASS'] ?? '',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            ],
            'jwt' => [
                'secret' => $_ENV['JWT_SECRET'] ?? '',
                'ttl' => 28800,
            ],
            'encryption' => [
                'key' => $_ENV['ENCRYPTION_KEY'] ?? '',
            ],
            'smtp' => [
                'host' => $_ENV['SMTP_HOST'] ?? '',
                'port' => (int)($_ENV['SMTP_PORT'] ?? 587),
                'username' => $_ENV['SMTP_USERNAME'] ?? '',
                'password' => $_ENV['SMTP_PASSWORD'] ?? '',
                'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
                'from_email' => $_ENV['EMAIL_FROM'] ?? '',
                'from_name' => $_ENV['EMAIL_FROM_NAME'] ?? '',
            ],
            'paths' => [
                'storage' => __DIR__ . '/../' . ($_ENV['STORAGE_PATH'] ?? 'storage'),
                'logs' => __DIR__ . '/../' . ($_ENV['LOGS_PATH'] ?? 'storage/logs'),
                'cache' => __DIR__ . '/../' . ($_ENV['CACHE_PATH'] ?? 'storage/cache'),
                'sessions' => __DIR__ . '/../' . ($_ENV['SESSIONS_PATH'] ?? 'storage/sessions'),
                'documents' => __DIR__ . '/../' . ($_ENV['DOCUMENTS_PATH'] ?? 'storage/documents'),
                'uploads' => __DIR__ . '/../' . ($_ENV['UPLOADS_PATH'] ?? 'storage/uploads'),
                'templates' => __DIR__ . '/../templates',
            ],
            'security' => [
                'session_timeout' => (int)($_ENV['SESSION_TIMEOUT'] ?? 480),
                'password_min_length' => (int)($_ENV['PASSWORD_MIN_LENGTH'] ?? 8),
                'login_max_attempts' => (int)($_ENV['LOGIN_MAX_ATTEMPTS'] ?? 5),
                'login_lockout_duration' => (int)($_ENV['LOGIN_LOCKOUT_DURATION'] ?? 15),
            ],
        ];
    },

    Logger::class => function ($c) {
        $settings = $c->get('settings');
        $logger = new Logger('app');
        $logger->pushHandler(new RotatingFileHandler(
            $settings['paths']['logs'] . '/app.log',
            30,
            Logger::DEBUG
        ));
        return $logger;
    },

    'audit_logger' => function ($c) {
        $settings = $c->get('settings');
        $logger = new Logger('audit');
        $logger->pushHandler(new RotatingFileHandler(
            $settings['paths']['logs'] . '/audit.log',
            365,
            Logger::INFO
        ));
        return $logger;
    },

    EventDispatcher::class => function () {
        return new EventDispatcher();
    },

    EntityManager::class => function ($c) {
        $settings = $c->get('settings');
        
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../src/Entity'],
            isDevMode: $settings['app']['debug'],
        );
        
        $connection = DriverManager::getConnection($settings['database'], $config);
        
        return new EntityManager($connection, $config);
    },

    PasswordHasherFactory::class => function () {
        return new PasswordHasherFactory([
            'default' => [
                'algorithm' => 'argon2id',
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 1,
            ],
        ]);
    },

    CacheService::class => function ($c) {
        $settings = $c->get('settings');
        return new CacheService($settings['paths']['cache']);
    },

    EncryptionService::class => function ($c) {
        $settings = $c->get('settings');
        return new EncryptionService($settings['encryption']['key']);
    },

    TemplateRenderer::class => function ($c) {
        $settings = $c->get('settings');
        return new TemplateRenderer($settings['paths']['templates']);
    },

    UtilisateurRepository::class => function ($c) {
        return new UtilisateurRepository($c->get(EntityManager::class));
    },

    GroupeUtilisateurRepository::class => function ($c) {
        return new GroupeUtilisateurRepository($c->get(EntityManager::class));
    },

    PermissionRepository::class => function ($c) {
        return new PermissionRepository($c->get(EntityManager::class));
    },

    AppSettingRepository::class => function ($c) {
        return new AppSettingRepository($c->get(EntityManager::class));
    },

    FonctionnaliteRepository::class => function ($c) {
        return new FonctionnaliteRepository($c->get(EntityManager::class));
    },

    SettingsService::class => function ($c) {
        return new SettingsService(
            $c->get(AppSettingRepository::class),
            $c->get(CacheService::class),
            $c->get(EncryptionService::class)
        );
    },

    AuditService::class => function ($c) {
        return new AuditService(
            $c->get(EntityManager::class),
            $c->get('audit_logger')
        );
    },

    MenuService::class => function ($c) {
        return new MenuService(
            $c->get(FonctionnaliteRepository::class),
            $c->get(CacheService::class)
        );
    },

    JwtService::class => function ($c) {
        $settings = $c->get('settings');
        return new JwtService($settings['jwt']['secret'], $settings['jwt']['ttl']);
    },

    RateLimiterService::class => function ($c) {
        $settings = $c->get('settings');
        return new RateLimiterService(
            $c->get(EntityManager::class),
            $settings['security']['login_max_attempts'],
            $settings['security']['login_lockout_duration']
        );
    },

    PasswordService::class => function ($c) {
        return new PasswordService($c->get(PasswordHasherFactory::class));
    },

    TwoFactorService::class => function ($c) {
        return new TwoFactorService($c->get(EncryptionService::class));
    },

    AuthenticationService::class => function ($c) {
        return new AuthenticationService(
            $c->get(UtilisateurRepository::class),
            $c->get(PasswordService::class),
            $c->get(JwtService::class),
            $c->get(TwoFactorService::class),
            $c->get(EventDispatcher::class)
        );
    },

    AuthorizationService::class => function ($c) {
        return new AuthorizationService(
            $c->get(PermissionRepository::class),
            $c->get(CacheService::class),
            $c->get(AuditService::class)
        );
    },

    EmailService::class => function ($c) {
        $settings = $c->get('settings');
        return new EmailService(
            $c->get(TemplateRenderer::class),
            $c->get(SettingsService::class),
            $c->get(EncryptionService::class),
            $c->get(Logger::class),
            $settings['smtp']
        );
    },

    // EntityManagerInterface alias
    EntityManagerInterface::class => function ($c) {
        return $c->get(EntityManager::class);
    },

    // User repositories
    TypeUtilisateurRepository::class => function ($c) {
        return new TypeUtilisateurRepository($c->get(EntityManager::class));
    },

    // Student repositories
    EtudiantRepository::class => function ($c) {
        return new EtudiantRepository($c->get(EntityManager::class));
    },

    InscriptionRepository::class => function ($c) {
        return new InscriptionRepository($c->get(EntityManager::class));
    },

    VersementRepository::class => function ($c) {
        return new VersementRepository($c->get(EntityManager::class));
    },

    EcheanceRepository::class => function ($c) {
        return new EcheanceRepository($c->get(EntityManager::class));
    },

    // Academic repositories
    AnneeAcademiqueRepository::class => function ($c) {
        return new AnneeAcademiqueRepository($c->get(EntityManager::class));
    },

    NiveauEtudeRepository::class => function ($c) {
        return new NiveauEtudeRepository($c->get(EntityManager::class));
    },

    FiliereRepository::class => function ($c) {
        return new FiliereRepository($c->get(EntityManager::class));
    },

    SemestreRepository::class => function ($c) {
        return new SemestreRepository($c->get(EntityManager::class));
    },

    UniteEnseignementRepository::class => function ($c) {
        return new UniteEnseignementRepository($c->get(EntityManager::class));
    },

    ElementConstitutifRepository::class => function ($c) {
        return new ElementConstitutifRepository($c->get(EntityManager::class));
    },

    NoteRepository::class => function ($c) {
        return new NoteRepository($c->get(EntityManager::class));
    },

    // Staff repositories
    EnseignantRepository::class => function ($c) {
        return new EnseignantRepository($c->get(EntityManager::class));
    },

    GradeRepository::class => function ($c) {
        return new GradeRepository($c->get(EntityManager::class));
    },

    FonctionRepository::class => function ($c) {
        return new FonctionRepository($c->get(EntityManager::class));
    },

    SpecialiteRepository::class => function ($c) {
        return new SpecialiteRepository($c->get(EntityManager::class));
    },

    PersonnelAdministratifRepository::class => function ($c) {
        return new PersonnelAdministratifRepository($c->get(EntityManager::class));
    },

    // Stage repositories
    CandidatureRepository::class => function ($c) {
        return new CandidatureRepository($c->get(EntityManager::class));
    },

    EntrepriseRepository::class => function ($c) {
        return new EntrepriseRepository($c->get(EntityManager::class));
    },

    HistoriqueCandidatureRepository::class => function ($c) {
        return new HistoriqueCandidatureRepository($c->get(EntityManager::class));
    },

    InformationStageRepository::class => function ($c) {
        return new InformationStageRepository($c->get(EntityManager::class));
    },

    MotifRejetCandidatureRepository::class => function ($c) {
        return new MotifRejetCandidatureRepository($c->get(EntityManager::class));
    },

    // Report repositories
    RapportRepository::class => function ($c) {
        return new RapportRepository($c->get(EntityManager::class));
    },

    VersionRapportRepository::class => function ($c) {
        return new VersionRapportRepository($c->get(EntityManager::class));
    },

    CommentaireRapportRepository::class => function ($c) {
        return new CommentaireRapportRepository($c->get(EntityManager::class));
    },

    ValidationRapportRepository::class => function ($c) {
        return new ValidationRapportRepository($c->get(EntityManager::class));
    },

    ModeleRapportRepository::class => function ($c) {
        return new ModeleRapportRepository($c->get(EntityManager::class));
    },

    // Commission repositories
    SessionCommissionRepository::class => function ($c) {
        return new SessionCommissionRepository($c->get(EntityManager::class));
    },

    MembreCommissionRepository::class => function ($c) {
        return new MembreCommissionRepository($c->get(EntityManager::class));
    },

    CompteRenduCommissionRepository::class => function ($c) {
        return new CompteRenduCommissionRepository($c->get(EntityManager::class));
    },

    CompteRenduRapportRepository::class => function ($c) {
        return new CompteRenduRapportRepository($c->get(EntityManager::class));
    },

    EvaluationRapportRepository::class => function ($c) {
        return new EvaluationRapportRepository($c->get(EntityManager::class));
    },

    AffectationEncadrantRepository::class => function ($c) {
        return new AffectationEncadrantRepository($c->get(EntityManager::class));
    },

    // Soutenance repositories
    SoutenanceRepository::class => function ($c) {
        return new SoutenanceRepository($c->get(EntityManager::class));
    },

    JuryRepository::class => function ($c) {
        return new JuryRepository($c->get(EntityManager::class));
    },

    CompositionJuryRepository::class => function ($c) {
        return new CompositionJuryRepository($c->get(EntityManager::class));
    },

    RoleJuryRepository::class => function ($c) {
        return new RoleJuryRepository($c->get(EntityManager::class));
    },

    NoteSoutenanceRepository::class => function ($c) {
        return new NoteSoutenanceRepository($c->get(EntityManager::class));
    },

    ResultatFinalRepository::class => function ($c) {
        return new ResultatFinalRepository($c->get(EntityManager::class));
    },

    AptitudeSoutenanceRepository::class => function ($c) {
        return new AptitudeSoutenanceRepository($c->get(EntityManager::class));
    },

    CritereEvaluationRepository::class => function ($c) {
        return new CritereEvaluationRepository($c->get(EntityManager::class));
    },

    BaremeCritereRepository::class => function ($c) {
        return new BaremeCritereRepository($c->get(EntityManager::class));
    },

    SalleRepository::class => function ($c) {
        return new SalleRepository($c->get(EntityManager::class));
    },

    MentionRepository::class => function ($c) {
        return new MentionRepository($c->get(EntityManager::class));
    },

    // System repositories
    AuditLogRepository::class => function ($c) {
        return new AuditLogRepository($c->get(EntityManager::class));
    },

    // Student services
    EtudiantService::class => function ($c) {
        return new EtudiantService(
            $c->get(EntityManagerInterface::class),
            $c->get(InscriptionRepository::class)
        );
    },

    InscriptionService::class => function ($c) {
        return new InscriptionService(
            $c->get(EntityManagerInterface::class),
            $c->get(InscriptionRepository::class)
        );
    },

    // Stage services
    CandidatureService::class => function ($c) {
        return new CandidatureService(
            $c->get(EntityManagerInterface::class),
            $c->get(CandidatureRepository::class),
            $c->get(HistoriqueCandidatureRepository::class)
        );
    },

    EntrepriseService::class => function ($c) {
        return new EntrepriseService(
            $c->get(EntityManagerInterface::class),
            $c->get(EntrepriseRepository::class)
        );
    },

    // Report services
    RapportService::class => function ($c) {
        return new RapportService(
            $c->get(EntityManagerInterface::class),
            $c->get(RapportRepository::class),
            $c->get(VersionRapportRepository::class),
            $c->get(CommentaireRapportRepository::class),
            $c->get(ValidationRapportRepository::class)
        );
    },

    // Commission services
    CommissionService::class => function ($c) {
        return new CommissionService(
            $c->get(EntityManagerInterface::class),
            $c->get(SessionCommissionRepository::class),
            $c->get(MembreCommissionRepository::class),
            $c->get(RapportRepository::class)
        );
    },

    VoteService::class => function ($c) {
        return new VoteService(
            $c->get(EntityManagerInterface::class),
            $c->get(EvaluationRapportRepository::class)
        );
    },

    AffectationService::class => function ($c) {
        return new AffectationService(
            $c->get(EntityManagerInterface::class),
            $c->get(AffectationEncadrantRepository::class)
        );
    },

    // Soutenance services
    JuryService::class => function ($c) {
        return new JuryService(
            $c->get(EntityManagerInterface::class),
            $c->get(JuryRepository::class),
            $c->get(CompositionJuryRepository::class),
            $c->get(RoleJuryRepository::class)
        );
    },

    SoutenanceService::class => function ($c) {
        return new SoutenanceService(
            $c->get(EntityManagerInterface::class),
            $c->get(SoutenanceRepository::class),
            $c->get(NoteSoutenanceRepository::class),
            $c->get(ResultatFinalRepository::class)
        );
    },

    // Document generators
    RecuPaiementGenerator::class => function ($c) {
        $settings = $c->get('settings');
        return new RecuPaiementGenerator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    AttestationInscriptionGenerator::class => function ($c) {
        $settings = $c->get('settings');
        return new AttestationInscriptionGenerator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    AttestationStageGenerator::class => function ($c) {
        $settings = $c->get('settings');
        return new AttestationStageGenerator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    Annexe1Generator::class => function ($c) {
        $settings = $c->get('settings');
        return new Annexe1Generator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    Annexe2Generator::class => function ($c) {
        $settings = $c->get('settings');
        return new Annexe2Generator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    Annexe3Generator::class => function ($c) {
        $settings = $c->get('settings');
        return new Annexe3Generator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    CompteRenduCommissionGenerator::class => function ($c) {
        $settings = $c->get('settings');
        return new CompteRenduCommissionGenerator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    FicheNotationGenerator::class => function ($c) {
        $settings = $c->get('settings');
        return new FicheNotationGenerator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    PvSoutenanceGenerator::class => function ($c) {
        $settings = $c->get('settings');
        return new PvSoutenanceGenerator(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    BulletinGeneratorService::class => function ($c) {
        $settings = $c->get('settings');
        return new BulletinGeneratorService(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    PvFinalGeneratorService::class => function ($c) {
        $settings = $c->get('settings');
        return new PvFinalGeneratorService(
            $c->get(SettingsService::class),
            $settings['paths']['documents']
        );
    },

    DocumentGeneratorService::class => function ($c) {
        $settings = $c->get('settings');
        return new DocumentGeneratorService(
            $c->get(SettingsService::class),
            $settings['paths']['documents'],
            $c->get(RecuPaiementGenerator::class),
            $c->get(AttestationInscriptionGenerator::class),
            $c->get(AttestationStageGenerator::class),
            $c->get(Annexe1Generator::class),
            $c->get(Annexe2Generator::class),
            $c->get(Annexe3Generator::class),
            $c->get(CompteRenduCommissionGenerator::class),
            $c->get(FicheNotationGenerator::class),
            $c->get(PvSoutenanceGenerator::class),
            $c->get(BulletinGeneratorService::class),
            $c->get(PvFinalGeneratorService::class)
        );
    },

    App::class => function ($c) {
        return new App($c);
    },
]);

return $containerBuilder->build();
