<?php

declare(strict_types=1);

/**
 * Bootstrap de l'application CheckMaster
 * 
 * Initialise l'autoloader, les configurations et les services essentiels.
 */

// Définir le chemin racine de l'application
define('BASE_PATH', dirname(__DIR__, 2));
define('APP_PATH', BASE_PATH . '/app');
define('SRC_PATH', BASE_PATH . '/src');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Charger l'autoloader Composer
require_once BASE_PATH . '/vendor/autoload.php';

// Configurer le rapport d'erreurs selon l'environnement
$environment = getenv('APP_ENV') ?: 'production';
define('APP_ENV', $environment);
define('APP_DEBUG', $environment === 'development' || $environment === 'testing');

if ($environment === 'development' || $environment === 'testing') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Configurer le fuseau horaire
date_default_timezone_set('Africa/Abidjan');

// Configurer la session de manière sécurisée
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli') {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '3600'); // 1 heure
    
    // Créer le dossier sessions s'il n'existe pas
    $sessionsPath = STORAGE_PATH . '/sessions';
    if (!is_dir($sessionsPath)) {
        mkdir($sessionsPath, 0755, true);
    }
    session_save_path($sessionsPath);
    session_start();
}

// Charger la configuration de la base de données
$dbConfigPath = APP_PATH . '/config/database.php';
if (file_exists($dbConfigPath)) {
    $dbConfig = require $dbConfigPath;

    // Enregistrer la configuration dans un conteneur ou une variable globale
    $GLOBALS['db_config'] = $dbConfig;

    // Initialiser la connexion PDO pour l'ORM
    if (isset($dbConfig['connections']['mysql'])) {
        $mysqlConfig = $dbConfig['connections']['mysql'];
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $mysqlConfig['host'],
                $mysqlConfig['port'],
                $mysqlConfig['database'],
                $mysqlConfig['charset']
            );
            $pdo = new PDO($dsn, $mysqlConfig['username'], $mysqlConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            \App\Orm\Model::setConnection($pdo);
            $GLOBALS['pdo'] = $pdo;
        } catch (PDOException $e) {
            // En développement, afficher l'erreur
            if (defined('APP_DEBUG') && APP_DEBUG) {
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
            // En production, logger silencieusement
            error_log('Database connection failed: ' . $e->getMessage());
        }
    }
}

// Initialiser le logger si disponible
if (class_exists(\Monolog\Logger::class)) {
    $logger = new \Monolog\Logger('checkmaster');
    $handler = new \Monolog\Handler\RotatingFileHandler(
        STORAGE_PATH . '/logs/app.log',
        7, // Garder 7 jours de logs
        \Monolog\Logger::DEBUG
    );
    $handler->setFormatter(new \Monolog\Formatter\LineFormatter(
        "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
        "Y-m-d H:i:s"
    ));
    $logger->pushHandler($handler);

    $GLOBALS['logger'] = $logger;
}

// Fonction helper pour accéder au logger
if (!function_exists('logger')) {
    function logger(): ?\Monolog\Logger
    {
        return $GLOBALS['logger'] ?? null;
    }
}
