# PRD Technique Global - Intégration des Bibliothèques

## 1. Vue d'ensemble de la stack technique

### 1.1 Environnement cible
- **PHP** : 8.4
- **Base de données** : MySQL 8.0+
- **Serveur** : Apache (mutualisé, sans SSH)
- **Frontend** : HTML5, CSS3, JavaScript ES6+, AJAX

### 1.2 Contraintes hébergement mutualisé
- Pas de CLI (ligne de commande)
- Pas de workers/queues en arrière-plan
- Upload via FTP uniquement
- Pas de Composer en production → vendor commité
- Pas de Redis/Memcached → cache fichiers

---

## 2. Catalogue complet des bibliothèques

### 2.1 Framework & Routage

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `nikic/fast-route` | ^1.3 | Moteur de routage ultra-rapide | Core (toutes les routes) |
| `middlewares/fast-route` | ^2.0 | Pont PSR-15 pour FastRoute | Core (middleware stack) |
| `oscarotero/middleland` | ^1.0 | Gestionnaire de middlewares | Core (pipeline requêtes) |
| `psr/http-server-middleware` | ^1.0 | Standard PSR-15 | Core (interfaces) |

**Intégration** :
```php
// index.php - Point d'entrée
$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->get('/login', [AuthController::class, 'loginForm']);
    $r->post('/login', [AuthController::class, 'login']);
    // ... routes chargées depuis config
});

$middlewares = new Middleland([
    new SessionMiddleware(),
    new CsrfMiddleware(),
    new AuthMiddleware(),
    new PermissionMiddleware(),
    new FastRouteMiddleware($dispatcher),
]);

$response = $middlewares->dispatch($request);
```

### 2.2 Fondations HTTP & API

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `symfony/http-foundation` | ^7.0 | Manipulation Request/Response | Core |
| `nyholm/psr7` | ^1.8 | Implémentation PSR-7 | Core (messages HTTP) |
| `symfony/http-client` | ^7.0 | Client HTTP pour APIs externes | Module 7 (envoi emails via API) |
| `league/uri` | ^7.0 | Manipulation des URIs | Core (routage, redirections) |

**Intégration** :
```php
// Création de la requête PSR-7 depuis les globales
$psr17Factory = new Nyholm\Psr7\Factory\Psr17Factory();
$creator = new Nyholm\Psr7Server\ServerRequestCreator($psr17Factory, ...);
$request = $creator->fromGlobals();

// Manipulation des réponses
$response = $psr17Factory->createResponse(200);
$response = $response->withHeader('Content-Type', 'application/json');
```

### 2.3 Gestion des Processus & Logique

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `symfony/workflow` | ^7.0 | Machine à états | Modules 3, 4, 5, 6 (candidature, rapport, commission, soutenance) |
| `symfony/expression-language` | ^7.0 | Règles métier configurables | Module 8 (conditions dynamiques) |
| `symfony/dependency-injection` | ^7.0 | Conteneur DI | Core (injection de dépendances) |
| `symfony/event-dispatcher` | ^7.0 | Événements découplés | Tous modules (notifications) |
| `symfony/options-resolver` | ^7.0 | Validation configurations | Module 8 (paramétrage) |
| `opis/closure` | ^4.0 | Sérialisation closures | Module 8 (règles dynamiques) |

**Intégration Workflow** :
```php
// config/workflows.php
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;

$definitionBuilder = new DefinitionBuilder();
$definition = $definitionBuilder
    ->addPlaces(['brouillon', 'soumis', 'valide', 'rejete'])
    ->addTransition(new Transition('soumettre', 'brouillon', 'soumis'))
    ->addTransition(new Transition('valider', 'soumis', 'valide'))
    ->addTransition(new Transition('rejeter', 'soumis', 'rejete'))
    ->build();

$markingStore = new MethodMarkingStore(true, 'statut');
$workflow = new Workflow($definition, $markingStore);
```

**Intégration Event Dispatcher** :
```php
// Listener pour les transitions
$dispatcher->addListener('workflow.candidature.completed.soumettre', function (Event $event) {
    $candidature = $event->getSubject();
    $this->emailService->notifyValidateurs($candidature);
    $this->auditService->log('candidature.soumise', $candidature);
});
```

