# 📦 CheckMaster Framework Core (src/)

## Vue d'Ensemble

Le dossier `src/` contient le **framework core PHP 8.0+** de CheckMaster avec **15,173 lignes** de code de qualité production.

### Architecture

```
src/
├── Container.php              # IoC Container pour injection de dépendances
├── Kernel.php                 # Application kernel - bootstrap système
├── Router.php                 # Routeur HTTP avec support REST
│
├── Database/                  # Couche accès base de données
│   ├── QueryBuilder.php       # ⭐ Query Builder SQL avancé (1127L)
│   ├── ConnectionPool.php     # ⭐ Pool de connexions DB (559L)
│   └── DB.php                 # ⭐ Façade DB avec transactions (405L)
│
├── Excel/                     # Manipulation fichiers Excel
│   └── ExcelHandler.php       # ⭐ PhpSpreadsheet wrapper (563L)
│
├── Exceptions/                # Exceptions typées (hiérarchie AppException)
│   ├── AppException.php
│   ├── DatabaseException.php
│   ├── ValidationException.php
│   ├── NotFoundException.php
│   ├── ForbiddenException.php
│   └── ... (23 exceptions au total)
│
├── Http/                      # Gestion HTTP
│   ├── Request.php            # Objet requête HTTP
│   ├── Response.php           # Réponse HTTP générique
│   ├── JsonResponse.php       # Réponse JSON structurée
│   ├── ApiResponse.php        # Réponse API REST
│   ├── PaginatedResponse.php  # Réponse avec pagination
│   ├── DownloadResponse.php   # Forcer téléchargement
│   └── StreamResponse.php     # Streaming de contenu
│
├── Mail/                      # Système emailing
│   └── MailQueue.php          # ⭐ Queue emails asynchrone (444L)
│
├── Middleware/                # Middlewares HTTP
│   ├── MiddlewareStack.php    # ⭐ Gestionnaire middlewares (48L)
│   ├── AuthMiddleware.php     # ⭐ Vérification authentification (34L)
│   └── CorsMiddleware.php     # ⭐ CORS headers (103L)
│
├── Pdf/                       # Génération PDF
│   └── PdfGenerator.php       # ⭐ TCPDF/mPDF wrapper (346L)
│
├── Queue/                     # Système de files d'attente
│   └── JobQueue.php           # ⭐ Jobs asynchrones (379L)
│
├── RateLimit/                 # Contrôle taux requêtes
│   └── RateLimiter.php        # ⭐ Token bucket + sliding window (496L)
│
├── Security/                  # Utilitaires sécurité
│   └── SecurityUtils.php      # ⭐ XSS, CSRF, encryption (517L)
│
├── Session/                   # Gestion sessions
│   └── SessionManager.php     # ⭐ Sessions DB sécurisées (512L)
│
├── Support/                   # Helpers et utilitaires
│   ├── Arr.php                # Manipulation tableaux
│   ├── Str.php                # Manipulation chaînes
│   ├── Auth.php               # Helper authentification
│   ├── CSRF.php               # Protection CSRF
│   ├── Collection.php         # Collection d'objets
│   ├── Pagination.php         # Helper pagination
│   ├── ConfigManager.php      # Gestionnaire configuration
│   ├── CacheFactory.php       # Factory cache (Redis/File)
│   ├── LoggerFactory.php      # Factory logger (Monolog)
│   ├── ValidatorFactory.php   # Factory Symfony Validator
│   ├── EventDispatcher.php    # Event dispatcher
│   ├── HashidManager.php      # Hashids pour IDs
│   ├── AuditLogger.php        # Logger audit trail
│   └── helpers.php            # Fonctions globales
│
└── Upload/                    # Gestion uploads
    └── UploadHandler.php      # ⭐ Upload sécurisé + chunked (723L)
```

⭐ = **Nouveaux fichiers créés (7,628 lignes)**

---

## 🎯 Composants Principaux

### 1. Database Layer (2,091 lignes)

#### QueryBuilder.php - Query Builder SQL Avancé
Construction de requêtes SQL type-safe avec bindings automatiques.

**Fonctionnalités:**
- SELECT avec colonnes, DISTINCT, agrégations
- WHERE complexes avec groupes imbriqués
- JOIN (INNER, LEFT, RIGHT, CROSS)
- GROUP BY / HAVING
- ORDER BY multi-colonnes
- LIMIT / OFFSET avec pagination
- UNION / UNION ALL
- CRUD complet (INSERT, UPDATE, DELETE)
- Increment / Decrement atomiques

