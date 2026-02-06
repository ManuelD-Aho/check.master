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
use App\Repository\User\UtilisateurRepository;
use App\Repository\User\GroupeUtilisateurRepository;
use App\Repository\User\PermissionRepository;
use App\Repository\System\AppSettingRepository;
use App\Repository\System\FonctionnaliteRepository;

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

    App::class => function ($c) {
        return new App($c);
    },
]);

return $containerBuilder->build();