### 2.4 Sécurité & Protection

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `symfony/security-core` | ^7.0 | Auth/Autorisation | Module 1 |
| `symfony/security-http` | ^7.0 | Pare-feu, guards | Module 1 |
| `symfony/password-hasher` | ^7.0 | Hachage Argon2id | Module 1 |
| `symfony/security-csrf` | ^7.0 | Protection CSRF | Core (tous formulaires) |
| `lcobucci/jwt` | ^5.0 | Tokens JWT | Module 1 (sessions API) |
| `spomky-labs/otphp` | ^11.0 | 2FA (TOTP) | Module 1 |
| `symfony/rate-limiter` | ^7.0 | Anti brute-force | Module 1 |
| `defuse/php-encryption` | ^2.4 | Chiffrement AES | Modules 1, 8 (données sensibles) |
| `ezyang/htmlpurifier` | ^4.17 | Nettoyage XSS | Module 4 (éditeur rapport) |

**Intégration Password Hasher** :
```php
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

$factory = new PasswordHasherFactory([
    User::class => ['algorithm' => 'argon2id'],
]);
$hasher = $factory->getPasswordHasher(new User());
$hash = $hasher->hash($plainPassword);
$isValid = $hasher->verify($hash, $plainPassword);
```

**Intégration JWT** :
```php
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

$config = Configuration::forSymmetricSigner(
    new Sha256(),
    InMemory::plainText($_ENV['JWT_SECRET'])
);

$token = $config->builder()
    ->issuedBy('miage-app')
    ->permittedFor('miage-app')
    ->issuedAt(new DateTimeImmutable())
    ->expiresAt((new DateTimeImmutable())->modify('+8 hours'))
    ->withClaim('user_id', $user->getId())
    ->getToken($config->signer(), $config->signingKey());
```

**Intégration 2FA** :
```php
use OTPHP\TOTP;

// Création du secret
$totp = TOTP::create();
$secret = $totp->getSecret();

// Génération QR code
$qrUri = $totp->getProvisioningUri();

// Vérification du code
$isValid = $totp->verify($userCode, time(), 1); // tolérance ±1
```

**Intégration Encryption** :
```php
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

$key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);
$encrypted = Crypto::encrypt($sensitiveData, $key);
$decrypted = Crypto::decrypt($encrypted, $key);
```

### 2.5 Base de Données & Cache

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `doctrine/orm` | ^3.0 | ORM principal | Tous modules |
| `doctrine/dbal` | ^4.0 | Couche abstraction DB | Core |
| `robmorgan/phinx` | ^0.14 | Migrations DB | Core (déploiement) |
| `psr/simple-cache` | ^3.0 | Interface cache | Core |
| `laravel/scout` | ^10.0 | Recherche plein texte | Module 2 (recherche étudiants) |

**Intégration Doctrine ORM** :
```php
// config/doctrine.php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/../src/Entity'],
    isDevMode: $_ENV['APP_ENV'] === 'dev',
);

$connection = [
    'driver' => 'pdo_mysql',
    'host' => $_ENV['DB_HOST'],
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'charset' => 'utf8mb4',
];

$entityManager = EntityManager::create($connection, $config);
```

**Intégration Phinx (Migrations)** :
```php
// phinx.php
return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinx_migrations',
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'pass' => $_ENV['DB_PASS'],
            'charset' => 'utf8mb4',
        ],
    ],
];
```

**Intégration Cache (fichiers)** :
```php
// Implémentation PSR-16 simple pour hébergement mutualisé
class FileCache implements Psr\SimpleCache\CacheInterface
{
    private string $cacheDir = '/storage/cache/';
    
    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->cacheDir . md5($key) . '.cache';
        if (!file_exists($file)) return $default;
        
        $data = unserialize(file_get_contents($file));
        if ($data['expires'] < time()) {
            unlink($file);
            return $default;
        }
        return $data['value'];
    }
}
```

### 2.6 Utilitaires Métier

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `respect/validation` | ^2.3 | Validation données | Tous modules (formulaires) |
| `egulias/email-validator` | ^4.0 | Validation emails stricte | Modules 1, 2, 3 |
| `nesbot/carbon` | ^3.0 | Manipulation dates | Tous modules |
| `brick/math` | ^0.12 | Calculs précis | Modules 2, 6 (notes, moyennes) |
| `symfony/string` | ^7.0 | Manipulation textes | Modules 2, 4 (nettoyage) |