**Exemple:**
```php
$users = DB::table('utilisateurs')
    ->select('nom', 'email', 'created_at')
    ->where('statut', 'Actif')
    ->where(function($q) {
        $q->where('role', 'admin')
          ->orWhere('permissions', 'LIKE', '%manage_%');
    })
    ->join('groupes', 'utilisateurs.groupe_id', '=', 'groupes.id')
    ->orderBy('nom', 'ASC')
    ->limit(50)
    ->get();
```

#### ConnectionPool.php - Pool de Connexions
Gestion optimale des connexions PDO avec réutilisation.

**Fonctionnalités:**
- Pool min/max connexions configurables
- Connexions read/write séparées (master/replica)
- Health checks automatiques
- Reconnexion sur échec
- Statistiques d'utilisation
- Timeouts (connexion, inactivité, lifetime)

#### DB.php - Façade Database
Interface simplifiée pour accès DB global.

**Fonctionnalités:**
- Transactions imbriquées avec savepoints
- Helper methods (select, insert, update, delete, scalar)
- Integration transparente avec ConnectionPool
- Transaction wrapper avec rollback auto

**Exemple:**
```php
DB::transaction(function() {
    $userId = DB::table('users')->insert(['name' => 'John']);
    DB::table('profiles')->insert(['user_id' => $userId]);
    return $userId;
});
```

---

### 2. Upload Handler (723 lignes)

Gestion sécurisée des uploads de fichiers.

**Fonctionnalités:**
- Validation type MIME (via finfo)
- Validation taille avec limites
- Scan antivirus (ClamAV) optionnel
- Validation images (dimensions, ratio)
- Chunked upload pour gros fichiers
- Multi-fichiers avec batch processing
- Noms sécurisés (slug + timestamp + hash)
- Organisation automatique par date (YYYY/MM)

**Exemple:**
```php
$uploader = new UploadHandler([
    'allowed_mimes' => ['image/jpeg', 'image/png', 'application/pdf'],
    'max_file_size' => 10 * 1024 * 1024, // 10 MB
]);

$file = $uploader->upload($_FILES['document'], [
    'subfolder' => 'dossiers',
    'min_width' => 800,
    'min_height' => 600
]);
```

---

### 3. Rate Limiter (496 lignes)

Contrôle du taux de requêtes avec algorithmes multiples.

**Algorithmes:**
- **Token Bucket** : classique avec decay
- **Sliding Window** : précision microseconde
- **Fixed Window** : fenêtres temporelles fixes

**Fonctionnalités:**
- Limitation par IP, user_id, action, clé custom
- Whitelist / Blacklist d'IPs
- Headers HTTP (X-RateLimit-*)
- Persistence Redis
- Protection brute-force login (5/15min)

**Exemple:**
```php
try {
    $limiter->forIp($_SERVER['REMOTE_ADDR'], 60, 1); // 60 req/min
    // Traiter requête
} catch (TooManyRequestsException $e) {
    http_response_code(429);
    header('Retry-After: ' . $e->getRetryAfter());
}
```

---

### 4. Job Queue (379 lignes)

Système de files d'attente pour tâches asynchrones.

**Fonctionnalités:**
- Files multiples avec priorités (1-10)
- Retry automatique avec backoff exponentiel
- Dead Letter Queue pour jobs échoués
- Delayed jobs avec `available_at`
- Worker daemon avec signal handling
- Job chaining et batching
- Statistiques complètes

**Exemple:**
```php
$queue = new JobQueue();

// Ajouter job
$jobId = $queue->push(SendEmailJob::class, [
    'to' => 'user@example.com',
    'subject' => 'Notification'
], [
    'priority' => 1,
    'delay' => 300, // 5 minutes
    'max_attempts' => 3
]);

// Worker (CLI)
$queue->daemon('default', 5); // Queue 'default', sleep 5s
```

---

### 5. Mail Queue (444 lignes)

Queue d'envoi d'emails asynchrone avec PHPMailer.

**Fonctionnalités:**
- Queue avec priorités
- PHPMailer integration (SMTP sécurisé)
- Templates HTML + alternative texte
- Multi-destinataires (TO, CC, BCC)
- Pièces jointes multiples
- Headers personnalisés
- Retry avec backoff (3 tentatives)
- Worker daemon

**Exemple:**
```php
$mailQueue = new MailQueue();

$emailId = $mailQueue->queue([
    'to' => 'user@example.com',
    'subject' => 'Notification',
    'body_html' => '<p>Votre dossier est validé.</p>',
], [
    'priority' => 1,
    'attachments' => ['/path/to/document.pdf'],
    'delay' => 0
]);
```