**Intégration Validation** :
```php
use Respect\Validation\Validator as v;

$validator = v::key('email', v::email())
    ->key('nom', v::stringType()->length(2, 100))
    ->key('date_naissance', v::date()->between('-60 years', '-18 years'));

try {
    $validator->assert($data);
} catch (NestedValidationException $e) {
    $errors = $e->getMessages();
}
```

**Intégration Brick Math** :
```php
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

// Calcul de moyenne pondérée avec précision
$moyenneM1 = BigDecimal::of('12.50');
$moyenneS1 = BigDecimal::of('14.33');
$noteMem = BigDecimal::of('17.50');

$numerateur = $moyenneM1->multipliedBy(2)
    ->plus($moyenneS1->multipliedBy(3))
    ->plus($noteMem->multipliedBy(3));

$moyenneFinale = $numerateur->dividedBy(8, 2, RoundingMode::HALF_UP);
// Résultat: 14.87 (précis)
```

### 2.7 Documents, Emails & Logs

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `phpoffice/phpword` | ^1.2 | Conversion Word→PDF | Module 7 |
| `tecnickcom/tcpdf` | ^6.6 | Génération PDF | Module 7 |
| `phpmailer/phpmailer` | ^6.9 | Envoi emails | Tous modules (notifications) |
| `league/csv` | ^9.0 | Import/Export CSV | Modules 2, 8 |
| `monolog/monolog` | ^3.5 | Journalisation | Core (audit) |
| `whoops/monolog` | N/A | Note: probablement `filp/whoops` | Dev (erreurs) |

**Intégration TCPDF** :
```php
use TCPDF;

class PdfGenerator
{
    public function generateRecu(array $data): string
    {
        $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8');
        $pdf->SetCreator('MIAGE Platform');
        $pdf->SetAuthor('Administration');
        $pdf->SetTitle('Reçu de paiement');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage();
        
        // Génération du contenu
        $html = $this->twig->render('pdf/recu.html.twig', $data);
        $pdf->writeHTML($html, true, false, true, false, '');
        
        return $pdf->Output('', 'S'); // Retourne le contenu
    }
}
```

**Intégration PHPMailer** :
```php
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    public function send(string $to, string $subject, string $body, array $attachments = []): void
    {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = $this->settings->get('smtp_host');
        $mail->SMTPAuth = true;
        $mail->Username = $this->settings->get('smtp_username');
        $mail->Password = $this->encryption->decrypt($this->settings->get('smtp_password'));
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $this->settings->get('smtp_port');
        
        $mail->setFrom($this->settings->get('email_from_address'), $this->settings->get('email_from_name'));
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        foreach ($attachments as $path => $name) {
            $mail->addAttachment($path, $name);
        }
        
        $mail->send();
    }
}
```

**Intégration Monolog** :
```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

$logger = new Logger('audit');
$logger->pushHandler(new RotatingFileHandler(
    '/storage/logs/audit.log',
    30, // garder 30 jours
    Logger::INFO
));

// Utilisation
$logger->info('user.login', [
    'user_id' => $user->getId(),
    'ip' => $request->getClientIp(),
    'user_agent' => $request->headers->get('User-Agent'),
]);
```

### 2.8 Interface & UX

| Bibliothèque | Version | Rôle | Modules utilisant |
|--------------|---------|------|-------------------|
| `jenssegers/agent` | ^2.6 | Détection appareil | Core (responsive) |
| `white-october/pagerfanta` | ^4.0 | Pagination | Tous modules (listes) |

**Intégration Pagerfanta** :
```php
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;

$queryBuilder = $entityManager->createQueryBuilder()
    ->select('e')
    ->from(Etudiant::class, 'e')
    ->orderBy('e.nom', 'ASC');

$adapter = new QueryAdapter($queryBuilder);
$pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
    $adapter,
    $currentPage,
    25
);

// Dans le template
foreach ($pagerfanta as $etudiant) {
    // Affichage
}

// Navigation
$pagerfanta->haveToPaginate(); // true si > 1 page
$pagerfanta->getCurrentPage();
$pagerfanta->getNbPages();
```

---

## 3. Architecture des services

### 3.1 Conteneur de dépendances

```php
// config/container.php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

// Configuration
$container->register('settings', SettingsService::class)
    ->addArgument(new Reference('cache'))
    ->addArgument(new Reference('encryption'));

// Base de données
$container->register('entity_manager', EntityManager::class)
    ->setFactory([EntityManagerFactory::class, 'create']);

// Services métier
$container->register('etudiant_service', EtudiantService::class)
    ->addArgument(new Reference('entity_manager'))
    ->addArgument(new Reference('event_dispatcher'))
    ->addArgument(new Reference('logger'));

$container->register('candidature_service', CandidatureService::class)
    ->addArgument(new Reference('entity_manager'))
    ->addArgument(new Reference('workflow_registry'))
    ->addArgument(new Reference('email_service'))
    ->addArgument(new Reference('event_dispatcher'));

// etc.
```

### 3.2 Registry des workflows

```php
class WorkflowRegistry
{
    private array $workflows = [];
    
    public function get(string $name): Workflow
    {
        if (!isset($this->workflows[$name])) {
            $this->workflows[$name] = $this->create($name);
        }
        return $this->workflows[$name];
    }
    
    private function create(string $name): Workflow
    {
        return match($name) {
            'candidature' => $this->createCandidatureWorkflow(),
            'rapport' => $this->createRapportWorkflow(),
            'commission' => $this->createCommissionWorkflow(),
            'soutenance' => $this->createSoutenanceWorkflow(),
            default => throw new \InvalidArgumentException("Unknown workflow: $name"),
        };
    }
}
```

---

## 4. Configuration des environnements

### 4.1 Variables d'environnement (.env)

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_SECRET=your-32-char-secret-here

# Database
DB_HOST=localhost
DB_NAME=miage_platform
DB_USER=miage_user
DB_PASS=secure_password
DB_CHARSET=utf8mb4

# Security
JWT_SECRET=your-jwt-secret-minimum-32-chars
ENCRYPTION_KEY=def000...generated-key

# Email
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=noreply@miage.edu
SMTP_PASSWORD=encrypted_password
SMTP_ENCRYPTION=tls
EMAIL_FROM=noreply@miage.edu
EMAIL_FROM_NAME="Plateforme MIAGE"

# Paths
STORAGE_PATH=/var/www/html/storage
LOGS_PATH=/var/www/html/storage/logs
CACHE_PATH=/var/www/html/storage/cache
```

### 4.2 Bootstrap de l'application

```php
// public/index.php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configuration d'erreurs selon environnement
if ($_ENV['APP_DEBUG'] === 'true') {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

// Bootstrap du conteneur
$container = require __DIR__ . '/../config/container.php';

// Création de la requête
$request = $container->get('request_factory')->createFromGlobals();

// Pipeline de middlewares
$app = $container->get('app');
$response = $app->handle($request);

// Envoi de la réponse
$container->get('response_emitter')->emit($response);
```

---

## 5. Tableau récapitulatif d'utilisation

| Module | Bibliothèques principales |
|--------|---------------------------|
| **Core** | fast-route, middleland, nyholm/psr7, symfony/http-foundation, doctrine/orm, symfony/di, monolog |
| **Module 1** | security-core, security-http, password-hasher, security-csrf, jwt, otphp, rate-limiter, php-encryption |
| **Module 2** | doctrine/orm, respect/validation, egulias/email-validator, carbon, brick/math, pagerfanta, csv, tcpdf, phpmailer |
| **Module 3** | workflow, event-dispatcher, htmlpurifier, phpmailer, carbon |
| **Module 4** | workflow, htmlpurifier, phpword, tcpdf, event-dispatcher, phpmailer |
| **Module 5** | workflow, event-dispatcher, expression-language, phpmailer, tcpdf, carbon |
| **Module 6** | workflow, brick/math, carbon, event-dispatcher, phpmailer, tcpdf |
| **Module 7** | tcpdf, phpword, csv, string |
| **Module 8** | options-resolver, expression-language, php-encryption, phinx |