---

### 6. Session Manager (512 lignes)

Gestion sécurisée des sessions avec stockage DB.

**Fonctionnalités:**
- SessionHandlerInterface (stockage DB)
- Device fingerprinting (anti-hijacking)
- Rotation automatique ID (5 min)
- Timeout inactivité (30 min)
- Flash messages
- Multi-device support
- Session locking
- Cleanup automatique (GC)

**Exemple:**
```php
$session = new SessionManager();
$session->start();

// Store
$session->set('user_id', 123);

// Flash
$session->flash('success', 'Opération réussie');

// Multi-device
$sessions = $session->getUserSessions(123);
$session->terminateOtherSessions(123, $currentSessionId);
```

---

### 7. PDF Generator (346 lignes)

Génération de documents PDF avec TCPDF/mPDF.

**Fonctionnalités:**
- **TCPDF** : documents simples et rapides
- **mPDF** : documents complexes avec CSS3
- Templates HTML avec variables {{key}}
- Headers / Footers personnalisés
- Watermarks (texte/image)
- Métadonnées (auteur, titre, sujet)
- Compression
- Batch generation

**Exemple:**
```php
$pdf = new PdfGenerator(['engine' => 'mpdf']);

$html = '<h1>Rapport</h1><p>Contenu...</p>';

$pdfContent = $pdf->generateFromHtml($html, [
    'title' => 'Rapport Commission',
    'watermark' => 'CONFIDENTIEL',
    'footer_html' => '<div>Page {PAGENO}/{nbpg}</div>'
]);

$pdf->download($pdfContent, 'rapport.pdf');
```

---

### 8. Excel Handler (563 lignes)

Manipulation avancée de fichiers Excel.

**Fonctionnalités:**
- PhpSpreadsheet (XLSX, XLS, CSV, HTML)
- Lecture / Écriture avec headers
- Styles (polices, couleurs, bordures)
- Formules (SUM, AVG, IF, VLOOKUP)
- Multi-feuilles
- Auto-sizing colonnes
- Filtres automatiques
- Freeze panes
- Merge cells
- Validation données
- Métadonnées

**Exemple:**
```php
$excel = new ExcelHandler();

// Export
$data = [
    ['Nom', 'Email', 'Score'],
    ['Dupont', 'dupont@example.com', 85]
];

$excel->create()
    ->writeArray($data, 'A1', true)
    ->setAutoFilter()
    ->freezePane('A2')
    ->export('/path/to/export.xlsx');

// Import
$imported = $excel->load('/path/to/file.xlsx')->readSheet();
```

---

### 9. Security Utils (517 lignes)

Utilitaires de sécurité complets.

**Fonctionnalités:**
- **XSS Prevention** : escapeHtml(), escapeJs(), escapeUrl()
- **SQL Injection Detection** : patterns UNION, SELECT, DROP
- **Input Sanitization** : email, URL, int, float, bool
- **Password Security** : bcrypt (cost 12), verify, needs_rehash
- **Token Generation** : random_bytes sécurisé
- **CSRF Protection** : génération + validation timing-safe
- **Password Strength** : validation complexité
- **File Integrity** : hash SHA256/512
- **Encryption** : AES-256-CBC
- **HMAC Signatures** : génération + vérification

**Exemple:**
```php
// XSS
echo SecurityUtils::escapeHtml($userInput);

// Password
$hash = SecurityUtils::hashPassword($password);
if (SecurityUtils::verifyPassword($password, $hash)) {
    // OK
}

// CSRF
$token = SecurityUtils::generateCsrfToken();
if (SecurityUtils::validateCsrfToken($_POST['csrf_token'])) {
    // Valide
}

// Encryption
$encrypted = SecurityUtils::encrypt($data, $key);
$decrypted = SecurityUtils::decrypt($encrypted, $key);
```

---

### 10. Middleware Stack (185 lignes)

Gestionnaire de middlewares HTTP.

**Middlewares:**
- **MiddlewareStack** : chaînage et exécution
- **AuthMiddleware** : vérification authentification
- **CorsMiddleware** : headers CORS complets

**Exemple:**
```php
$stack = new MiddlewareStack();
$stack->add(new CorsMiddleware())
      ->add(new AuthMiddleware());

$response = $stack->handle($request, function($req) {
    // Controller action
    return new JsonResponse(['status' => 'ok']);
});
```

---

## 🔒 Standards de Sécurité

### Appliqués Partout
1. **Prepared Statements** : 100% des requêtes DB
2. **Type Hints** : 100% des paramètres et retours
3. **Strict Types** : `declare(strict_types=1)` obligatoire
4. **Exceptions Typées** : hiérarchie AppException
5. **Validation Avant Traitement** : Symfony Validator
6. **XSS Prevention** : échappement automatique
7. **CSRF Protection** : tokens avec validation
8. **Password Hashing** : bcrypt cost 12
9. **Session Security** : fingerprinting + rotation
10. **Rate Limiting** : protection brute-force

---

## 📚 Dépendances Requises

### Composer
```json
{
    "require": {
        "php": "^8.0",
        "ext-pdo": "*",
        "ext-mbstring": "*",
        "ext-openssl": "*",
        "ext-fileinfo": "*",
        "phpmailer/phpmailer": "^6.8",
        "phpoffice/phpspreadsheet": "^1.28",
        "tecnickcom/tcpdf": "^6.6",
        "mpdf/mpdf": "^8.1",
        "predis/predis": "^2.2"
    }
}
```

### Extensions PHP
- **pdo_mysql** : Base de données
- **mbstring** : UTF-8
- **gd** ou **imagick** : Images
- **openssl** : Chiffrement
- **fileinfo** : MIME types
- **json** : JSON

---

## 🧪 Tests

### Structure Tests
```
tests/
├── Unit/
│   ├── Database/
│   │   ├── QueryBuilderTest.php
│   │   ├── ConnectionPoolTest.php
│   │   └── DBTest.php
│   ├── Security/
│   │   └── SecurityUtilsTest.php
│   └── ...
│
└── Integration/
    ├── UploadHandlerTest.php
    ├── MailQueueTest.php
    └── ...
```

### Lancer les Tests
```bash
# Tous les tests
composer test

# Avec couverture
composer test -- --coverage-html coverage/
```

---

## 📖 Documentation API

Toutes les méthodes publiques sont documentées avec PHPDoc:

```php
/**
 * Description courte
 *
 * Description longue optionnelle avec détails
 *
 * @param Type $param Description du paramètre
 * @param Type $autre Autre paramètre
 * @return Type Description du retour
 * @throws ExceptionType Quand levée
 */
public function method(Type $param, Type $autre): Type
{
    // Implémentation
}
```

---

## 🚀 Performance

### Optimisations Appliquées
- **Connection Pool** : réutilisation connexions DB
- **Query Builder** : prepared statements optimisés
- **Cache** : Redis pour sessions, rate limiting, stats
- **Lazy Loading** : chargement à la demande
- **Pagination** : requêtes LIMIT/OFFSET
- **Compression** : PDF avec compression activée

### Benchmarks Attendus
- **Query Builder** : ~1ms pour requêtes simples
- **Connection Pool** : ~0.5ms overhead vs connexion directe
- **Rate Limiter** : ~2ms avec Redis
- **Session Manager** : ~3ms read/write DB

---

## 🔧 Configuration

### Fichiers Config Suggérés
- `app/config/database.php` : connexions DB + pool
- `app/config/queue.php` : configuration jobs
- `app/config/upload.php` : limites et validations
- `app/config/rate_limiting.php` : limites par type
- `app/config/mail.php` : SMTP + queue

---

## 📝 Changelog

### v1.0.0 (2025-01-16)
- ✨ **QueryBuilder** : Query Builder SQL complet
- ✨ **ConnectionPool** : Pool de connexions DB
- ✨ **DB** : Façade DB avec transactions
- ✨ **UploadHandler** : Uploads sécurisés + chunked
- ✨ **RateLimiter** : Contrôle taux requêtes
- ✨ **JobQueue** : Jobs asynchrones
- ✨ **MailQueue** : Queue emails
- ✨ **SessionManager** : Sessions DB sécurisées
- ✨ **PdfGenerator** : Génération PDF
- ✨ **ExcelHandler** : Manipulation Excel
- ✨ **SecurityUtils** : Utilitaires sécurité
- ✨ **Middleware** : Stack + Auth + Cors

---

## 🤝 Contribution

### Standards Code
1. PSR-12 strict (PHP-CS-Fixer)
2. PHP 8.0+ avec types stricts
3. PHPDoc complet
4. Tests unitaires obligatoires
5. Pas de breaking changes sans migration

### Pull Requests
1. Créer branche feature/nom-feature
2. Implémenter avec tests
3. Lancer `composer fix` et `composer stan`
4. Créer PR avec description détaillée

---

**Framework Core CheckMaster - Production Ready 🚀**

*Documentation générée le 16 janvier 2025*
